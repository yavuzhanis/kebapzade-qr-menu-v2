CREATE TABLE IF NOT EXISTS reservations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  guest_name VARCHAR(160) NOT NULL,
  phone VARCHAR(60) NOT NULL,
  email VARCHAR(190) NULL,
  reservation_date DATE NOT NULL,
  reservation_time TIME NOT NULL,
  guest_count TINYINT UNSIGNED NOT NULL DEFAULT 2,
  note TEXT NULL,
  status ENUM('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  source VARCHAR(40) NOT NULL DEFAULT 'website',
  language CHAR(2) NOT NULL DEFAULT 'tr',
  admin_note TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_reservation_date (reservation_date, reservation_time),
  INDEX idx_reservation_status (status, reservation_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
