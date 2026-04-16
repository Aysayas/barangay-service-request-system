<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Community_post_model extends Model
{
    protected $table = 'community_posts';
    protected $primary_key = 'id';

    protected $fillable = [
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'image_path',
        'event_date',
        'event_time',
        'venue',
        'organizer',
        'resource_link',
        'is_featured',
        'is_published',
        'published_at',
        'created_by',
        'updated_by',
    ];

    public function categories()
    {
        return [
            'announcement' => 'Announcement',
            'event' => 'Event',
            'program' => 'Program',
            'advisory' => 'Advisory',
            'resource' => 'Resource',
        ];
    }

    public function all_for_admin($category = 'all', $search = '')
    {
        $params = [];
        $where = [];

        if ($category !== 'all') {
            $where[] = 'cp.category = ?';
            $params[] = $category;
        }

        if ($search !== '') {
            $where[] = "(cp.title LIKE ? OR cp.excerpt LIKE ? OR cp.content LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT cp.*,
                       CONCAT(creator.first_name, ' ', creator.last_name) AS created_by_name,
                       CONCAT(updater.first_name, ' ', updater.last_name) AS updated_by_name
                FROM community_posts cp
                LEFT JOIN users creator ON creator.id = cp.created_by
                LEFT JOIN users updater ON updater.id = cp.updated_by
                {$where_sql}
                ORDER BY cp.created_at DESC";

        return $this->db->raw($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function published($category = 'all', $limit = 12)
    {
        $params = [];
        $where = ['is_published = 1'];

        if ($category !== 'all') {
            $where[] = 'category = ?';
            $params[] = $category;
        }

        $where_sql = 'WHERE ' . implode(' AND ', $where);
        $sql = "SELECT *
                FROM community_posts
                {$where_sql}
                ORDER BY is_featured DESC, published_at DESC, created_at DESC
                LIMIT " . (int) $limit;

        return $this->db->raw($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function featured($limit = 3)
    {
        $sql = "SELECT *
                FROM community_posts
                WHERE is_published = 1 AND is_featured = 1
                ORDER BY published_at DESC, created_at DESC
                LIMIT " . (int) $limit;

        return $this->db->raw($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function upcoming_events($limit = 4)
    {
        $sql = "SELECT *
                FROM community_posts
                WHERE is_published = 1
                  AND category = 'event'
                  AND event_date IS NOT NULL
                  AND event_date >= CURDATE()
                ORDER BY event_date ASC, event_time ASC, published_at DESC
                LIMIT " . (int) $limit;

        return $this->db->raw($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function resources($limit = 4)
    {
        $sql = "SELECT *
                FROM community_posts
                WHERE is_published = 1
                  AND category IN ('resource', 'advisory', 'program')
                ORDER BY published_at DESC, created_at DESC
                LIMIT " . (int) $limit;

        return $this->db->raw($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find_published_by_slug($slug)
    {
        $sql = "SELECT *
                FROM community_posts
                WHERE slug = ? AND is_published = 1
                LIMIT 1";

        return $this->db->raw($sql, [$slug])->fetch(PDO::FETCH_ASSOC);
    }

    public function find_published_image($id)
    {
        $sql = "SELECT id, title, image_path
                FROM community_posts
                WHERE id = ? AND is_published = 1 AND image_path IS NOT NULL
                LIMIT 1";

        return $this->db->raw($sql, [(int) $id])->fetch(PDO::FETCH_ASSOC);
    }

    public function related_posts($current_id, $category, $limit = 3)
    {
        $sql = "SELECT *
                FROM community_posts
                WHERE id != ?
                  AND category = ?
                  AND is_published = 1
                ORDER BY published_at DESC, created_at DESC
                LIMIT " . (int) $limit;

        return $this->db->raw($sql, [(int) $current_id, $category])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function slug_exists($slug, $ignore_id = null)
    {
        $sql = "SELECT id FROM community_posts WHERE slug = ?";
        $params = [$slug];

        if (!empty($ignore_id)) {
            $sql .= " AND id != ?";
            $params[] = (int) $ignore_id;
        }

        $sql .= " LIMIT 1";

        return !empty($this->db->raw($sql, $params)->fetch(PDO::FETCH_ASSOC));
    }

    public function create_post(array $data)
    {
        return $this->insert($this->postFields($data));
    }

    public function update_post($id, array $data)
    {
        $fields = $this->postFields($data);

        if (!array_key_exists('image_path', $data)) {
            unset($fields['image_path']);
        }

        if (!array_key_exists('created_by', $data)) {
            unset($fields['created_by']);
        }

        return $this->update((int) $id, $fields);
    }

    public function toggle_publish($id, $admin_id)
    {
        $post = $this->find((int) $id);

        if (empty($post)) {
            return false;
        }

        $is_published = ((int) $post['is_published'] === 1) ? 0 : 1;

        return $this->update((int) $id, [
            'is_published' => $is_published,
            'published_at' => $is_published === 1 ? date('Y-m-d H:i:s') : null,
            'updated_by' => (int) $admin_id,
        ]);
    }

    public function toggle_feature($id, $admin_id)
    {
        $post = $this->find((int) $id);

        if (empty($post)) {
            return false;
        }

        return $this->update((int) $id, [
            'is_featured' => ((int) $post['is_featured'] === 1) ? 0 : 1,
            'updated_by' => (int) $admin_id,
        ]);
    }

    public function update_image($id, $image_path, $admin_id)
    {
        return $this->update((int) $id, [
            'image_path' => $image_path,
            'updated_by' => (int) $admin_id,
        ]);
    }

    private function postFields(array $data)
    {
        $published_at = null;

        if ((int) ($data['is_published'] ?? 0) === 1) {
            $published_at = !empty($data['published_at']) ? $data['published_at'] : date('Y-m-d H:i:s');
        }

        return [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'category' => $data['category'],
            'excerpt' => $data['excerpt'] ?: null,
            'content' => $data['content'],
            'image_path' => $data['image_path'] ?? null,
            'event_date' => $data['event_date'] ?: null,
            'event_time' => $data['event_time'] ?: null,
            'venue' => $data['venue'] ?: null,
            'organizer' => $data['organizer'] ?: null,
            'resource_link' => $data['resource_link'] ?: null,
            'is_featured' => (int) ($data['is_featured'] ?? 0),
            'is_published' => (int) ($data['is_published'] ?? 0),
            'published_at' => $published_at,
            'created_by' => $data['created_by'] ?? null,
            'updated_by' => $data['updated_by'] ?? null,
        ];
    }
}
