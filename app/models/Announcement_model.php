<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Announcement_model extends Model
{
    protected $table = 'announcements';
    protected $primary_key = 'id';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'is_published',
        'published_at',
    ];

    public function all_for_admin()
    {
        return $this->db
            ->table($this->table)
            ->order_by('created_at', 'DESC')
            ->get_all();
    }

    public function published($limit = 3)
    {
        $sql = "SELECT * FROM announcements
                WHERE is_published = 1
                ORDER BY published_at DESC, created_at DESC
                LIMIT " . (int) $limit;

        return $this->db->raw($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function published_count()
    {
        $sql = "SELECT COUNT(*) AS total
                FROM announcements
                WHERE is_published = 1";

        $row = $this->db->raw($sql)->fetch(PDO::FETCH_ASSOC);

        return (int) ($row['total'] ?? 0);
    }

    public function slug_exists($slug, $ignore_id = null)
    {
        $sql = "SELECT id FROM announcements WHERE slug = ?";
        $params = [$slug];

        if (!empty($ignore_id)) {
            $sql .= " AND id != ?";
            $params[] = (int) $ignore_id;
        }

        $sql .= " LIMIT 1";

        return !empty($this->db->raw($sql, $params)->fetch(PDO::FETCH_ASSOC));
    }

    public function create_announcement(array $data)
    {
        return $this->insert([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'body' => $data['body'],
            'is_published' => (int) $data['is_published'],
            'published_at' => ((int) $data['is_published'] === 1) ? date('Y-m-d H:i:s') : null,
        ]);
    }

    public function update_announcement($id, array $data)
    {
        $announcement = $this->find((int) $id);
        $published_at = $announcement['published_at'] ?? null;

        if ((int) $data['is_published'] === 1 && empty($published_at)) {
            $published_at = date('Y-m-d H:i:s');
        }

        if ((int) $data['is_published'] === 0) {
            $published_at = null;
        }

        return $this->update((int) $id, [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'body' => $data['body'],
            'is_published' => (int) $data['is_published'],
            'published_at' => $published_at,
        ]);
    }

    public function toggle_announcement($id)
    {
        $announcement = $this->find((int) $id);

        if (empty($announcement)) {
            return false;
        }

        $is_published = ((int) $announcement['is_published'] === 1) ? 0 : 1;

        return $this->update((int) $id, [
            'is_published' => $is_published,
            'published_at' => $is_published === 1 ? date('Y-m-d H:i:s') : null,
        ]);
    }
}
