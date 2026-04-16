<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Payment_model extends Model
{
    protected $table = 'payments';
    protected $primary_key = 'id';

    protected $fillable = [
        'request_id',
        'amount',
        'payment_method',
        'reference_number',
        'proof_original_name',
        'proof_stored_name',
        'proof_file_path',
        'proof_file_size',
        'proof_file_type',
        'payment_status',
        'submitted_by',
        'verified_by',
        'submitted_at',
        'verified_at',
        'remarks',
    ];

    public function allowed_methods()
    {
        return [
            'gcash' => 'GCash',
            'maya' => 'Maya',
            'cash' => 'Cash',
        ];
    }

    public function allowed_statuses()
    {
        return [
            'pending_payment',
            'payment_submitted',
            'payment_verified',
            'payment_rejected',
        ];
    }

    public function review_statuses()
    {
        return [
            'payment_verified',
            'payment_rejected',
        ];
    }

    public function find_for_request($request_id)
    {
        $sql = "SELECT p.*,
                       CONCAT(submitter.first_name, ' ', submitter.last_name) AS submitted_by_name,
                       CONCAT(verifier.first_name, ' ', verifier.last_name) AS verified_by_name
                FROM payments p
                LEFT JOIN users submitter ON submitter.id = p.submitted_by
                LEFT JOIN users verifier ON verifier.id = p.verified_by
                WHERE p.request_id = ?
                LIMIT 1";

        return $this->db->raw($sql, [(int) $request_id])->fetch(PDO::FETCH_ASSOC);
    }

    public function find_for_resident($request_id, $user_id)
    {
        $sql = "SELECT p.*, sr.user_id, sr.reference_no,
                       CONCAT(submitter.first_name, ' ', submitter.last_name) AS submitted_by_name,
                       CONCAT(verifier.first_name, ' ', verifier.last_name) AS verified_by_name
                FROM payments p
                INNER JOIN service_requests sr ON sr.id = p.request_id
                LEFT JOIN users submitter ON submitter.id = p.submitted_by
                LEFT JOIN users verifier ON verifier.id = p.verified_by
                WHERE p.request_id = ? AND sr.user_id = ?
                LIMIT 1";

        return $this->db->raw($sql, [(int) $request_id, (int) $user_id])->fetch(PDO::FETCH_ASSOC);
    }

    public function find_with_request($payment_id)
    {
        $sql = "SELECT p.*, sr.reference_no, sr.user_id, sr.status AS request_status,
                       s.name AS service_name, s.requires_payment,
                       CONCAT(resident.first_name, ' ', resident.last_name) AS resident_name
                FROM payments p
                INNER JOIN service_requests sr ON sr.id = p.request_id
                INNER JOIN services s ON s.id = sr.service_id
                INNER JOIN users resident ON resident.id = sr.user_id
                WHERE p.id = ?
                LIMIT 1";

        return $this->db->raw($sql, [(int) $payment_id])->fetch(PDO::FETCH_ASSOC);
    }

    public function create_pending_for_request($request_id, $amount)
    {
        $existing = $this->find_for_request((int) $request_id);

        if (!empty($existing)) {
            return (int) $existing['id'];
        }

        return $this->insert([
            'request_id' => (int) $request_id,
            'amount' => (float) $amount,
            'payment_status' => 'pending_payment',
        ]);
    }

    public function submit_payment($request_id, array $data)
    {
        $existing = $this->find_for_request((int) $request_id);
        $fields = [
            'request_id' => (int) $request_id,
            'amount' => (float) $data['amount'],
            'payment_method' => $data['payment_method'],
            'reference_number' => $data['reference_number'],
            'proof_original_name' => $data['proof_original_name'],
            'proof_stored_name' => $data['proof_stored_name'],
            'proof_file_path' => $data['proof_file_path'],
            'proof_file_size' => (int) $data['proof_file_size'],
            'proof_file_type' => $data['proof_file_type'],
            'payment_status' => 'payment_submitted',
            'submitted_by' => (int) $data['submitted_by'],
            'submitted_at' => date('Y-m-d H:i:s'),
            'verified_by' => null,
            'verified_at' => null,
            'remarks' => null,
        ];

        if (!empty($existing)) {
            $this->update((int) $existing['id'], $fields);
            return (int) $existing['id'];
        }

        return $this->insert($fields);
    }

    public function review_payment($payment_id, $payment_status, $remarks, $staff_id)
    {
        return $this->update((int) $payment_id, [
            'payment_status' => $payment_status,
            'remarks' => $remarks !== '' ? $remarks : null,
            'verified_by' => (int) $staff_id,
            'verified_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
