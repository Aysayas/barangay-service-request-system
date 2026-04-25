<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Service_request_model extends Model
{
    protected $table = 'service_requests';
    protected $primary_key = 'id';

    protected $fillable = [
        'user_id',
        'service_id',
        'reference_no',
        'purpose',
        'remarks',
        'status',
        'staff_notes',
        'final_document_path',
        'last_processed_by',
    ];

    public function allowed_statuses()
    {
        return [
            'submitted',
            'under_review',
            'needs_info',
            'approved',
            'rejected',
            'ready_for_pickup',
            'released',
        ];
    }

    public function reference_exists($reference_no)
    {
        $row = $this->db
            ->table($this->table)
            ->where('reference_no', $reference_no)
            ->limit(1)
            ->get();

        return !empty($row);
    }

    public function create_request(array $data)
    {
        return $this->insert([
            'user_id' => (int) $data['user_id'],
            'service_id' => (int) $data['service_id'],
            'reference_no' => $data['reference_no'],
            'purpose' => $data['purpose'],
            'remarks' => $data['remarks'] ?? null,
            'status' => 'submitted',
            'staff_notes' => null,
            'final_document_path' => null,
        ]);
    }

    public function for_user($user_id)
    {
        $sql = "SELECT sr.*, s.name AS service_name, s.fee, s.requires_payment
                FROM service_requests sr
                INNER JOIN services s ON s.id = sr.service_id
                WHERE sr.user_id = ?
                ORDER BY sr.created_at DESC";

        return $this->db->raw($sql, [(int) $user_id])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recent_for_user($user_id, $limit = 5)
    {
        $sql = "SELECT sr.*, s.name AS service_name
                FROM service_requests sr
                INNER JOIN services s ON s.id = sr.service_id
                WHERE sr.user_id = ?
                ORDER BY sr.created_at DESC
                LIMIT " . (int) $limit;

        return $this->db->raw($sql, [(int) $user_id])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find_for_user($request_id, $user_id)
    {
        $sql = "SELECT sr.*, s.name AS service_name, s.description AS service_description,
                       s.requirements_text, s.fee, s.requires_payment
                FROM service_requests sr
                INNER JOIN services s ON s.id = sr.service_id
                WHERE sr.id = ? AND sr.user_id = ?
                LIMIT 1";

        return $this->db->raw($sql, [(int) $request_id, (int) $user_id])->fetch(PDO::FETCH_ASSOC);
    }

    public function dashboard_counts($user_id)
    {
        $sql = "SELECT
                    COUNT(*) AS total_requests,
                    SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) AS submitted_count,
                    SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) AS under_review_count,
                    SUM(CASE WHEN status IN ('approved', 'ready_for_pickup', 'released') THEN 1 ELSE 0 END) AS completed_count
                FROM service_requests
                WHERE user_id = ?";

        $row = $this->db->raw($sql, [(int) $user_id])->fetch(PDO::FETCH_ASSOC);

        return [
            'total_requests' => (int) ($row['total_requests'] ?? 0),
            'submitted_count' => (int) ($row['submitted_count'] ?? 0),
            'under_review_count' => (int) ($row['under_review_count'] ?? 0),
            'completed_count' => (int) ($row['completed_count'] ?? 0),
        ];
    }

    public function resident_next_action_counts($user_id)
    {
        $sql = "SELECT
                    SUM(CASE
                        WHEN sr.status NOT IN ('released', 'rejected') THEN 1 ELSE 0
                    END) AS active_request_count,
                    SUM(CASE
                        WHEN s.requires_payment = 1
                             AND COALESCE(p.payment_status, 'pending_payment') IN ('pending_payment', 'payment_rejected')
                             AND sr.status NOT IN ('released', 'rejected')
                        THEN 1 ELSE 0
                    END) AS pending_payment_proof_count,
                    SUM(CASE
                        WHEN rfd.id IS NOT NULL
                             AND sr.status IN ('approved', 'ready_for_pickup', 'released')
                        THEN 1 ELSE 0
                    END) AS ready_document_count
                FROM service_requests sr
                INNER JOIN services s ON s.id = sr.service_id
                LEFT JOIN payments p ON p.request_id = sr.id
                LEFT JOIN request_final_documents rfd ON rfd.request_id = sr.id
                WHERE sr.user_id = ?";

        $row = $this->db->raw($sql, [(int) $user_id])->fetch(PDO::FETCH_ASSOC);

        return [
            'active_request_count' => (int) ($row['active_request_count'] ?? 0),
            'pending_payment_proof_count' => (int) ($row['pending_payment_proof_count'] ?? 0),
            'ready_document_count' => (int) ($row['ready_document_count'] ?? 0),
        ];
    }

    public function staff_counts()
    {
        $sql = "SELECT
                    COUNT(*) AS total_requests,
                    SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) AS submitted_count,
                    SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) AS under_review_count,
                    SUM(CASE WHEN status = 'needs_info' THEN 1 ELSE 0 END) AS needs_info_count,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count,
                    SUM(CASE WHEN status = 'ready_for_pickup' THEN 1 ELSE 0 END) AS ready_for_pickup_count,
                    SUM(CASE WHEN status = 'released' THEN 1 ELSE 0 END) AS released_count
                FROM service_requests";

        $row = $this->db->raw($sql)->fetch(PDO::FETCH_ASSOC);

        return [
            'total_requests' => (int) ($row['total_requests'] ?? 0),
            'submitted_count' => (int) ($row['submitted_count'] ?? 0),
            'under_review_count' => (int) ($row['under_review_count'] ?? 0),
            'needs_info_count' => (int) ($row['needs_info_count'] ?? 0),
            'approved_count' => (int) ($row['approved_count'] ?? 0),
            'rejected_count' => (int) ($row['rejected_count'] ?? 0),
            'ready_for_pickup_count' => (int) ($row['ready_for_pickup_count'] ?? 0),
            'released_count' => (int) ($row['released_count'] ?? 0),
        ];
    }

    public function recent_for_staff($limit = 6)
    {
        $sql = "SELECT sr.*, s.name AS service_name,
                       s.requires_payment,
                       CASE
                           WHEN s.requires_payment = 1 THEN COALESCE(p.payment_status, 'pending_payment')
                           ELSE NULL
                       END AS payment_status,
                       CONCAT(u.first_name, ' ', u.last_name) AS resident_name
                FROM service_requests sr
                INNER JOIN services s ON s.id = sr.service_id
                INNER JOIN users u ON u.id = sr.user_id
                LEFT JOIN payments p ON p.request_id = sr.id
                ORDER BY sr.created_at DESC
                LIMIT " . (int) $limit;

        return $this->db->raw($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function staff_queue($status = 'all', $search = '')
    {
        return $this->request_queue($status, $search);
    }

    public function request_queue($status = 'all', $search = '')
    {
        $params = [];
        $where = [];

        if ($status !== 'all') {
            $where[] = "sr.status = ?";
            $params[] = $status;
        }

        if ($search !== '') {
            $where[] = "(sr.reference_no LIKE ? OR CONCAT(u.first_name, ' ', u.last_name) LIKE ? OR s.name LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT sr.*, s.name AS service_name, s.requires_payment,
                       CASE
                           WHEN s.requires_payment = 1 THEN COALESCE(p.payment_status, 'pending_payment')
                           ELSE NULL
                       END AS payment_status,
                       CONCAT(u.first_name, ' ', u.last_name) AS resident_name
                FROM service_requests sr
                INNER JOIN services s ON s.id = sr.service_id
                INNER JOIN users u ON u.id = sr.user_id
                LEFT JOIN payments p ON p.request_id = sr.id
                {$where_sql}
                ORDER BY sr.created_at DESC";

        return $this->db->raw($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find_for_staff($request_id)
    {
        $sql = "SELECT sr.*, s.name AS service_name, s.description AS service_description,
                       s.requirements_text, s.fee, s.requires_payment,
                       CONCAT(u.first_name, ' ', u.last_name) AS resident_name,
                       u.email AS resident_email,
                       u.contact_number AS resident_contact_number,
                       u.address AS resident_address,
                       CONCAT(processor.first_name, ' ', processor.last_name) AS last_processed_by_name
                FROM service_requests sr
                INNER JOIN services s ON s.id = sr.service_id
                INNER JOIN users u ON u.id = sr.user_id
                LEFT JOIN users processor ON processor.id = sr.last_processed_by
                WHERE sr.id = ?
                LIMIT 1";

        return $this->db->raw($sql, [(int) $request_id])->fetch(PDO::FETCH_ASSOC);
    }

    public function update_staff_review($request_id, $status, $staff_notes, $staff_id)
    {
        return $this->update((int) $request_id, [
            'status' => $status,
            'staff_notes' => $staff_notes !== '' ? $staff_notes : null,
            'last_processed_by' => (int) $staff_id,
        ]);
    }

    public function admin_counts()
    {
        $sql = "SELECT
                    COUNT(*) AS total_requests,
                    SUM(CASE WHEN status IN ('submitted', 'under_review', 'needs_info') THEN 1 ELSE 0 END) AS pending_requests
                FROM service_requests";

        $row = $this->db->raw($sql)->fetch(PDO::FETCH_ASSOC);

        return [
            'total_requests' => (int) ($row['total_requests'] ?? 0),
            'pending_requests' => (int) ($row['pending_requests'] ?? 0),
        ];
    }
}
