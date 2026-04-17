<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AdminComplaints extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('Complaint_model');
        $this->call->model('Complaint_attachment_model');
        $this->call->model('Audit_log_model');
    }

    public function index()
    {
        $status = trim($_GET['status'] ?? 'all');
        $search = trim($_GET['search'] ?? '');
        $allowed_statuses = $this->Complaint_model->allowed_statuses();

        if ($status !== 'all' && !in_array($status, $allowed_statuses, true)) {
            $status = 'all';
        }

        $this->call->view('admin/complaints/index', [
            'title' => 'Admin Complaint Oversight',
            'complaints' => $this->Complaint_model->complaint_queue($status, $search),
            'statuses' => $allowed_statuses,
            'current_status' => $status,
            'search' => $search,
        ]);
    }

    public function show($id)
    {
        $complaint = $this->Complaint_model->find_for_staff((int) $id);

        if (empty($complaint)) {
            $this->session->set_flashdata('error', 'Complaint not found.');
            redirect('admin/complaints');
            exit;
        }

        $this->call->view('admin/complaints/show', [
            'title' => 'Admin Complaint Review',
            'complaint' => $complaint,
            'attachments' => $this->Complaint_attachment_model->for_complaint((int) $complaint['id']),
            'audit_logs' => $this->Audit_log_model->for_target('complaint', (int) $complaint['id'], 10),
            'statuses' => $this->Complaint_model->allowed_statuses(),
        ]);
    }

    public function attachment($attachment_id)
    {
        $admin = auth_user();
        $attachment = $this->Complaint_attachment_model->find_for_staff((int) $attachment_id);

        if (empty($attachment)) {
            show_404();
            return;
        }

        $this->Audit_log_model->record(
            (int) $admin['id'],
            'reviewed_complaint_attachment',
            'complaint',
            (int) $attachment['complaint_id'],
            'Admin reviewed complaint evidence file.'
        );

        $this->streamAttachment($attachment);
    }

    private function streamAttachment(array $attachment)
    {
        $real_path = safe_storage_path($attachment['file_path'], 'runtime/uploads/complaints');

        if ($real_path === null) {
            show_404();
            return;
        }

        stream_protected_file($real_path, $attachment['file_type'], $attachment['original_name'], 'inline');
    }
}
