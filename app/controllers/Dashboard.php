<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Dashboard extends Controller
{
    public function index()
    {
        $user = auth_user();
        redirect(dashboard_path_for_role($user['role'] ?? 'resident'));
        exit;
    }

    public function resident()
    {
        $user = auth_user();

        $this->call->database();
        $this->call->model('Service_request_model');

        $this->call->view('dashboard/resident', [
            'title' => 'Resident Dashboard',
            'user' => $user,
            'counts' => $this->Service_request_model->dashboard_counts((int) $user['id']),
            'recent_requests' => $this->Service_request_model->recent_for_user((int) $user['id'], 5),
        ]);
    }

    public function staff()
    {
        $this->call->database();
        $this->call->model('Service_request_model');

        $this->call->view('dashboard/staff', [
            'title' => 'Staff Dashboard',
            'user' => auth_user(),
            'counts' => $this->Service_request_model->staff_counts(),
            'recent_requests' => $this->Service_request_model->recent_for_staff(6),
            'statuses' => $this->Service_request_model->allowed_statuses(),
        ]);
    }

    public function admin()
    {
        $this->call->database();
        $this->call->model('User_model');
        $this->call->model('Service_model');
        $this->call->model('Service_request_model');

        $this->call->view('dashboard/admin', [
            'title' => 'Admin Dashboard',
            'user' => auth_user(),
            'user_counts' => $this->User_model->admin_counts(),
            'service_counts' => $this->Service_model->admin_counts(),
            'request_counts' => $this->Service_request_model->admin_counts(),
            'recent_users' => $this->User_model->recent_for_admin(5),
            'recent_requests' => $this->Service_request_model->recent_for_staff(5),
        ]);
    }
}
