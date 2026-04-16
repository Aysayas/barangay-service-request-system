<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AdminServices extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('Service_model');
        $this->call->model('Audit_log_model');
    }

    public function index()
    {
        $this->call->view('admin/services/index', [
            'title' => 'Manage Services',
            'services' => $this->Service_model->all_for_admin(),
        ]);
    }

    public function create()
    {
        $this->call->view('admin/services/form', [
            'title' => 'Create Service',
            'mode' => 'create',
            'service' => [],
            'old' => $this->session->flashdata('old') ?: [],
        ]);
    }

    public function store()
    {
        $data = $this->serviceInput();
        $errors = $this->validateService($data);

        if (!empty($errors)) {
            $this->redirectWithErrors('admin/services/create', $errors, $data);
        }

        $data['slug'] = $this->uniqueServiceSlug($data['slug']);
        $service_id = $this->Service_model->create_service($data);

        $this->Audit_log_model->record(auth_user()['id'], 'created_service', 'service', $service_id, 'Created service: ' . $data['name']);
        $this->session->set_flashdata('success', 'Service created.');
        redirect('admin/services');
        exit;
    }

    public function edit($id)
    {
        $service = $this->Service_model->find_admin((int) $id);

        if (empty($service)) {
            $this->session->set_flashdata('error', 'Service not found.');
            redirect('admin/services');
            exit;
        }

        $this->call->view('admin/services/form', [
            'title' => 'Edit Service',
            'mode' => 'edit',
            'service' => $service,
            'old' => $this->session->flashdata('old') ?: [],
        ]);
    }

    public function update($id)
    {
        $service = $this->Service_model->find_admin((int) $id);

        if (empty($service)) {
            $this->session->set_flashdata('error', 'Service not found.');
            redirect('admin/services');
            exit;
        }

        $data = $this->serviceInput();
        $errors = $this->validateService($data, (int) $id);

        if (!empty($errors)) {
            $this->redirectWithErrors('admin/services/edit/' . (int) $id, $errors, $data);
        }

        $data['slug'] = $this->uniqueServiceSlug($data['slug'], (int) $id);
        $this->Service_model->update_service((int) $id, $data);

        $this->Audit_log_model->record(auth_user()['id'], 'updated_service', 'service', (int) $id, 'Updated service: ' . $data['name']);
        $this->session->set_flashdata('success', 'Service updated.');
        redirect('admin/services');
        exit;
    }

    public function toggle($id)
    {
        $service = $this->Service_model->find_admin((int) $id);

        if (empty($service)) {
            $this->session->set_flashdata('error', 'Service not found.');
            redirect('admin/services');
            exit;
        }

        $this->Service_model->toggle_service((int) $id);
        $this->Audit_log_model->record(auth_user()['id'], 'toggled_service', 'service', (int) $id, 'Toggled service status: ' . $service['name']);

        $this->session->set_flashdata('success', 'Service status updated.');
        redirect('admin/services');
        exit;
    }

    private function serviceInput()
    {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');

        return [
            'name' => $name,
            'slug' => slugify($slug !== '' ? $slug : $name),
            'description' => trim($_POST['description'] ?? ''),
            'requirements_text' => trim($_POST['requirements_text'] ?? ''),
            'fee' => trim($_POST['fee'] ?? '0'),
            'requires_payment' => isset($_POST['requires_payment']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    private function validateService(array $data, $ignore_id = null)
    {
        $errors = [];

        if ($data['name'] === '') {
            $errors[] = 'Service name is required.';
        }

        if ($data['description'] === '') {
            $errors[] = 'Description is required.';
        }

        if ($data['requirements_text'] === '') {
            $errors[] = 'Requirements are required.';
        }

        if (!is_numeric($data['fee']) || (float) $data['fee'] < 0) {
            $errors[] = 'Fee must be a valid zero or positive amount.';
        }

        if ($this->Service_model->slug_exists($data['slug'], $ignore_id)) {
            $errors[] = 'Service slug is already used.';
        }

        return $errors;
    }

    private function uniqueServiceSlug($slug, $ignore_id = null)
    {
        $base = slugify($slug);
        $candidate = $base;
        $count = 2;

        while ($this->Service_model->slug_exists($candidate, $ignore_id)) {
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
