<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

require_once SYSTEM_DIR . 'libraries/Upload.php';

class ResidentRequests extends Controller
{
    private $max_upload_mb = 5;

    private $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

    private $allowed_mimes = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/zip',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('Service_model');
        $this->call->model('Service_request_model');
        $this->call->model('Request_attachment_model');
        $this->call->model('Request_final_document_model');
        $this->call->model('Payment_model');
    }

    public function services()
    {
        $this->call->view('resident/services/index', [
            'title' => 'Available Services',
            'services' => $this->Service_model->active_services(),
        ]);
    }

    public function index()
    {
        $user = auth_user();

        $this->call->view('resident/requests/index', [
            'title' => 'My Requests',
            'requests' => $this->Service_request_model->for_user($user['id']),
        ]);
    }

    public function create($service_id = null)
    {
        $selected_service = null;

        if (!empty($service_id)) {
            $selected_service = $this->Service_model->find_active((int) $service_id);

            if (empty($selected_service)) {
                $this->session->set_flashdata('error', 'The selected service is not available.');
                redirect('resident/services');
                exit;
            }
        }

        $this->call->view('resident/requests/create', [
            'title' => 'New Service Request',
            'services' => $this->Service_model->active_services(),
            'selected_service' => $selected_service,
            'old' => $this->session->flashdata('old') ?: [],
            'max_upload_mb' => $this->max_upload_mb,
        ]);
    }

    public function store()
    {
        $user = auth_user();
        $files = $this->normalizeFiles($_FILES['attachments'] ?? []);

        $data = [
            'service_id' => (int) ($_POST['service_id'] ?? 0),
            'purpose' => trim($_POST['purpose'] ?? ''),
            'remarks' => trim($_POST['remarks'] ?? ''),
        ];

        $service = $this->Service_model->find_active($data['service_id']);
        $errors = $this->validateRequestInput($data, $files, $service);

        if (!empty($errors)) {
            $this->redirectBackToForm($data['service_id'], $errors, $data);
        }

        $moved_files = [];

        try {
            $this->db->transaction();

            $request_id = $this->Service_request_model->create_request([
                'user_id' => (int) $user['id'],
                'service_id' => (int) $service['id'],
                'reference_no' => $this->generateReferenceNo(),
                'purpose' => $data['purpose'],
                'remarks' => $data['remarks'] !== '' ? $data['remarks'] : null,
            ]);

            if ((int) $service['requires_payment'] === 1) {
                $this->Payment_model->create_pending_for_request((int) $request_id, (float) $service['fee']);
            }

            $upload_errors = $this->saveAttachments($request_id, $files, $moved_files);

            if (!empty($upload_errors)) {
                throw new RuntimeException(implode(' ', $upload_errors));
            }

            $this->db->commit();

            $this->session->set_flashdata('success', 'Your service request was submitted.');
            redirect('resident/requests/' . $request_id);
            exit;
        } catch (Throwable $e) {
            $this->db->roll_back();
            $this->deleteMovedFiles($moved_files);

            $this->redirectBackToForm($data['service_id'], [
                'We could not submit the request. ' . $e->getMessage(),
            ], $data);
        }
    }

    public function show($id)
    {
        $user = auth_user();
        $request = $this->Service_request_model->find_for_user((int) $id, (int) $user['id']);

        if (empty($request)) {
            $this->session->set_flashdata('error', 'Request not found.');
            redirect('resident/requests');
            exit;
        }

        $this->call->view('resident/requests/show', [
            'title' => 'Request Details',
            'request' => $request,
            'attachments' => $this->Request_attachment_model->for_request((int) $request['id']),
            'final_document' => $this->Request_final_document_model->find_for_request((int) $request['id']),
            'can_download_final_document' => final_document_download_allowed($request['status']),
            'payment' => ((int) $request['requires_payment'] === 1) ? $this->Payment_model->find_for_request((int) $request['id']) : null,
            'payment_methods' => $this->Payment_model->allowed_methods(),
            'max_payment_upload_mb' => 5,
        ]);
    }

    private function validateRequestInput(array $data, array $files, $service)
    {
        $errors = [];

        if (empty($service)) {
            $errors[] = 'Choose a valid service.';
        }

        if ($data['purpose'] === '') {
            $errors[] = 'Purpose is required.';
        } elseif (strlen($data['purpose']) > 1000) {
            $errors[] = 'Purpose must be 1000 characters or fewer.';
        }

        if (strlen($data['remarks']) > 1000) {
            $errors[] = 'Remarks must be 1000 characters or fewer.';
        }

        if (!$this->hasUploadedFiles($files)) {
            $errors[] = 'Upload at least one requirement file.';
        }

        foreach ($files as $file) {
            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                $errors[] = 'One of the uploaded files could not be read.';
                continue;
            }

            $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));

            if (!in_array($extension, $this->allowed_extensions, true)) {
                $errors[] = 'Allowed file types are PDF, JPG, PNG, DOC, and DOCX.';
            }

            if (($file['size'] ?? 0) > ($this->max_upload_mb * 1024 * 1024)) {
                $errors[] = 'Each file must be ' . $this->max_upload_mb . 'MB or smaller.';
            }
        }

        return array_values(array_unique($errors));
    }

    private function saveAttachments($request_id, array $files, array &$moved_files)
    {
        $errors = [];
        $upload_dir = ROOT_DIR . 'runtime/uploads/resident_requests/request_' . (int) $request_id;

        foreach ($files as $file) {
            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $upload = new Upload($file);
            $upload
                ->set_dir($upload_dir)
                ->allowed_extensions($this->allowed_extensions)
                ->allowed_mimes($this->allowed_mimes)
                ->max_size($this->max_upload_mb)
                ->encrypt_name();

            if (!$upload->do_upload()) {
                $errors = array_merge($errors, $upload->get_errors());
                continue;
            }

            $stored_name = $upload->get_filename();
            $absolute_path = $upload_dir . DIRECTORY_SEPARATOR . $stored_name;
            $relative_path = 'runtime/uploads/resident_requests/request_' . (int) $request_id . '/' . $stored_name;
            $moved_files[] = $absolute_path;

            $this->Request_attachment_model->create_attachment([
                'request_id' => (int) $request_id,
                'original_name' => sanitize_filename($file['name']),
                'stored_name' => $stored_name,
                'file_path' => $relative_path,
                'file_size' => (int) $upload->get_size(),
                'file_type' => $upload->mime,
            ]);
        }

        return $errors;
    }

    private function generateReferenceNo()
    {
        do {
            $reference = 'BRGY-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        } while ($this->Service_request_model->reference_exists($reference));

        return $reference;
    }

    private function normalizeFiles(array $files)
    {
        if (empty($files) || !isset($files['name'])) {
            return [];
        }

        if (!is_array($files['name'])) {
            return [$files];
        }

        $normalized = [];

        foreach ($files['name'] as $index => $name) {
            $normalized[] = [
                'name' => $name,
                'type' => $files['type'][$index] ?? '',
                'tmp_name' => $files['tmp_name'][$index] ?? '',
                'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$index] ?? 0,
            ];
        }

        return $normalized;
    }

    private function hasUploadedFiles(array $files)
    {
        foreach ($files as $file) {
            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                return true;
            }
        }

        return false;
    }

    private function redirectBackToForm($service_id, array $errors, array $old)
    {
        $path = !empty($service_id)
            ? 'resident/requests/create/' . (int) $service_id
            : 'resident/requests/create';

        $this->session->set_flashdata('errors', $errors);
        $this->session->set_flashdata('old', $old);
        redirect($path);
        exit;
    }

    private function deleteMovedFiles(array $paths)
    {
        foreach ($paths as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }
}
