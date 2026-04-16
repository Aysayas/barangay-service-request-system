<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Auth_middleware
{
    public function handle($next)
    {
        require_once SYSTEM_DIR . 'helpers/url_helper.php';

        $session = load_class('session', 'libraries');

        if (empty($session->userdata('user'))) {
            $session->set_flashdata('error', 'Please log in first.');
            redirect('login');
            exit;
        }

        return $next();
    }
}
