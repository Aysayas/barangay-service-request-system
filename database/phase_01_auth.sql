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
