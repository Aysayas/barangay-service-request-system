<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

require_once SYSTEM_DIR . 'libraries/Upload.php';

class AdminCommunity extends Controller
{
    private $max_upload_mb = 4;

    private $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

    private $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];

    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('Community_post_model');
        $this->call->model('Audit_log_model');
    }

    public function index()
    {
        $category = trim($_GET['category'] ?? 'all');
        $search = trim($_GET['search'] ?? '');
        $categories = $this->Community_post_model->categories();

        if ($category !== 'all' && !array_key_exists($category, $categories)) {
            $category = 'all';
        }

        $this->call->view('admin/community/index', [
            'title' => 'Manage Community',
            'posts' => $this->Community_post_model->all_for_admin($category, $search),
            'categories' => $categories,
            'current_category' => $category,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $this->call->view('admin/community/form', [
            'title' => 'Create Community Post',
            'mode' => 'create',
            'post' => [],
            'old' => $this->session->flashdata('old') ?: [],
            'categories' => $this->Community_post_model->categories(),
            'max_upload_mb' => $this->max_upload_mb,
        ]);
    }

    public function store()
    {
        $admin = auth_user();
        $data = $this->postInput();
        $data['created_by'] = (int) $admin['id'];
        $data['updated_by'] = (int) $admin['id'];
        $errors = $this->validatePost($data, null, $_FILES['image'] ?? []);

        if (!empty($errors)) {
            $this->redirectWithErrors('admin/community/create', $errors, $data);
        }

        $data['slug'] = $this->uniqueSlug($data['slug']);
        $moved_file = null;

        try {
            $this->db->transaction();

            $post_id = $this->Community_post_model->create_post($data);

            if ($this->hasImage($_FILES['image'] ?? [])) {
                $image_path = $this->saveImage((int) $post_id, $_FILES['image'], $moved_file);
                $this->Community_post_model->update_image((int) $post_id, $image_path, (int) $admin['id']);
            }

            $this->Audit_log_model->record((int) $admin['id'], 'created_community_post', 'community_post', (int) $post_id, 'Created community post: ' . $data['title']);
            $this->db->commit();

            $this->session->set_flashdata('success', 'Community post created.');
            redirect('admin/community');
            exit;
        } catch (Throwable $e) {
            $this->db->roll_back();
            $this->deleteAbsoluteFile($moved_file);
            $this->redirectWithErrors('admin/community/create', ['Community post could not be created. Please check the form details and image upload, then try again.'], $data);
        }
    }

    public function edit($id)
    {
        $post = $this->Community_post_model->find((int) $id);

        if (empty($post)) {
            $this->session->set_flashdata('error', 'Community post not found.');
            redirect('admin/community');
            exit;
        }

        $this->call->view('admin/community/form', [
            'title' => 'Edit Community Post',
            'mode' => 'edit',
            'post' => $post,
            'old' => $this->session->flashdata('old') ?: [],
            'categories' => $this->Community_post_model->categories(),
            'max_upload_mb' => $this->max_upload_mb,
        ]);
    }

    public function update($id)
    {
        $admin = auth_user();
        $post = $this->Community_post_model->find((int) $id);

        if (empty($post)) {
            $this->session->set_flashdata('error', 'Community post not found.');
            redirect('admin/community');
            exit;
        }

        $data = $this->postInput();
        $data['updated_by'] = (int) $admin['id'];
        $data['published_at'] = $post['published_at'] ?? null;
        $errors = $this->validatePost($data, (int) $id, $_FILES['image'] ?? []);

        if (!empty($errors)) {
            $this->redirectWithErrors('admin/community/edit/' . (int) $id, $errors, $data);
        }

        $data['slug'] = $this->uniqueSlug($data['slug'], (int) $id);
        $moved_file = null;
        $old_image = $post['image_path'] ?? null;

        try {
            $this->db->transaction();
            $this->Community_post_model->update_post((int) $id, $data);

            if ($this->hasImage($_FILES['image'] ?? [])) {
                $image_path = $this->saveImage((int) $id, $_FILES['image'], $moved_file);
                $this->Community_post_model->update_image((int) $id, $image_path, (int) $admin['id']);
            }

            $this->Audit_log_model->record((int) $admin['id'], 'updated_community_post', 'community_post', (int) $id, 'Updated community post: ' . $data['title']);
            $this->db->commit();

            if (!empty($moved_file) && !empty($old_image)) {
                $this->deleteRelativeFile($old_image);
            }

            $this->session->set_flashdata('success', 'Community post updated.');
            redirect('admin/community');
            exit;
        } catch (Throwable $e) {
            $this->db->roll_back();
            $this->deleteAbsoluteFile($moved_file);
            $this->redirectWithErrors('admin/community/edit/' . (int) $id, ['Community post could not be updated. Please check the form details and image upload, then try again.'], $data);
        }
    }

    public function toggle($id)
    {
        $admin = auth_user();
        $post = $this->Community_post_model->find((int) $id);

        if (empty($post)) {
            $this->session->set_flashdata('error', 'Community post not found.');
            redirect('admin/community');
            exit;
        }

        $this->Community_post_model->toggle_publish((int) $id, (int) $admin['id']);
        $this->Audit_log_model->record((int) $admin['id'], 'toggled_community_post', 'community_post', (int) $id, 'Toggled publish status for community post: ' . $post['title']);

        $this->session->set_flashdata('success', 'Community post publish status updated.');
        redirect('admin/community');
        exit;
    }

    public function feature($id)
    {
        $admin = auth_user();
        $post = $this->Community_post_model->find((int) $id);

        if (empty($post)) {
            $this->session->set_flashdata('error', 'Community post not found.');
            redirect('admin/community');
            exit;
        }

        $this->Community_post_model->toggle_feature((int) $id, (int) $admin['id']);
        $this->Audit_log_model->record((int) $admin['id'], 'toggled_community_feature', 'community_post', (int) $id, 'Toggled featured status for community post: ' . $post['title']);

        $this->session->set_flashdata('success', 'Community post featured status updated.');
        redirect('admin/community');
        exit;
    }

    public function image($id)
    {
        $post = $this->Community_post_model->find((int) $id);

        if (empty($post) || empty($post['image_path'])) {
            show_404();
            return;
        }

        $real_path = safe_storage_path($post['image_path'], 'runtime/uploads/community');

        if ($real_path === null) {
            show_404();
            return;
        }

        $mime = mime_content_type($real_path) ?: 'application/octet-stream';

        stream_protected_file($real_path, $mime, basename($real_path), 'inline');
    }

    private function postInput()
    {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');

        return [
            'title' => $title,
            'slug' => slugify($slug !== '' ? $slug : $title),
            'category' => trim($_POST['category'] ?? ''),
            'excerpt' => trim($_POST['excerpt'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
            'event_date' => trim($_POST['event_date'] ?? ''),
            'event_time' => $this->normalizeTime(trim($_POST['event_time'] ?? '')),
            'venue' => trim($_POST['venue'] ?? ''),
            'organizer' => trim($_POST['organizer'] ?? ''),
            'resource_link' => trim($_POST['resource_link'] ?? ''),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_published' => isset($_POST['is_published']) ? 1 : 0,
        ];
    }

    private function validatePost(array $data, $ignore_id = null, array $image = [])
    {
        $errors = [];

        if ($data['title'] === '') {
            $errors[] = 'Title is required.';
        } elseif (strlen($data['title']) > 180) {
            $errors[] = 'Title must be 180 characters or fewer.';
        }

        if (!array_key_exists($data['category'], $this->Community_post_model->categories())) {
            $errors[] = 'Choose a valid community category.';
        }

        if ($data['content'] === '') {
            $errors[] = 'Content is required.';
        }

        if (strlen($data['excerpt']) > 255) {
            $errors[] = 'Excerpt must be 255 characters or fewer.';
        }

        if ($this->Community_post_model->slug_exists($data['slug'], $ignore_id)) {
            $errors[] = 'Slug is already used.';
        }

        if ($data['event_date'] !== '') {
            $date = DateTime::createFromFormat('Y-m-d', $data['event_date']);

            if (!$date || $date->format('Y-m-d') !== $data['event_date']) {
                $errors[] = 'Event date must be a valid date.';
            }
        }

        if ($data['event_time'] !== '' && !preg_match('/^\d{2}:\d{2}:\d{2}$/', $data['event_time'])) {
            $errors[] = 'Event time must be valid.';
        }

        if ($data['resource_link'] !== '') {
            $scheme = strtolower((string) parse_url($data['resource_link'], PHP_URL_SCHEME));

            if (!filter_var($data['resource_link'], FILTER_VALIDATE_URL) || !in_array($scheme, ['http', 'https'], true)) {
                $errors[] = 'Resource link must be a valid http or https URL.';
            }
        }

        if ($this->hasImage($image)) {
            if (($image['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                $errors[] = 'The uploaded image could not be read.';
            }

            $extension = strtolower(pathinfo($image['name'] ?? '', PATHINFO_EXTENSION));

            if (!in_array($extension, $this->allowed_extensions, true)) {
                $errors[] = 'Allowed image types are JPG, PNG, and WEBP.';
            }

            if (($image['size'] ?? 0) > ($this->max_upload_mb * 1024 * 1024)) {
                $errors[] = 'Image must be ' . $this->max_upload_mb . 'MB or smaller.';
            }
        }

        return array_values(array_unique($errors));
    }

    private function saveImage($post_id, array $file, &$moved_file)
    {
        $upload_dir = ROOT_DIR . 'runtime/uploads/community/post_' . (int) $post_id;
        $upload = new Upload($file);
        $upload
            ->set_dir($upload_dir)
            ->allowed_extensions($this->allowed_extensions)
            ->allowed_mimes($this->allowed_mimes)
            ->max_size($this->max_upload_mb)
            ->encrypt_name();

        if (!$upload->do_upload()) {
            throw new RuntimeException(implode(' ', $upload->get_errors()));
        }

        $stored_name = $upload->get_filename();
        $moved_file = $upload_dir . DIRECTORY_SEPARATOR . $stored_name;

        return 'runtime/uploads/community/post_' . (int) $post_id . '/' . $stored_name;
    }

    private function hasImage(array $image)
    {
        return !empty($image) && (($image['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE);
    }

    private function normalizeTime($time)
    {
        if ($time === '') {
            return '';
        }

        return preg_match('/^\d{2}:\d{2}$/', $time) ? $time . ':00' : $time;
    }

    private function uniqueSlug($slug, $ignore_id = null)
    {
        $base = slugify($slug);
        $candidate = $base;
        $count = 2;

        while ($this->Community_post_model->slug_exists($candidate, $ignore_id)) {
            $candidate = $base . '-' . $count;
            $count++;
        }

        return $candidate;
    }

    private function redirectWithErrors($path, array $errors, array $old)
    {
        $this->session->set_flashdata('errors', $errors);
        $this->session->set_flashdata('old', $old);
        redirect($path);
        exit;
    }

    private function deleteAbsoluteFile($path)
    {
        safe_delete_storage_file($path, 'runtime/uploads/community');
    }

    private function deleteRelativeFile($relative_path)
    {
        safe_delete_storage_file($relative_path, 'runtime/uploads/community');
    }
}
