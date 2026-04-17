<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class StaffComplaints extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('Complaint_model');
        $this->call->model('Complaint_attachment_model');
        $this->call->model('Audit_log_model');
        $this->call->model('User_model');
        $this->call->library('Notification_service');
    }

    public function index()
    {
        $status = trim($_GET['status'] ?? 'all');
        $search = trim($_GET['search'] ?? '');
        $allowed_statuses = $this->Complaint_model->allowed_statuses();

        if ($status !== 'all' && !in_array($status, $allowed_statuses, true)) {
            $status = 'all';
        }

        $this->call->view('staff/complaints/index', [
            'title' => 'Staff Complaint Queue',
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
            redirect('staff/complaints');
            exit;
        }

        $this->call->view('staff/complaints/show', [
            'title' => 'Review Complaint',
            'complaint' => $complaint,
            'attachments' => $this->Complaint_attachment_model->for_complaint((int) $complaint['id']),
            'statuses' => $this->Complaint_model->allowed_statuses(),
            'priorities' => $this->Complaint_model->allowed_priorities(),
            'staff_users' => $this->User_model->active_staff_options(),
            'audit_logs' => $this->Audit_log_model->for_target('complaint', (int) $complaint['id'], 10),
        ]);
    }

    public function update($id)
    {
        $staff = auth_user();
        $complaint = $this->Complaint_model->find_for_staff((int) $id);

        if (empty($complaint)) {
            $this->session->set_flashdata('error', 'Complaint not found.');
            redirect('staff/complaints');
            exit;
        }

        $staff_users = $this->User_model->active_staff_options();
        $staff_ids = array_map('intval', array_column($staff_users, 'id'));

        $data = [
            'status' => trim($_POST['status'] ?? ''),
            'priority' => trim($_POST['priority'] ?? ''),
            'staff_notes' => trim($_POST['staff_notes'] ?? ''),
            'resolution_notes' => trim($_POST['resolution_notes'] ?? ''),
            'assigned_to' => (int) ($_POST['assigned_to'] ?? 0),
        ];

        $errors = $this->validateReviewInput($data, $staff_ids);

        if (!empty($errors)) {
            $this->session->set_flashdata('errors', $errors);
            redirect('staff/complaints/' . (int) $id);
            exit;
        }

        $old_status = $complaint['status'];
        $old_priority = $complaint['priority'];
        $old_staff_notes = trim((string) ($complaint['staff_notes'] ?? ''));
        $old_resolution_notes = trim((string) ($complaint['resolution_notes'] ?? ''));
        $old_assigned_to = (int) ($complaint['assigned_to'] ?? 0);

        $this->Complaint_model->update_staff_review((int) $id, $data);

        if ($old_status !== $data['status']) {
            $this->Audit_log_model->record(
                (int) $staff['id'],
                'updated_complaint_status',
                'complaint',
                (int) $id,
                'Changed complaint status from ' . complaint_status_label($old_status) . ' to ' . complaint_status_label($data['status']) . '.'
            );
        }

        if ($old_priority !== $data['priority']) {
            $this->Audit_log_model->record(
                (int) $staff['id'],
                'updated_complaint_priority',
                'complaint',
                (int) $id,
                'Changed complaint priority from ' . complaint_priority_label($old_priority) . ' to ' . complaint_priority_label($data['priority']) . '.'
            );
        }

        if ($old_staff_notes !== $data['staff_notes']) {
            $this->Audit_log_model->record(
                (int) $staff['id'],
                'updated_complaint_notes',
                'complaint',
                (int) $id,
                'Updated complaint staff notes.'
            );
        }

        if ($old_resolution_notes !== $data['resolution_notes']) {
            $this->Audit_log_model->record(
                (int) $staff['id'],
                'updated_complaint_resolution',
                'complaint',
                (int) $id,
                'Updated complaint resolution notes.'
            );
        }

        if ($old_assigned_to !== (int) $data['assigned_to']) {
            $this->Audit_log_model->record(
                (int) $staff['id'],
                'updated_complaint_assignment',
                'complaint',
                (int) $id,
                'Updated complaint assignment.'
            );
        }

        if ($old_status !== $data['status'] && in_array($data['status'], ['resolved', 'closed'], true)) {
            $this->Notification_service->complaint_closed($complaint, $data['status']);
        }

        $this->session->set_flashdata('success', 'Complaint review was updated.');
        redirect('staff/complaints/' . (int) $id);
        exit;
    }

    public function attachment($attachment_id)
    {
        $staff = auth_user();
        $attachment = $this->Complaint_attachment_model->find_for_staff((int) $attachment_id);

        if (empty($attachment)) {
            show_404();
            return;
        }

        $this->Audit_log_model->record(
            (int) $staff['id'],
            'reviewed_complaint_attachment',
            'complaint',
            (int) $attachment['complaint_id'],
            'Reviewed complaint evidence file.'
        );

        $this->streamAttachment($attachment);
    }

    private function validateReviewInput(array $data, array $staff_ids)
    {
        $errors = [];

        if (!in_array($data['status'], $this->Complaint_model->allowed_statuses(), true)) {
            $errors[] = 'Choose a valid complaint status.';
        }

        if (!in_array($data['priority'], $this->Complaint_model->allowed_priorities(), true)) {
            $errors[] = 'Choose a valid priority.';
        }

        if (!empty($data['assigned_to']) && !in_array((int) $data['assigned_to'], $staff_ids, true)) {
            $errors[] = 'Choose a valid staff assignee.';
        }

        if (strlen($data['staff_notes']) > 3000) {
            $errors[] = 'Staff notes must be 3000 characters or fewer.';
        }

        if (strlen($data['resolution_notes']) > 3000) {
            $errors[] = 'Resolution notes must be 3000 characters or fewer.';
        }

        if (in_array($data['status'], ['resolved', 'closed', 'dismissed'], true) && $data['resolution_notes'] === '') {
            $errors[] = 'Resolution notes are required before marking a complaint resolved, closed, or dismissed.';
        }

        return $errors;
    }

    private function streamAttachment(array $attachment)
    {
        $path = ROOT_DIR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $attachment['file_path']);
        $upload_root = realpath(ROOT_DIR . 'runtime/uploads/complaints');
        $real_path = realpath($path);

        if ($upload_root === false || $real_path === false || strpos($real_path, $upload_root) !== 0 || !is_file($real_path)) {
            show_404();
            return;
        }

        $filename = str_replace(['"', "\r", "\n"], '', basename($attachment['original_name']));

        while (ob_get_level() > 0) {
            @ob_end_clean();
        }

        header('Content-Type: ' . $attachment['file_type']);
        header('Content-Length: ' . filesize($real_path));
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('X-Content-Type-Options: nosniff');
        readfile($real_path);
        exit;
    }
}
