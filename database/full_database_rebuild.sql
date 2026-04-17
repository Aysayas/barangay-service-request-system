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

CREATE TABLE IF NOT EXISTS community_posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    category ENUM('announcement', 'event', 'program', 'advisory', 'resource') NOT NULL DEFAULT 'announcement',
    excerpt VARCHAR(255) NULL,
    content TEXT NOT NULL,
    image_path VARCHAR(255) NULL,
    event_date DATE NULL,
    event_time TIME NULL,
    venue VARCHAR(160) NULL,
    organizer VARCHAR(160) NULL,
    resource_link VARCHAR(255) NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    is_published TINYINT(1) NOT NULL DEFAULT 0,
    published_at DATETIME NULL,
    created_by INT UNSIGNED NULL,
    updated_by INT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_community_posts_category (category),
    INDEX idx_community_posts_published (is_published),
    INDEX idx_community_posts_published_at (published_at),
    INDEX idx_community_posts_featured (is_featured),
    INDEX idx_community_posts_event_date (event_date),
    INDEX idx_community_posts_created_by (created_by),
    INDEX idx_community_posts_updated_by (updated_by),
    CONSTRAINT fk_community_posts_created_by
        FOREIGN KEY (created_by) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT fk_community_posts_updated_by
        FOREIGN KEY (updated_by) REFERENCES users(id)
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
    'Welcome to eBarangayHub',
    'welcome-to-ebarangayhub',
    'Centralized Barangay Services, Reports, and Community Access is now available for residents and barangay staff.',
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

INSERT INTO community_posts (
    title,
    slug,
    category,
    excerpt,
    content,
    event_date,
    event_time,
    venue,
    organizer,
    resource_link,
    is_featured,
    is_published,
    published_at,
    created_by,
    updated_by,
    created_at,
    updated_at
) VALUES
(
    'eBarangayHub Online Services Are Now Available',
    'online-barangay-services-are-now-available',
    'announcement',
    'Residents can now request certificates, track status updates, and submit requirements online.',
    'eBarangayHub is available for residents who need clearances, certificates, complaint tracking, community updates, and other standard barangay services. Create an account, choose a service, upload requirements, and track the progress from your resident dashboard.',
    NULL,
    NULL,
    NULL,
    'Barangay Office',
    NULL,
    1,
    1,
    NOW(),
    (SELECT id FROM users WHERE email = 'admin@barangay.local' LIMIT 1),
    (SELECT id FROM users WHERE email = 'admin@barangay.local' LIMIT 1),
    NOW(),
    NOW()
),
(
    'Community Clean-up Drive',
    'community-clean-up-drive',
    'event',
    'Join the upcoming barangay clean-up activity with neighbors and volunteers.',
    'Residents are invited to join the community clean-up drive. Bring comfortable clothes, water, and basic cleaning tools if available. Assembly will be at the barangay hall before volunteers are assigned to nearby zones.',
    DATE_ADD(CURDATE(), INTERVAL 14 DAY),
    '08:00:00',
    'Barangay Hall Covered Court',
    'Barangay Council',
    NULL,
    1,
    1,
    NOW(),
    (SELECT id FROM users WHERE email = 'admin@barangay.local' LIMIT 1),
    (SELECT id FROM users WHERE email = 'admin@barangay.local' LIMIT 1),
    NOW(),
    NOW()
),
(
    'Water Interruption Advisory',
    'water-interruption-advisory',
    'advisory',
    'Prepare stored water ahead of the scheduled maintenance window.',
    'A temporary water interruption may affect selected streets due to maintenance work. Residents are advised to store enough water for household needs and follow barangay updates for changes to the schedule.',
    NULL,
    NULL,
    NULL,
    'Barangay Information Desk',
    NULL,
    0,
    1,
    NOW(),
    (SELECT id FROM users WHERE email = 'admin@barangay.local' LIMIT 1),
    (SELECT id FROM users WHERE email = 'admin@barangay.local' LIMIT 1),
    NOW(),
    NOW()
),
(
    'Resident Guide: Required IDs and Proof of Residency',
    'resident-guide-required-ids-and-proof-of-residency',
    'resource',
    'A quick guide to common documents needed for barangay transactions.',
    'For most barangay services, residents should prepare a valid ID and proof of residency. Acceptable proof may include a utility bill, lease document, barangay record, or other document showing the resident address.',
    NULL,
    NULL,
    'Barangay Hall',
    'Barangay Records Desk',
    NULL,
    0,
    1,
    NOW(),
    (SELECT id FROM users WHERE email = 'admin@barangay.local' LIMIT 1),
    (SELECT id FROM users WHERE email = 'admin@barangay.local' LIMIT 1),
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    category = VALUES(category),
    excerpt = VALUES(excerpt),
    content = VALUES(content),
    event_date = VALUES(event_date),
    event_time = VALUES(event_time),
    venue = VALUES(venue),
    organizer = VALUES(organizer),
    resource_link = VALUES(resource_link),
    is_featured = VALUES(is_featured),
    is_published = VALUES(is_published),
    published_at = VALUES(published_at),
    updated_by = VALUES(updated_by),
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
