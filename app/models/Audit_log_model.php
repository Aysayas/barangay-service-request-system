<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Audit_log_model extends Model
{
    protected $table = 'audit_logs';
    protected $primary_key = 'id';
    protected $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'target_type',
        'target_id',
        'description',
        'created_at',
    ];

    public function record($user_id, $action, $target_type, $target_id, $description)
    {
        return $this->insert([
            'user_id' => !empty($user_id) ? (int) $user_id : null,
            'action' => $action,
            'target_type' => $target_type,
            'target_id' => (int) $target_id,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function for_target($target_type, $target_id, $limit = 10)
    {
        $sql = "SELECT al.*, CONCAT(u.first_name, ' ', u.last_name) AS user_name
                FROM audit_logs al
                LEFT JOIN users u ON u.id = al.user_id
                WHERE al.target_type = ? AND al.target_id = ?
                ORDER BY al.created_at DESC
                LIMIT " . (int) $limit;

        return $this->db->raw($sql, [$target_type, (int) $target_id])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recent_for_dashboard($limit = 5)
    {
        $sql = "SELECT al.*, CONCAT(u.first_name, ' ', u.last_name) AS user_name
                FROM audit_logs al
                LEFT JOIN users u ON u.id = al.user_id
                ORDER BY al.created_at DESC
                LIMIT " . (int) $limit;

        return $this->db->raw($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function list_for_admin($search = '', $action = '')
    {
        $params = [];
        $where = [];

        if ($action !== '') {
            $where[] = 'al.action = ?';
            $params[] = $action;
        }

        if ($search !== '') {
            $where[] = "(al.action LIKE ? OR al.target_type LIKE ? OR al.description LIKE ? OR CONCAT(u.first_name, ' ', u.last_name) LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT al.*, CONCAT(u.first_name, ' ', u.last_name) AS user_name
                FROM audit_logs al
                LEFT JOIN users u ON u.id = al.user_id
                {$where_sql}
                ORDER BY al.created_at DESC
                LIMIT 200";

        return $this->db->raw($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}
