USE barangay_service_request_system;

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
