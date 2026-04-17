<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Auth extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function loginForm()
    {
        $this->call->view('auth/login', [
            'title' => 'Login',
            'old' => $this->session->flashdata('old') ?: [],
        ]);
    }

    public function login()
    {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $errors = [];

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email address.';
        }

        if ($password === '') {
            $errors[] = 'Enter your password.';
        }

        if (!empty($errors)) {
            $this->redirectWithErrors('login', $errors, ['email' => $email]);
        }

        $this->loadUserModel();

        $user = $this->User_model->find_by_email($email);

        if (empty($user) || !password_verify($password, $user['password'])) {
            $this->redirectWithErrors('login', ['Email or password is incorrect.'], ['email' => $email]);
        }

        if ($user['status'] !== 'active') {
            $this->redirectWithErrors('login', ['This account is inactive. Please contact the barangay office.'], ['email' => $email]);
        }

        $this->session->after_successful_login();
        $this->session->set_userdata('user', [
            'id' => (int) $user['id'],
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ]);

        redirect(dashboard_path_for_role($user['role']));
        exit;
    }

    public function registerForm()
    {
        $this->call->view('auth/register', [
            'title' => 'Resident Registration',
            'old' => $this->session->flashdata('old') ?: [],
        ]);
    }

    public function register()
    {
        $this->loadUserModel();

        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => strtolower(trim($_POST['email'] ?? '')),
            'contact_number' => trim($_POST['contact_number'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
        ];

        $errors = $this->validateRegistration($data);

        if (!empty($errors)) {
            unset($data['password'], $data['password_confirm']);
            $this->redirectWithErrors('register', $errors, $data);
        }

        $user_id = $this->User_model->create_resident($data);

        if (!$user_id) {
            unset($data['password'], $data['password_confirm']);
            $this->redirectWithErrors('register', ['Registration failed. Please try again.'], $data);
        }

        $this->call->library('Notification_service');
        $this->Notification_service->resident_registered($data);

        $this->session->set_flashdata('success', 'Your resident account is ready. You can log in now.');
        redirect('login');
        exit;
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('login');
        exit;
    }

    private function validateRegistration(array $data)
    {
        $errors = [];

        if ($data['first_name'] === '' || !preg_match("/^[\p{L} .'-]+$/u", $data['first_name'])) {
            $errors[] = 'Enter a valid first name.';
        }

        if ($data['last_name'] === '' || !preg_match("/^[\p{L} .'-]+$/u", $data['last_name'])) {
            $errors[] = 'Enter a valid last name.';
        }

        if ($data['email'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Enter a valid email address.';
        } elseif ($this->User_model->email_exists($data['email'])) {
            $errors[] = 'That email address is already registered.';
        }

        if ($data['contact_number'] !== '' && !preg_match('/^[0-9+\-\s().]{7,20}$/', $data['contact_number'])) {
            $errors[] = 'Enter a valid contact number.';
        }

        if ($data['address'] === '') {
            $errors[] = 'Enter your address.';
        }

        if (strlen($data['password']) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Password confirmation does not match.';
        }

        return $errors;
    }

    private function loadUserModel()
    {
        $this->call->database();
        $this->call->model('User_model');
    }

    private function redirectWithErrors($path, array $errors, array $old = [])
    {
        $this->session->set_flashdata('errors', $errors);
        $this->session->set_flashdata('old', $old);
        redirect($path);
        exit;
    }
}
