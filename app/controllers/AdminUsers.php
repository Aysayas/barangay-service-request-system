<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AdminUsers extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('User_model');
        $this->call->model('Audit_log_model');
    }

    public function index()
    {
        $role = trim($_GET['role'] ?? 'all');
        $search = trim($_GET['search'] ?? '');
        $roles = ['resident', 'staff', 'admin'];

        if ($role !== 'all' && !in_array($role, $roles, true)) {
            $role = 'all';
        }

        $this->call->view('admin/users/index', [
            'title' => 'Manage Users',
            'users' => $this->User_model->all_for_admin($role, $search),
            'roles' => $roles,
            'current_role' => $role,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $this->call->view('admin/users/form', [
            'title' => 'Create User',
            'mode' => 'create',
            'user_record' => [],
            'old' => $this->session->flashdata('old') ?: [],
        ]);
    }

    public function store()
    {
        $data = $this->userInput();
        $errors = $this->validateUser($data, true);

        if (!empty($errors)) {
            $this->redirectWithErrors('admin/users/create', $errors, $data);
        }

        $user_id = $this->User_model->create_admin_user($data);
        $this->Audit_log_model->record(auth_user()['id'], 'created_user', 'user', $user_id, 'Created ' . $data['role'] . ' account: ' . $data['email']);

        $this->session->set_flashdata('success', 'User account created.');
        redirect('admin/users');
        exit;
    }

    public function edit($id)
    {
        $user_record = $this->User_model->find((int) $id);

        if (empty($user_record)) {
            $this->session->set_flashdata('error', 'User not found.');
            redirect('admin/users');
            exit;
        }

        $this->call->view('admin/users/form', [
            'title' => 'Edit User',
            'mode' => 'edit',
            'user_record' => $user_record,
            'old' => $this->session->flashdata('old') ?: [],
        ]);
    }

    public function update($id)
    {
        $user_record = $this->User_model->find((int) $id);

        if (empty($user_record)) {
            $this->session->set_flashdata('error', 'User not found.');
            redirect('admin/users');
            exit;
        }

        $data = $this->userInput(false);
        $current_admin = auth_user();

        if ((int) $current_admin['id'] === (int) $id) {
            $data['role'] = 'admin';
            $data['status'] = 'active';
        }

        $errors = $this->validateUser($data, false, (int) $id);

        if (!empty($errors)) {
            $this->redirectWithErrors('admin/users/edit/' . (int) $id, $errors, $data);
        }

        $this->User_model->update_admin_user((int) $id, $data);
        $this->Audit_log_model->record($current_admin['id'], 'updated_user', 'user', (int) $id, 'Updated user account: ' . $data['email']);

        $this->session->set_flashdata('success', 'User account updated.');
        redirect('admin/users');
        exit;
    }

    public function toggle($id)
    {
        $current_admin = auth_user();

        if ((int) $current_admin['id'] === (int) $id) {
            $this->session->set_flashdata('error', 'You cannot deactivate your own admin account.');
            redirect('admin/users');
            exit;
        }

        $user_record = $this->User_model->find((int) $id);

        if (empty($user_record)) {
            $this->session->set_flashdata('error', 'User not found.');
            redirect('admin/users');
            exit;
        }

        $this->User_model->toggle_status((int) $id);
        $this->Audit_log_model->record($current_admin['id'], 'toggled_user', 'user', (int) $id, 'Toggled user status: ' . $user_record['email']);

        $this->session->set_flashdata('success', 'User status updated.');
        redirect('admin/users');
        exit;
    }

    private function userInput($include_password = true)
    {
        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => strtolower(trim($_POST['email'] ?? '')),
            'role' => trim($_POST['role'] ?? 'staff'),
            'status' => trim($_POST['status'] ?? 'active'),
            'contact_number' => trim($_POST['contact_number'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
        ];

        if ($include_password || isset($_POST['password'])) {
            $data['password'] = $_POST['password'] ?? '';
        }

        return $data;
    }

    private function validateUser(array $data, $is_create, $ignore_id = null)
    {
        $errors = [];
        $allowed_roles = $is_create ? ['staff', 'admin'] : ['resident', 'staff', 'admin'];

        if ($data['first_name'] === '') {
            $errors[] = 'First name is required.';
        }

        if ($data['last_name'] === '') {
            $errors[] = 'Last name is required.';
        }

        if ($data['email'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email is required.';
        } elseif ($ignore_id ? $this->User_model->email_exists_except($data['email'], $ignore_id) : $this->User_model->email_exists($data['email'])) {
            $errors[] = 'Email address is already registered.';
        }

        if (!in_array($data['role'], $allowed_roles, true)) {
            $errors[] = 'Choose a valid role.';
        }

        if (!in_array($data['status'], ['active', 'inactive'], true)) {
            $errors[] = 'Choose a valid status.';
        }

        if ($is_create && strlen($data['password'] ?? '') < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if (!$is_create && !empty($data['password']) && strlen($data['password']) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        }

        return $errors;
    }

    private function redirectWithErrors($path, array $errors, array $old)
    {
        unset($old['password']);
        $this->session->set_flashdata('errors', $errors);
        $this->session->set_flashdata('old', $old);
        redirect($path);
        exit;
    }
}
