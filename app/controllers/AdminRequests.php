<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AdminRequests extends Controller
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
        $this->call->library('Pdf_service');
    }

    public function index()
    {
        $status = trim($_GET['status'] ?? 'all');
        $search = trim($_GET['search'] ?? '');
        $allowed_statuses = $this->Service_request_model->allowed_statuses();

        if ($status !== 'all' && !in_array($status, $allowed_statuses, true)) {
            $status = 'all';
        }

        $this->call->view('admin/requests/index', [
            'title' => 'Admin Request Review',
            'requests' => $this->Service_request_model->request_queue($status, $search),
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
            redirect('admin/requests');
            exit;
        }

        $this->call->view('admin/requests/show', [
            'title' => 'Admin Request Review',
            'request' => $request,
            'attachments' => $this->Request_attachment_model->for_request((int) $request['id']),
            'final_document' => $this->Request_final_document_model->find_for_request((int) $request['id']),
            'payment' => ((int) $request['requires_payment'] === 1) ? $this->Payment_model->find_for_request((int) $request['id']) : null,
            'audit_logs' => $this->Audit_log_model->for_target('service_request', (int) $request['id'], 8),
            'max_upload_mb' => 10,
        ]);
    }

    public function pdf($id)
    {
        $request = $this->Service_request_model->find_for_staff((int) $id);

        if (empty($request)) {
            $this->session->set_flashdata('error', 'Request not found.');
            redirect('admin/requests');
            exit;
        }

        $final_document = $this->Request_final_document_model->find_for_request((int) $request['id']);
        $final_document_exists = !empty($final_document['file_path'])
            && safe_storage_path($final_document['file_path'], 'runtime/uploads/final_documents') !== null;
        $payment = ((int) $request['requires_payment'] === 1)
            ? $this->Payment_model->find_for_request((int) $request['id'])
            : null;

        try {
            $this->Pdf_service->download('pdf/staff_request_case', [
                'request' => $request,
                'attachments' => $this->Request_attachment_model->for_request((int) $request['id']),
                'final_document' => $final_document,
                'final_document_exists' => $final_document_exists,
                'payment' => $payment,
                'audit_logs' => $this->Audit_log_model->for_target('service_request', (int) $request['id'], 8),
                'case_scope' => 'Admin Request Case',
            ], 'request_case_' . $request['reference_no'] . '.pdf');
        } catch (Throwable $e) {
            $this->session->set_flashdata('error', 'The request case PDF could not be generated right now.');
            redirect('admin/requests/' . (int) $request['id']);
            exit;
        }
    }
}
