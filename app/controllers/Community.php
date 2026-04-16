<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Community extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('Community_post_model');
    }

    public function index()
    {
        $category = trim($_GET['category'] ?? 'all');
        $categories = $this->Community_post_model->categories();

        if ($category !== 'all' && !array_key_exists($category, $categories)) {
            $category = 'all';
        }

        $this->call->view('community/index', [
            'title' => 'Community',
            'categories' => $categories,
            'current_category' => $category,
            'featured_posts' => $this->Community_post_model->featured(3),
            'posts' => $this->Community_post_model->published($category, 12),
            'upcoming_events' => $this->Community_post_model->upcoming_events(4),
            'resources' => $this->Community_post_model->resources(4),
        ]);
    }

    public function show($slug)
    {
        $post = $this->Community_post_model->find_published_by_slug($slug);

        if (empty($post)) {
            show_404();
            return;
        }

        $this->call->view('community/show', [
            'title' => $post['title'],
            'post' => $post,
            'related_posts' => $this->Community_post_model->related_posts((int) $post['id'], $post['category'], 3),
        ]);
    }

    public function image($id)
    {
        $post = $this->Community_post_model->find_published_image((int) $id);

        if (empty($post)) {
            show_404();
            return;
        }

        $path = ROOT_DIR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $post['image_path']);
        $upload_root = realpath(ROOT_DIR . 'runtime/uploads/community');
        $real_path = realpath($path);

        if ($upload_root === false || $real_path === false || strpos($real_path, $upload_root) !== 0 || !is_file($real_path)) {
            show_404();
            return;
        }

        $mime = mime_content_type($real_path) ?: 'application/octet-stream';

        while (ob_get_level() > 0) {
            @ob_end_clean();
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($real_path));
        header('Content-Disposition: inline; filename="' . basename($real_path) . '"');
        header('X-Content-Type-Options: nosniff');
        readfile($real_path);
        exit;
    }
}
