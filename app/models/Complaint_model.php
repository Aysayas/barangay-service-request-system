<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Complaint_model extends Model
{
    protected $table = 'complaints';
    protected $primary_key = 'id';

    protected $fillable = [
        'reference_no',
        'user_id',
        'complainant_name',
        'complainant_email',
        'complainant_contact',
        'subject',
        'category',
        'description',
        'incident_date',
        'location',
        'respondent_name',
        'status',
        'priority',
        'staff_notes',
        'resolution_notes',
        'assigned_to',
        'is_anonymous',
    ];

    public function allowed_statuses()
    {
        return [
            'submitted',
            'under_review',
            'needs_info',
            'investigating',
            'resolved',
            'closed',
            'dismissed',
        ];
    }

    public function allowed_priorities()
    {
        return ['low', 'medium', 'high'];
    }

    public function categories()
    {
        return [
            'noise_complaint' => 'Noise complaint',
            'sanitation' => 'Sanitation',
            'neighborhood_dispute' => 'Neighborhood dispute',
            'public_disturbance' => 'Public disturbance',
            'property_concern' => 'Property concern',
            'business_related_concern' => 'Business-related concern',
            'other' => 'Other',
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

    public function create_complaint(array $data)
    {
        return $this->insert([
            'reference_no' => $data['reference_no'],
            'user_id' => (int) $data['user_id'],
            'complainant_name' => $data['complainant_name'],
            'complainant_email' => $data['complainant_email'],
            'complainant_contact' => $data['complainant_contact'] ?: null,
            'subject' => $data['subject'],
            'category' => $data['category'],
            'description' => $data['description'],
            'incident_date' => $data['incident_date'] ?: null,
            'location' => $data['location'],
            'respondent_name' => $data['respondent_name'] ?: null,
            'status' => 'submitted',
            'priority' => 'medium',
            'staff_notes' => null,
            'resolution_notes' => null,
            'assigned_to' => null,
            'is_anonymous' => 0,
        ]);
    }

    public function for_user($user_id)
    {
        $sql = "SELECT c.*, CONCAT(assigned.first_name, ' ', assigned.last_name) AS assigned_to_name
                FROM complaints c
                LEFT JOIN users assigned ON assigned.id = c.assigned_to
                WHERE c.user_id = ?
                ORDER BY c.created_at DESC";

        return $this->db->raw($sql, [(int) $user_id])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recent_for_user($user_id, $limit = 5)
    {
        $sql = "SELECT *
                FROM complaints
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT " . (int) $limit;

        return $this->db->raw($sql, [(int) $user_id])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find_for_user($complaint_id, $user_id)
    {
        $sql = "SELECT c.*, CONCAT(assigned.first_name, ' ', assigned.last_name) AS assigned_to_name
                FROM complaints c
                LEFT JOIN users assigned ON assigned.id = c.assigned_to
                WHERE c.id = ? AND c.user_id = ?
                LIMIT 1";

        return $this->db->raw($sql, [(int) $complaint_id, (int) $user_id])->fetch(PDO::FETCH_ASSOC);
    }

    public function complaint_queue($status = 'all', $search = '')
    {
        $params = [];
        $where = [];

        if ($status !== 'all') {
            $where[] = 'c.status = ?';
            $params[] = $status;
        }

        if ($search !== '') {
            $where[] = "(c.reference_no LIKE ? OR c.subject LIKE ? OR c.complainant_name LIKE ? OR c.category LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT c.*,
                       CONCAT(u.first_name, ' ', u.last_name) AS resident_name,
                       CONCAT(assigned.first_name, ' ', assigned.last_name) AS assigned_to_name
                FROM complaints c
                INNER JOIN users u ON u.id = c.user_id
                LEFT JOIN users assigned ON assigned.id = c.assigned_to
                {$where_sql}
                ORDER BY c.created_at DESC";

        return $this->db->raw($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find_for_staff($complaint_id)
    {
        $sql = "SELECT c.*,
                       CONCAT(u.first_name, ' ', u.last_name) AS resident_name,
                       u.email AS resident_email,
                       u.contact_number AS resident_contact_number,
                       u.address AS resident_address,
                       CONCAT(assigned.first_name, ' ', assigned.last_name) AS assigned_to_name
                FROM complaints c
                INNER JOIN users u ON u.id = c.user_id
                LEFT JOIN users assigned ON assigned.id = c.assigned_to
                WHERE c.id = ?
                LIMIT 1";

        return $this->db->raw($sql, [(int) $complaint_id])->fetch(PDO::FETCH_ASSOC);
    }

    public function update_staff_review($complaint_id, array $data)
    {
        return $this->update((int) $complaint_id, [
            'status' => $data['status'],
            'priority' => $data['priority'],
            'staff_notes' => $data['staff_notes'] !== '' ? $data['staff_notes'] : null,
            'resolution_notes' => $data['resolution_notes'] !== '' ? $data['resolution_notes'] : null,
            'assigned_to' => !empty($data['assigned_to']) ? (int) $data['assigned_to'] : null,
        ]);
    }

    public function resident_counts($user_id)
    {
        $sql = "SELECT
                    COUNT(*) AS total_complaints,
                    SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) AS submitted_count,
                    SUM(CASE WHEN status IN ('under_review', 'needs_info', 'investigating') THEN 1 ELSE 0 END) AS active_count,
                    SUM(CASE WHEN status IN ('resolved', 'closed') THEN 1 ELSE 0 END) AS resolved_count
                FROM complaints
                WHERE user_id = ?";

        $row = $this->db->raw($sql, [(int) $user_id])->fetch(PDO::FETCH_ASSOC);

        return [
            'total_complaints' => (int) ($row['total_complaints'] ?? 0),
            'submitted_count' => (int) ($row['submitted_count'] ?? 0),
            'active_count' => (int) ($row['active_count'] ?? 0),
            'resolved_count' => (int) ($row['resolved_count'] ?? 0),
        ];
    }

    public function staff_counts()
    {
        $sql = "SELECT
                    COUNT(*) AS total_complaints,
                    SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) AS submitted_count,
                    SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) AS under_review_count,
                    SUM(CASE WHEN status = 'needs_info' THEN 1 ELSE 0 END) AS needs_info_count,
                    SUM(CASE WHEN status = 'investigating' THEN 1 ELSE 0 END) AS investigating_count,
                    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) AS resolved_count,
                    SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) AS closed_count,
                    SUM(CASE WHEN status = 'dismissed' THEN 1 ELSE 0 END) AS dismissed_count
                FROM complaints";

        $row = $this->db->raw($sql)->fetch(PDO::FETCH_ASSOC);

        return [
            'total_complaints' => (int) ($row['total_complaints'] ?? 0),
            'submitted_count' => (int) ($row['submitted_count'] ?? 0),
            'under_review_count' => (int) ($row['under_review_count'] ?? 0),
            'needs_info_count' => (int) ($row['needs_info_count'] ?? 0),
            'investigating_count' => (int) ($row['investigating_count'] ?? 0),
            'resolved_count' => (int) ($row['resolved_count'] ?? 0),
            'closed_count' => (int) ($row['closed_count'] ?? 0),
            'dismissed_count' => (int) ($row['dismissed_count'] ?? 0),
        ];
    }

    public function admin_counts()
    {
        $sql = "SELECT
                    COUNT(*) AS total_complaints,
                    SUM(CASE WHEN status IN ('submitted', 'under_review', 'needs_info', 'investigating') THEN 1 ELSE 0 END) AS open_complaints
                FROM complaints";

        $row = $this->db->raw($sql)->fetch(PDO::FETCH_ASSOC);

        return [
            'total_complaints' => (int) ($row['total_complaints'] ?? 0),
            'open_complaints' => (int) ($row['open_complaints'] ?? 0),
        ];
    }

    public function recent_for_staff($limit = 6)
    {
        $sql = "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) AS resident_name
                FROM complaints c
                INNER JOIN users u ON u.id = c.user_id
                ORDER BY c.created_at DESC
                LIMIT " . (int) $limit;

        return $this->db->raw($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
