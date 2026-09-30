CREATE TABLE IF NOT EXISTS media_uploads (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  scope VARCHAR(20) NOT NULL,
  filename VARCHAR(190) NOT NULL,
  mime_type VARCHAR(80) NOT NULL,
  size_bytes INT UNSIGNED NOT NULL DEFAULT 0,
  data MEDIUMBLOB NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_media_scope_created (scope, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings(`key`,`value`) VALUES
('friday_notice_tr','Cuma günleri öğleden sonra açığız.'),
('friday_notice_en','On Fridays, we open in the afternoon.'),
('instagram','https://www.instagram.com/kebapzaderestaurant/')
ON DUPLICATE KEY UPDATE `value` = IF(TRIM(COALESCE(`value`,''))='', VALUES(`value`), `value`);
