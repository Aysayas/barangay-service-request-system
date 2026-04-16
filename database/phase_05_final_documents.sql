USE barangay_service_request_system;

CREATE TABLE IF NOT EXISTS request_final_documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id INT UNSIGNED NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    file_type VARCHAR(120) NOT NULL,
    uploaded_by INT UNSIGNED NOT NULL,
    uploaded_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    UNIQUE KEY uq_request_final_documents_request (request_id),
    INDEX idx_request_final_documents_uploaded_by (uploaded_by),
    CONSTRAINT fk_request_final_documents_request
        FOREIGN KEY (request_id) REFERENCES service_requests(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_request_final_documents_uploaded_by
        FOREIGN KEY (uploaded_by) REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
