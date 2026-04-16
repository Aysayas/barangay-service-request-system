USE barangay_service_request_system;

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
