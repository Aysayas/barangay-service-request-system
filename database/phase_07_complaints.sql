USE barangay_service_request_system;

CREATE TABLE IF NOT EXISTS complaints (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference_no VARCHAR(40) NOT NULL UNIQUE,
    user_id INT UNSIGNED NOT NULL,
    complainant_name VARCHAR(160) NOT NULL,
    complainant_email VARCHAR(150) NOT NULL,
    complainant_contact VARCHAR(30) NULL,
    subject VARCHAR(160) NOT NULL,
    category VARCHAR(80) NOT NULL,
    description TEXT NOT NULL,
    incident_date DATE NULL,
    location VARCHAR(255) NOT NULL,
    respondent_name VARCHAR(160) NULL,
    status ENUM(
        'submitted',
        'under_review',
        'needs_info',
        'investigating',
        'resolved',
        'closed',
        'dismissed'
    ) NOT NULL DEFAULT 'submitted',
    priority ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium',
    staff_notes TEXT NULL,
    resolution_notes TEXT NULL,
    assigned_to INT UNSIGNED NULL,
    is_anonymous TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_complaints_user (user_id),
    INDEX idx_complaints_status (status),
    INDEX idx_complaints_priority (priority),
    INDEX idx_complaints_category (category),
    INDEX idx_complaints_assigned_to (assigned_to),
    CONSTRAINT fk_complaints_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_complaints_assigned_to
        FOREIGN KEY (assigned_to) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS complaint_attachments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    complaint_id INT UNSIGNED NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    file_type VARCHAR(120) NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_complaint_attachments_complaint (complaint_id),
    CONSTRAINT fk_complaint_attachments_complaint
        FOREIGN KEY (complaint_id) REFERENCES complaints(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
