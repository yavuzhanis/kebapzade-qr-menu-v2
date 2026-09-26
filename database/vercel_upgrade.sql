CREATE TABLE IF NOT EXISTS app_sessions (
  id VARCHAR(128) NOT NULL PRIMARY KEY,
  payload MEDIUMBLOB NOT NULL,
  last_activity BIGINT UNSIGNED NOT NULL,
  expires_at BIGINT UNSIGNED NOT NULL,
  INDEX idx_session_expiry (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
