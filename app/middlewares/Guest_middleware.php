<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Guest_middleware
{
    public function handle($next)
    {
        require_once SYSTEM_DIR . 'helpers/url_helper.php';

        $session = load_class('session', 'libraries');
        $user = $session->userdata('user');

        if (!empty($user)) {
            redirect($this->dashboardPath($user['role'] ?? 'resident'));
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
