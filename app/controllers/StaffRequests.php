<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class StaffRequests extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('Service_request_model');
        $this->call->model('Request_attachment_model');
        $this->call->model('Request_final_document_model');
        $this->call->model('Payment_model');
        $this->call->model('Audit_log_model');
        $this->call->library('Notification_service');
    }

    public function index()
    {
        $status = trim($_GET['status'] ?? 'all');
        $search = trim($_GET['search'] ?? '');
        $allowed_statuses = $this->Service_request_model->allowed_statuses();

        if ($status !== 'all' && !in_array($status, $allowed_statuses, true)) {
            $status = 'all';
        }

        $this->call->view('staff/requests/index', [
            'title' => 'Staff Request Queue',
            'requests' => $this->Service_request_model->staff_queue($status, $search),
            'statuses' => $allowed_statuses,
            'current_status' => $status,
            'search' => $search,
        ]);
    }

    public function show($id)
    {
        $request = $this->Service_request_model->find_for_staff((int) $id);

        if (empty($request)) {
            $this->session->set_flashdata('error', 'Request not found.');
            redirect('staff/requests');
            exit;
        }

        $this->call->view('staff/requests/show', [
            'title' => 'Review Request',
            'request' => $request,
            'attachments' => $this->Request_attachment_model->for_request((int) $request['id']),
            'final_document' => $this->Request_final_document_model->find_for_request((int) $request['id']),
            'payment' => ((int) $request['requires_payment'] === 1) ? $this->Payment_model->find_for_request((int) $request['id']) : null,
            'payment_review_statuses' => $this->Payment_model->review_statuses(),
            'statuses' => $this->Service_request_model->allowed_statuses(),
            'audit_logs' => $this->Audit_log_model->for_target('service_request', (int) $request['id'], 8),
            'max_upload_mb' => 10,
        ]);
    }

    public function update($id)
    {
        $staff = auth_user();
        $request = $this->Service_request_model->find_for_staff((int) $id);

        if (empty($request)) {
            $this->session->set_flashdata('error', 'Request not found.');
            redirect('staff/requests');
            exit;
        }

        $status = trim($_POST['status'] ?? '');
        $staff_notes = trim($_POST['staff_notes'] ?? '');
        $allowed_statuses = $this->Service_request_model->allowed_statuses();
        $errors = [];

        if (!in_array($status, $allowed_statuses, true)) {
            $errors[] = 'Choose a valid request status.';
        }

        if (empty($errors) && !request_status_transition_allowed($request['status'], $status)) {
            $errors[] = request_status_transition_message($request['status'], $status);
        }

        if (strlen($staff_notes) > 2000) {
            $errors[] = 'Staff notes must be 2000 characters or fewer.';
        }

        if ((int) $request['requires_payment'] === 1 && in_array($status, ['approved', 'ready_for_pickup', 'released'], true)) {
            $payment = $this->Payment_model->find_for_request((int) $id);

            if (empty($payment) || $payment['payment_status'] !== 'payment_verified') {
                $errors[] = 'Verify the payment proof before moving this paid request to an approved, pickup, or released status.';
            }
        }

        if ($request['status'] !== $status && in_array($status, ['ready_for_pickup', 'released'], true)) {
            $final_document = $this->Request_final_document_model->find_for_request((int) $id);

            if (empty($final_document)) {
                $errors[] = 'Upload the final document before moving this request to ' . status_label($status) . '.';
            }
        }

        if (!empty($errors)) {
            $this->session->set_flashdata('errors', $errors);
            redirect('staff/requests/' . (int) $id);
            exit;
        }

        $old_status = $request['status'];
        $old_notes = trim((string) ($request['staff_notes'] ?? ''));

        $this->Service_request_model->update_staff_review((int) $id, $status, $staff_notes, (int) $staff['id']);

        if ($old_status !== $status) {
            $this->Audit_log_model->record(
                (int) $staff['id'],
                'changed_status',
                'service_request',
                (int) $id,
                'Changed status from ' . status_label($old_status) . ' to ' . status_label($status) . '.'
            );
        }

        if ($old_notes !== $staff_notes) {
            $this->Audit_log_model->record(
                (int) $staff['id'],
                'updated_staff_notes',
                'service_request',
                (int) $id,
                'Updated staff notes.'
            );
        }

        if ($old_status !== $status && $status === 'approved') {
            $this->Notification_service->request_approved($request);
        }

        $this->session->set_flashdata('success', 'Request review was updated.');
        redirect('staff/requests/' . (int) $id);
        exit;
    }

    public function attachment($attachment_id)
    {
        $attachment = $this->Request_attachment_model->find_for_staff((int) $attachment_id);

        if (empty($attachment)) {
            show_404();
            return;
        }

        $real_path = safe_storage_path($attachment['file_path'], 'runtime/uploads/resident_requests');

        if ($real_path === null) {
            show_404();
            return;
        }

        stream_protected_file($real_path, $attachment['file_type'], $attachment['original_name'], 'inline');
    }
}
