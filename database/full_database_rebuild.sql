CREATE DATABASE IF NOT EXISTS barangay_service_request_system
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE barangay_service_request_system;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('resident', 'staff', 'admin') NOT NULL DEFAULT 'resident',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    contact_number VARCHAR(30) NULL,
    address VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_users_role (role),
    INDEX idx_users_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
    last_processed_by INT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_service_requests_user (user_id),
    INDEX idx_service_requests_service (service_id),
    INDEX idx_service_requests_status (status),
    INDEX idx_service_requests_last_processed_by (last_processed_by),
    CONSTRAINT fk_service_requests_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_service_requests_service
        FOREIGN KEY (service_id) REFERENCES services(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_service_requests_last_processed_by
        FOREIGN KEY (last_processed_by) REFERENCES users(id)
        ON DELETE SET NULL
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

CREATE TABLE IF NOT EXISTS announcements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(160) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    body TEXT NOT NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 0,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_announcements_published (is_published),
    INDEX idx_announcements_published_at (published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id INT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_method VARCHAR(40) NULL,
    reference_number VARCHAR(120) NULL,
    proof_original_name VARCHAR(255) NULL,
    proof_stored_name VARCHAR(255) NULL,
    proof_file_path VARCHAR(255) NULL,
    proof_file_size INT UNSIGNED NULL,
    proof_file_type VARCHAR(120) NULL,
    payment_status ENUM(
        'pending_payment',
        'payment_submitted',
        'payment_verified',
        'payment_rejected'
    ) NOT NULL DEFAULT 'pending_payment',
    submitted_by INT UNSIGNED NULL,
    verified_by INT UNSIGNED NULL,
    submitted_at DATETIME NULL,
    verified_at DATETIME NULL,
    remarks TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    UNIQUE KEY uq_payments_request (request_id),
    INDEX idx_payments_status (payment_status),
    INDEX idx_payments_submitted_by (submitted_by),
    INDEX idx_payments_verified_by (verified_by),
    CONSTRAINT fk_payments_request
        FOREIGN KEY (request_id) REFERENCES service_requests(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_payments_submitted_by
        FOREIGN KEY (submitted_by) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT fk_payments_verified_by
        FOREIGN KEY (verified_by) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (
    first_name,
    last_name,
    email,
    password,
    role,
    status,
    contact_number,
    address,
    created_at,
    updated_at
) VALUES
(
    'System',
    'Admin',
    'admin@barangay.local',
    '$2y$10$E4CBM8Ej7yNCMol5ZcMShOjja29APDQwFDZimeLp57czAy7CGfYy6',
    'admin',
    'active',
    NULL,
    'Barangay Hall',
    NOW(),
    NOW()
),
(
    'Barangay',
    'Staff',
    'staff@barangay.local',
    '$2y$10$E4CBM8Ej7yNCMol5ZcMShOjja29APDQwFDZimeLp57czAy7CGfYy6',
    'staff',
    'active',
    NULL,
    'Barangay Hall',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    first_name = VALUES(first_name),
    last_name = VALUES(last_name),
    role = VALUES(role),
    status = VALUES(status),
    updated_at = NOW();

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

INSERT INTO announcements (
    title,
    slug,
    body,
    is_published,
    published_at,
    created_at,
    updated_at
) VALUES
(
    'Welcome to the Barangay Service Request System',
    'welcome-to-the-barangay-service-request-system',
    'Residents may now register, submit service requests, and monitor status updates online.',
    1,
    NOW(),
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    body = VALUES(body),
    is_published = VALUES(is_published),
    published_at = VALUES(published_at),
    updated_at = NOW();

INSERT INTO payments (
    request_id,
    amount,
    payment_status,
    created_at,
    updated_at
)
SELECT
    sr.id,
    s.fee,
    'pending_payment',
    NOW(),
    NOW()
FROM service_requests sr
INNER JOIN services s ON s.id = sr.service_id
WHERE s.requires_payment = 1
  AND NOT EXISTS (
      SELECT 1
      FROM payments p
      WHERE p.request_id = sr.id
  );
