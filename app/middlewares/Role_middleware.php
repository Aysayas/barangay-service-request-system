<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Role_middleware
{
    private $allowed_roles = [];

    public function __construct(array $allowed_roles)
    {
        $this->allowed_roles = $allowed_roles;
    }

    public function handle($next)
    {
        require_once SYSTEM_DIR . 'helpers/url_helper.php';

        $session = load_class('session', 'libraries');
        $user = $session->userdata('user');
        $role = $user['role'] ?? null;

        if (empty($user)) {
            $session->set_flashdata('error', 'Please log in first.');
            redirect('login');
            exit;
        }

        if (!in_array($role, $this->allowed_roles, true)) {
            $session->set_flashdata('error', 'You do not have access to that page.');
            redirect($this->dashboardPath($role));
            exit;
        }

        return $next();
    }

    private function dashboardPath($role)
    {
        if ($role === 'admin') {
            return 'admin/dashboard';
        }

        if ($role === 'staff') {
            return 'staff/dashboard';
        }

        return 'resident/dashboard';
    }
}
