<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AdminAnnouncements extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('Announcement_model');
        $this->call->model('Audit_log_model');
    }

    public function index()
    {
        $this->call->view('admin/announcements/index', [
            'title' => 'Manage Announcements',
            'announcements' => $this->Announcement_model->all_for_admin(),
        ]);
    }

    public function create()
    {
        $this->call->view('admin/announcements/form', [
            'title' => 'Create Announcement',
            'mode' => 'create',
            'announcement' => [],
            'old' => $this->session->flashdata('old') ?: [],
        ]);
    }

    public function store()
    {
        $data = $this->announcementInput();
        $errors = $this->validateAnnouncement($data);

        if (!empty($errors)) {
            $this->redirectWithErrors('admin/announcements/create', $errors, $data);
        }

        $data['slug'] = $this->uniqueAnnouncementSlug($data['slug']);
        $announcement_id = $this->Announcement_model->create_announcement($data);

        $this->Audit_log_model->record(auth_user()['id'], 'created_announcement', 'announcement', $announcement_id, 'Created announcement: ' . $data['title']);
        $this->session->set_flashdata('success', 'Announcement created.');
        redirect('admin/announcements');
        exit;
    }

    public function edit($id)
    {
        $announcement = $this->Announcement_model->find((int) $id);

        if (empty($announcement)) {
            $this->session->set_flashdata('error', 'Announcement not found.');
            redirect('admin/announcements');
            exit;
        }

        $this->call->view('admin/announcements/form', [
            'title' => 'Edit Announcement',
            'mode' => 'edit',
            'announcement' => $announcement,
            'old' => $this->session->flashdata('old') ?: [],
        ]);
    }

    public function update($id)
    {
        $announcement = $this->Announcement_model->find((int) $id);

        if (empty($announcement)) {
            $this->session->set_flashdata('error', 'Announcement not found.');
            redirect('admin/announcements');
            exit;
        }

        $data = $this->announcementInput();
        $errors = $this->validateAnnouncement($data, (int) $id);

        if (!empty($errors)) {
            $this->redirectWithErrors('admin/announcements/edit/' . (int) $id, $errors, $data);
        }

        $data['slug'] = $this->uniqueAnnouncementSlug($data['slug'], (int) $id);
        $this->Announcement_model->update_announcement((int) $id, $data);

        $this->Audit_log_model->record(auth_user()['id'], 'updated_announcement', 'announcement', (int) $id, 'Updated announcement: ' . $data['title']);
        $this->session->set_flashdata('success', 'Announcement updated.');
        redirect('admin/announcements');
        exit;
    }

    public function toggle($id)
    {
        $announcement = $this->Announcement_model->find((int) $id);

        if (empty($announcement)) {
            $this->session->set_flashdata('error', 'Announcement not found.');
            redirect('admin/announcements');
            exit;
        }

        $this->Announcement_model->toggle_announcement((int) $id);
        $this->Audit_log_model->record(auth_user()['id'], 'toggled_announcement', 'announcement', (int) $id, 'Toggled announcement: ' . $announcement['title']);

        $this->session->set_flashdata('success', 'Announcement publish status updated.');
        redirect('admin/announcements');
        exit;
    }

    private function announcementInput()
    {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');

        return [
            'title' => $title,
            'slug' => slugify($slug !== '' ? $slug : $title),
            'body' => trim($_POST['body'] ?? ''),
            'is_published' => isset($_POST['is_published']) ? 1 : 0,
        ];
    }

    private function validateAnnouncement(array $data, $ignore_id = null)
    {
        $errors = [];

        if ($data['title'] === '') {
            $errors[] = 'Announcement title is required.';
        }

        if ($data['body'] === '') {
            $errors[] = 'Announcement content is required.';
        }

        if ($this->Announcement_model->slug_exists($data['slug'], $ignore_id)) {
            $errors[] = 'Announcement slug is already used.';
        }

        return $errors;
    }

    private function uniqueAnnouncementSlug($slug, $ignore_id = null)
    {
        $base = slugify($slug);
        $candidate = $base;
        $count = 2;

        while ($this->Announcement_model->slug_exists($candidate, $ignore_id)) {
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
}
