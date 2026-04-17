<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

require_once SYSTEM_DIR . 'libraries/Upload.php';

class FinalDocuments extends Controller
{
    private $max_upload_mb = 10;

    private $allowed_extensions = ['pdf', 'doc', 'docx'];

    private $allowed_mimes = [
        'application/pdf',
        'application/msword',
        'application/vnd.ms-office',
        'application/vnd.ms-word',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/x-cfb',
        'application/cdfv2',
        'application/zip',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('Service_request_model');
        $this->call->model('Request_final_document_model');
        $this->call->model('Payment_model');
        $this->call->model('Audit_log_model');
        $this->call->library('Notification_service');
    }

    public function staffUpload($id)
    {
        $this->uploadForInternalRole((int) $id, 'staff', 'staff/requests/' . (int) $id);
    }

    public function adminUpload($id)
    {
        $this->uploadForInternalRole((int) $id, 'admin', 'admin/requests/' . (int) $id);
    }

    public function staffDownload($id)
    {
        $this->downloadForInternalRole((int) $id, 'staff', 'staff/requests/' . (int) $id);
    }

    public function adminDownload($id)
    {
        $this->downloadForInternalRole((int) $id, 'admin', 'admin/requests/' . (int) $id);
    }

    public function residentDownload($id)
    {
        $resident = auth_user();
        $request = $this->Service_request_model->find_for_user((int) $id, (int) $resident['id']);

        if (empty($request)) {
            $this->session->set_flashdata('error', 'Request not found.');
            redirect('resident/requests');
            exit;
        }

        $document = $this->Request_final_document_model->find_for_resident((int) $id, (int) $resident['id']);

        if (empty($document)) {
            $this->session->set_flashdata('error', 'No final document is available yet.');
            redirect('resident/requests/' . (int) $id);
            exit;
        }

        if (!final_document_download_allowed($request['status'])) {
            $this->session->set_flashdata('error', 'The final document is not ready for resident download yet.');
            redirect('resident/requests/' . (int) $id);
            exit;
        }

        $path = $this->safeFinalDocumentPath($document['file_path']);

        if ($path === null) {
            $this->session->set_flashdata('error', 'The final document file could not be found.');
            redirect('resident/requests/' . (int) $id);
            exit;
        }

        $this->Audit_log_model->record(
            (int) $resident['id'],
            'downloaded_final_document',
            'service_request',
            (int) $id,
            'Resident downloaded final document for ' . $request['reference_no'] . '.'
        );

        $this->streamFile($path, $document);
    }

    private function uploadForInternalRole($request_id, $role, $redirect_path)
    {
        $user = auth_user();
        $request = $this->Service_request_model->find_for_staff((int) $request_id);

        if (empty($request)) {
            $this->session->set_flashdata('error', 'Request not found.');
            redirect($role . '/dashboard');
            exit;
        }

        $payment = ((int) $request['requires_payment'] === 1)
            ? $this->Payment_model->find_for_request((int) $request_id)
            : null;

        if (!final_document_upload_allowed($request, $payment)) {
            $this->session->set_flashdata('error', final_document_block_reason($request, $payment));
            redirect($redirect_path);
            exit;
        }

        $file = $_FILES['final_document'] ?? [];
        $errors = $this->validateFinalDocument($file);

        if (!empty($errors)) {
            $this->session->set_flashdata('errors', $errors);
            redirect($redirect_path);
            exit;
        }

        $existing_document = $this->Request_final_document_model->find_for_request((int) $request_id);
        $upload_dir = ROOT_DIR . 'runtime/uploads/final_documents/request_' . (int) $request_id;
        $upload = new Upload($file);
        $upload
            ->set_dir($upload_dir)
            ->allowed_extensions($this->allowed_extensions)
            ->allowed_mimes($this->allowed_mimes)
            ->max_size($this->max_upload_mb)
            ->encrypt_name();

        if (!$upload->do_upload()) {
            $this->session->set_flashdata('errors', $upload->get_errors());
            redirect($redirect_path);
            exit;
        }

        $stored_name = $upload->get_filename();
        $new_absolute_path = $upload_dir . DIRECTORY_SEPARATOR . $stored_name;
        $relative_path = 'runtime/uploads/final_documents/request_' . (int) $request_id . '/' . $stored_name;
        $action = empty($existing_document) ? 'uploaded_final_document' : 'replaced_final_document';
        $description = (empty($existing_document) ? 'Uploaded' : 'Replaced') . ' final document for ' . $request['reference_no'] . '.';

        try {
            $this->db->transaction();

            $this->Request_final_document_model->save_for_request((int) $request_id, [
                'original_name' => sanitize_filename($file['name']),
                'stored_name' => $stored_name,
                'file_path' => $relative_path,
                'file_size' => (int) $upload->get_size(),
                'file_type' => $upload->mime,
                'uploaded_by' => (int) $user['id'],
            ]);

            $this->Audit_log_model->record(
                (int) $user['id'],
                $action,
                'service_request',
                (int) $request_id,
                $description
            );

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->roll_back();
            $this->deleteFileIfSafe($new_absolute_path);

            $this->session->set_flashdata('error', 'Final document could not be saved.');
            redirect($redirect_path);
            exit;
        }

        if (!empty($existing_document['file_path'])) {
            $old_path = $this->safeFinalDocumentPath($existing_document['file_path']);

            if ($old_path !== null && $old_path !== realpath($new_absolute_path)) {
                @unlink($old_path);
            }
        }

        $this->Notification_service->final_document_available($request);

        $this->session->set_flashdata('success', empty($existing_document) ? 'Final document uploaded.' : 'Final document replaced.');
        redirect($redirect_path);
        exit;
    }

    private function downloadForInternalRole($request_id, $role, $redirect_path)
    {
        $user = auth_user();
        $request = $this->Service_request_model->find_for_staff((int) $request_id);

        if (empty($request)) {
            $this->session->set_flashdata('error', 'Request not found.');
            redirect($role . '/dashboard');
            exit;
        }

        $document = $this->Request_final_document_model->find_for_request((int) $request_id);

        if (empty($document)) {
            $this->session->set_flashdata('error', 'No final document has been uploaded yet.');
            redirect($redirect_path);
            exit;
        }

        $path = $this->safeFinalDocumentPath($document['file_path']);

        if ($path === null) {
            $this->session->set_flashdata('error', 'The final document file could not be found.');
            redirect($redirect_path);
            exit;
        }

        $this->Audit_log_model->record(
            (int) $user['id'],
            'downloaded_final_document_internal',
            'service_request',
            (int) $request_id,
            ucfirst($role) . ' downloaded final document for ' . $request['reference_no'] . '.'
        );

        $this->streamFile($path, $document);
    }

    private function validateFinalDocument(array $file)
    {
        $errors = [];

        if (empty($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['Choose a final document file to upload.'];
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return ['The uploaded final document could not be read.'];
        }

        $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));

        if (!in_array($extension, $this->allowed_extensions, true)) {
            $errors[] = 'Allowed final document types are PDF, DOC, and DOCX.';
        }

        if (($file['size'] ?? 0) > ($this->max_upload_mb * 1024 * 1024)) {
            $errors[] = 'Final document must be ' . $this->max_upload_mb . 'MB or smaller.';
        }

        return $errors;
    }

    private function safeFinalDocumentPath($relative_path)
    {
        $storage_root = realpath(ROOT_DIR . 'runtime/uploads/final_documents');

        if ($storage_root === false) {
            return null;
        }

        $absolute_path = ROOT_DIR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative_path);
        $real_path = realpath($absolute_path);
        $storage_root = rtrim($storage_root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if ($real_path === false || strpos($real_path, $storage_root) !== 0 || !is_file($real_path)) {
            return null;
        }

        return $real_path;
    }

    private function deleteFileIfSafe($absolute_path)
    {
        $storage_root = realpath(ROOT_DIR . 'runtime/uploads/final_documents');
        $real_path = realpath($absolute_path);

        if ($storage_root === false || $real_path === false) {
            return;
        }

        $storage_root = rtrim($storage_root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (strpos($real_path, $storage_root) === 0 && is_file($real_path)) {
            @unlink($real_path);
        }
    }

    private function streamFile($path, array $document)
    {
        $filename = basename($document['original_name'] ?: 'final-document');
        $filename = str_replace(['"', "\r", "\n"], '', $filename);

        while (ob_get_level() > 0) {
            @ob_end_clean();
        }

        header('Content-Type: ' . $document['file_type']);
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('X-Content-Type-Options: nosniff');
        readfile($path);
        exit;
    }
}
