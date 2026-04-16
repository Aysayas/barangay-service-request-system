USE barangay_service_request_system;

SET @column_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'service_requests'
      AND COLUMN_NAME = 'last_processed_by'
);

SET @sql := IF(
    @column_exists = 0,
    'ALTER TABLE service_requests ADD COLUMN last_processed_by INT UNSIGNED NULL AFTER final_document_path',
    'SELECT "last_processed_by already exists"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'service_requests'
      AND INDEX_NAME = 'idx_service_requests_last_processed_by'
);

SET @sql := IF(
    @index_exists = 0,
    'ALTER TABLE service_requests ADD INDEX idx_service_requests_last_processed_by (last_processed_by)',
    'SELECT "idx_service_requests_last_processed_by already exists"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(80) NOT NULL,
    target_type VARCHAR(80) NOT NULL,
    target_id INT UNSIGNED NOT NULL,
    description TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_audit_logs_user (user_id),
    INDEX idx_audit_logs_target (target_type, target_id),
    INDEX idx_audit_logs_action (action),
    CONSTRAINT fk_audit_logs_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
