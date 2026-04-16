<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class User_model extends Model
{
    protected $table = 'users';
    protected $primary_key = 'id';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'status',
        'contact_number',
        'address',
    ];

    public function find_by_email($email)
    {
        return $this->db
            ->table($this->table)
            ->where('email', $email)
            ->limit(1)
            ->get();
    }

    public function email_exists($email)
    {
        return !empty($this->find_by_email($email));
    }

    public function create_resident(array $data)
    {
        return $this->insert([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => 'resident',
            'status' => 'active',
            'contact_number' => $data['contact_number'] ?? null,
            'address' => $data['address'] ?? null,
        ]);
    }

    public function all_for_admin($role = 'all', $search = '')
    {
        $params = [];
        $where = [];

        if ($role !== 'all') {
            $where[] = 'role = ?';
            $params[] = $role;
        }

        if ($search !== '') {
            $where[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT * FROM users {$where_sql} ORDER BY created_at DESC";

        return $this->db->raw($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recent_for_admin($limit = 5)
    {
        $sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT " . (int) $limit;
        return $this->db->raw($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function admin_counts()
    {
        $sql = "SELECT
                    COUNT(*) AS total_users,
                    SUM(CASE WHEN role = 'resident' THEN 1 ELSE 0 END) AS total_residents,
                    SUM(CASE WHEN role = 'staff' THEN 1 ELSE 0 END) AS total_staff,
                    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) AS total_admins
                FROM users";

        $row = $this->db->raw($sql)->fetch(PDO::FETCH_ASSOC);

        return [
            'total_users' => (int) ($row['total_users'] ?? 0),
            'total_residents' => (int) ($row['total_residents'] ?? 0),
            'total_staff' => (int) ($row['total_staff'] ?? 0),
            'total_admins' => (int) ($row['total_admins'] ?? 0),
        ];
    }

    public function create_admin_user(array $data)
    {
        return $this->insert([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'status' => $data['status'],
            'contact_number' => $data['contact_number'] ?? null,
            'address' => $data['address'] ?? null,
        ]);
    }

    public function update_admin_user($id, array $data)
    {
        $fields = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => $data['status'],
            'contact_number' => $data['contact_number'] ?? null,
            'address' => $data['address'] ?? null,
        ];

        if (!empty($data['password'])) {
            $fields['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $this->update((int) $id, $fields);
    }

    public function toggle_status($id)
    {
        $user = $this->find((int) $id);

        if (empty($user)) {
            return false;
        }

        return $this->update((int) $id, [
            'status' => ($user['status'] === 'active') ? 'inactive' : 'active',
        ]);
    }

    public function email_exists_except($email, $ignore_id)
    {
        $sql = "SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1";
        return !empty($this->db->raw($sql, [$email, (int) $ignore_id])->fetch(PDO::FETCH_ASSOC));
    }
}
