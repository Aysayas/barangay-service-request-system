<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Service_model extends Model
{
    protected $table = 'services';
    protected $primary_key = 'id';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'requirements_text',
        'fee',
        'requires_payment',
        'is_active',
    ];

    public function active_services()
    {
        return $this->db
            ->table($this->table)
            ->where('is_active', 1)
            ->order_by('name', 'ASC')
            ->get_all();
    }

    public function find_active($id)
    {
        return $this->db
            ->table($this->table)
            ->where([
                'id' => (int) $id,
                'is_active' => 1,
            ])
            ->limit(1)
            ->get();
    }

    public function all_for_admin()
    {
        return $this->db
            ->table($this->table)
            ->order_by('created_at', 'DESC')
            ->get_all();
    }

    public function find_admin($id)
    {
        return $this->find((int) $id);
    }

    public function slug_exists($slug, $ignore_id = null)
    {
        $sql = "SELECT id FROM services WHERE slug = ?";
        $params = [$slug];

        if (!empty($ignore_id)) {
            $sql .= " AND id != ?";
            $params[] = (int) $ignore_id;
        }

        $sql .= " LIMIT 1";

        return !empty($this->db->raw($sql, $params)->fetch(PDO::FETCH_ASSOC));
    }

    public function create_service(array $data)
    {
        return $this->insert([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'requirements_text' => $data['requirements_text'],
            'fee' => (float) $data['fee'],
            'requires_payment' => (int) $data['requires_payment'],
            'is_active' => (int) $data['is_active'],
        ]);
    }

    public function update_service($id, array $data)
    {
        return $this->update((int) $id, [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'requirements_text' => $data['requirements_text'],
            'fee' => (float) $data['fee'],
            'requires_payment' => (int) $data['requires_payment'],
            'is_active' => (int) $data['is_active'],
        ]);
    }

    public function toggle_service($id)
    {
        $service = $this->find_admin((int) $id);

        if (empty($service)) {
            return false;
        }

        return $this->update((int) $id, [
            'is_active' => ((int) $service['is_active'] === 1) ? 0 : 1,
        ]);
    }

    public function admin_counts()
    {
        $sql = "SELECT
                    COUNT(*) AS total_services,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active_services
                FROM services";

        $row = $this->db->raw($sql)->fetch(PDO::FETCH_ASSOC);

        return [
            'total_services' => (int) ($row['total_services'] ?? 0),
            'active_services' => (int) ($row['active_services'] ?? 0),
        ];
    }
}
