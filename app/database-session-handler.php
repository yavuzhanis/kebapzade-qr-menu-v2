<?php
declare(strict_types=1);

final class DatabaseSessionHandler implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface
{
    public function __construct(
        private PDO $pdo,
        private int $ttl = 7200,
        private string $table = 'app_sessions',
        private bool $autoMigrate = true,
    ) {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $this->table)) {
            throw new InvalidArgumentException('Geçersiz session tablo adı.');
        }
    }

    public function open(string $path, string $name): bool { return true; }
    public function close(): bool { return true; }

    public function read(string $id): string|false
    {
        return $this->withTable(function () use ($id): string {
            $q = $this->pdo->prepare("SELECT payload FROM `{$this->table}` WHERE id=? AND expires_at>=? LIMIT 1");
            $q->execute([$id, time()]);
            $value = $q->fetchColumn();
            return $value === false ? '' : (string)$value;
        }, '');
    }

    public function write(string $id, string $data): bool
    {
        return $this->withTable(function () use ($id, $data): bool {
            $now = time();
            $q = $this->pdo->prepare(
                "INSERT INTO `{$this->table}` (id,payload,last_activity,expires_at) VALUES (?,?,?,?) " .
                "ON DUPLICATE KEY UPDATE payload=VALUES(payload),last_activity=VALUES(last_activity),expires_at=VALUES(expires_at)"
            );
            return $q->execute([$id, $data, $now, $now + $this->ttl]);
        }, false);
    }

    public function destroy(string $id): bool
    {
        return $this->withTable(function () use ($id): bool {
            $q = $this->pdo->prepare("DELETE FROM `{$this->table}` WHERE id=?");
            return $q->execute([$id]);
        }, true);
    }

    public function gc(int $max_lifetime): int|false
    {
        return $this->withTable(function (): int {
            $q = $this->pdo->prepare("DELETE FROM `{$this->table}` WHERE expires_at<?");
            $q->execute([time()]);
            return $q->rowCount();
        }, 0);
    }

    public function validateId(string $id): bool
    {
        return $this->withTable(function () use ($id): bool {
            $q = $this->pdo->prepare("SELECT 1 FROM `{$this->table}` WHERE id=? AND expires_at>=? LIMIT 1");
            $q->execute([$id, time()]);
            return (bool)$q->fetchColumn();
        }, false);
    }

    public function updateTimestamp(string $id, string $data): bool
    {
        return $this->withTable(function () use ($id, $data): bool {
            $now = time();
            $q = $this->pdo->prepare("UPDATE `{$this->table}` SET last_activity=?,expires_at=? WHERE id=?");
            $q->execute([$now, $now + $this->ttl, $id]);
            return $q->rowCount() > 0 ? true : $this->write($id, $data);
        }, false);
    }

    private function withTable(callable $operation, mixed $fallback): mixed
    {
        try {
            return $operation();
        } catch (PDOException $e) {
            if (!$this->autoMigrate || !$this->isMissingTable($e)) {
                return $fallback;
            }
            $this->ensureTable();
            try {
                return $operation();
            } catch (Throwable) {
                return $fallback;
            }
        }
    }

    private function isMissingTable(PDOException $e): bool
    {
        return $e->getCode() === '42S02' || (int)($e->errorInfo[1] ?? 0) === 1146;
    }

    private function ensureTable(): void
    {
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS `{$this->table}` (" .
            "id VARCHAR(128) NOT NULL PRIMARY KEY," .
            "payload MEDIUMBLOB NOT NULL," .
            "last_activity BIGINT UNSIGNED NOT NULL," .
            "expires_at BIGINT UNSIGNED NOT NULL," .
            "INDEX idx_session_expiry (expires_at)" .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }
}
