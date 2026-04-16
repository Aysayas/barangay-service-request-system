USE barangay_service_request_system;

CREATE TABLE IF NOT EXISTS services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(140) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    requirements_text TEXT NOT NULL,
    fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    requires_payment TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_services_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS service_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    service_id INT UNSIGNED NOT NULL,
    reference_no VARCHAR(40) NOT NULL UNIQUE,
    purpose TEXT NOT NULL,
    remarks TEXT NULL,
    status ENUM(
        'submitted',
        'under_review',
        'needs_info',
        'approved',
        'rejected',
        'ready_for_pickup',
        'released'
    ) NOT NULL DEFAULT 'submitted',
    staff_notes TEXT NULL,
    final_document_path VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_service_requests_user (user_id),
    INDEX idx_service_requests_service (service_id),
    INDEX idx_service_requests_status (status),
    CONSTRAINT fk_service_requests_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_service_requests_service
        FOREIGN KEY (service_id) REFERENCES services(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS request_attachments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id INT UNSIGNED NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    file_type VARCHAR(120) NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_request_attachments_request (request_id),
    CONSTRAINT fk_request_attachments_request
        FOREIGN KEY (request_id) REFERENCES service_requests(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO services (
    name,
    slug,
    description,
    requirements_text,
    fee,
    requires_payment,
    is_active,
    created_at,
    updated_at
) VALUES
(
    'Barangay Clearance',
    'barangay-clearance',
    'For employment, local transactions, school, and other standard clearance needs.',
    'Valid ID, proof of residency, and any supporting document related to the purpose.',
    50.00,
    1,
    1,
    NOW(),
    NOW()
),
(
    'Certificate of Residency',
    'certificate-of-residency',
    'Certifies that the resident lives within the barangay.',
    'Valid ID and proof of address such as utility bill, lease document, or barangay record.',
    0.00,
    0,
    1,
    NOW(),
    NOW()
),
(
    'Certificate of Indigency',
    'certificate-of-indigency',
    'For residents requesting certification for assistance, scholarship, or medical support.',
    'Valid ID, proof of residency, and supporting documents for the assistance request.',
    0.00,
    0,
    1,
    NOW(),
    NOW()
),
(
    'Business Clearance',
    'business-clearance',
    'For business owners who need barangay clearance for business permits or renewal.',
    'Valid ID, proof of business address, DTI or SEC registration if available, and lease or ownership proof.',
    100.00,
    1,
    1,
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    requirements_text = VALUES(requirements_text),
    fee = VALUES(fee),
    requires_payment = VALUES(requires_payment),
    is_active = VALUES(is_active),
    updated_at = NOW();
