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
        $this->call->model('Complaint_model');
        $this->call->model('Announcement_model');
        $this->call->model('Community_post_model');

        $this->call->view('dashboard/resident', [
            'title' => 'Resident Dashboard',
            'user' => $user,
            'counts' => $this->Service_request_model->dashboard_counts((int) $user['id']),
            'next_action_counts' => $this->Service_request_model->resident_next_action_counts((int) $user['id']),
            'recent_requests' => $this->Service_request_model->recent_for_user((int) $user['id'], 5),
            'complaint_counts' => $this->Complaint_model->resident_counts((int) $user['id']),
            'recent_complaints' => $this->Complaint_model->recent_for_user((int) $user['id'], 5),
            'latest_announcements' => $this->Announcement_model->published(3),
            'latest_community_posts' => $this->Community_post_model->published('all', 3),
        ]);
    }

    public function staff()
    {
        $this->call->database();
        $this->call->model('Service_request_model');
        $this->call->model('Complaint_model');
        $this->call->model('Payment_model');

        $this->call->view('dashboard/staff', [
            'title' => 'Staff Dashboard',
            'user' => auth_user(),
            'counts' => $this->Service_request_model->staff_counts(),
            'recent_requests' => $this->Service_request_model->recent_for_staff(6),
            'statuses' => $this->Service_request_model->allowed_statuses(),
            'complaint_counts' => $this->Complaint_model->staff_counts(),
            'recent_complaints' => $this->Complaint_model->recent_for_staff(6),
            'payment_review_count' => $this->Payment_model->awaiting_review_count(),
        ]);
    }

    public function admin()
    {
        $this->call->database();
        $this->call->model('User_model');
        $this->call->model('Service_model');
        $this->call->model('Service_request_model');
        $this->call->model('Complaint_model');
        $this->call->model('Announcement_model');
        $this->call->model('Community_post_model');
        $this->call->model('Audit_log_model');

        $published_announcements = $this->Announcement_model->published_count();
        $published_community_posts = $this->Community_post_model->published_count();

        $this->call->view('dashboard/admin', [
            'title' => 'Admin Dashboard',
            'user' => auth_user(),
            'user_counts' => $this->User_model->admin_counts(),
            'service_counts' => $this->Service_model->admin_counts(),
            'request_counts' => $this->Service_request_model->admin_counts(),
            'complaint_counts' => $this->Complaint_model->admin_counts(),
            'content_counts' => [
                'published_announcements' => $published_announcements,
                'published_community_posts' => $published_community_posts,
                'published_content_total' => $published_announcements + $published_community_posts,
            ],
            'recent_users' => $this->User_model->recent_for_admin(5),
            'recent_requests' => $this->Service_request_model->recent_for_staff(5),
            'recent_complaints' => $this->Complaint_model->recent_for_staff(5),
            'recent_audit_logs' => $this->Audit_log_model->recent_for_dashboard(5),
        ]);
    }
}
