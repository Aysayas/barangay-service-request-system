<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

require_once SYSTEM_DIR . 'libraries/Upload.php';

class ResidentComplaints extends Controller
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
        $this->call->model('Complaint_model');
        $this->call->model('Complaint_attachment_model');
        $this->call->model('Audit_log_model');
        $this->call->library('Notification_service');
        $this->call->library('Pdf_service');
    }

    public function index()
    {
        $user = auth_user();

        $this->call->view('resident/complaints/index', [
            'title' => 'My Complaints',
            'complaints' => $this->Complaint_model->for_user((int) $user['id']),
        ]);
    }

    public function create()
    {
        $this->call->view('resident/complaints/create', [
            'title' => 'Submit Complaint',
            'categories' => $this->Complaint_model->categories(),
            'old' => $this->session->flashdata('old') ?: [],
            'max_upload_mb' => $this->max_upload_mb,
            'user' => auth_user(),
        ]);
    }

    public function store()
    {
        $user = auth_user();
        $files = $this->normalizeFiles($_FILES['attachments'] ?? []);

        $data = [
            'subject' => trim($_POST['subject'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'incident_date' => trim($_POST['incident_date'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'respondent_name' => trim($_POST['respondent_name'] ?? ''),
            'complainant_contact' => trim($_POST['complainant_contact'] ?? ''),
        ];

        $errors = $this->validateComplaintInput($data, $files);

        if (!empty($errors)) {
            $this->redirectBackToForm($errors, $data);
        }

        $moved_files = [];

        try {
            $this->db->transaction();
            $reference_no = $this->generateReferenceNo();

            $complaint_id = $this->Complaint_model->create_complaint([
                'reference_no' => $reference_no,
                'user_id' => (int) $user['id'],
                'complainant_name' => $user['name'] ?? 'Resident',
                'complainant_email' => $user['email'] ?? '',
                'complainant_contact' => $data['complainant_contact'],
                'subject' => $data['subject'],
                'category' => $data['category'],
                'description' => $data['description'],
                'incident_date' => $data['incident_date'],
                'location' => $data['location'],
                'respondent_name' => $data['respondent_name'],
            ]);

            $upload_errors = $this->saveAttachments($complaint_id, $files, $moved_files);

            if (!empty($upload_errors)) {
                throw new RuntimeException(implode(' ', $upload_errors));
            }

            $this->Audit_log_model->record(
                (int) $user['id'],
                'submitted_complaint',
                'complaint',
                (int) $complaint_id,
                'Resident submitted complaint ' . $data['subject'] . '.'
            );

            $this->db->commit();

            $this->Notification_service->complaint_submitted($user, $reference_no, $data['subject']);

            $this->session->set_flashdata('success', 'Your complaint was submitted.');
            redirect('resident/complaints/' . (int) $complaint_id);
            exit;
        } catch (Throwable $e) {
            $this->db->roll_back();
            $this->deleteMovedFiles($moved_files);

            $this->redirectBackToForm([
                'We could not submit the complaint. Please check your details and evidence files, then try again.',
            ], $data);
        }
    }

    public function show($id)
    {
        $user = auth_user();
        $complaint = $this->Complaint_model->find_for_user((int) $id, (int) $user['id']);

        if (empty($complaint)) {
            $this->session->set_flashdata('error', 'Complaint not found.');
            redirect('resident/complaints');
            exit;
        }

        $this->call->view('resident/complaints/show', [
            'title' => 'Complaint Details',
            'complaint' => $complaint,
            'attachments' => $this->Complaint_attachment_model->for_complaint((int) $complaint['id']),
            'statuses' => $this->Complaint_model->allowed_statuses(),
        ]);
    }

    public function pdf($id)
    {
        $user = auth_user();
        $complaint = $this->Complaint_model->find_for_user((int) $id, (int) $user['id']);

        if (empty($complaint)) {
            $this->session->set_flashdata('error', 'Complaint not found.');
            redirect('resident/complaints');
            exit;
        }

        try {
            $this->Pdf_service->download('pdf/complaint_summary', [
                'complaint' => $complaint,
                'resident' => $user,
                'attachments' => $this->Complaint_attachment_model->for_complaint((int) $complaint['id']),
            ], 'complaint_' . $complaint['reference_no'] . '.pdf');
        } catch (Throwable $e) {
            $this->session->set_flashdata('error', 'The complaint PDF could not be generated right now.');
            redirect('resident/complaints/' . (int) $complaint['id']);
            exit;
        }
    }

    public function attachment($attachment_id)
    {
        $user = auth_user();
        $attachment = $this->Complaint_attachment_model->find_for_resident((int) $attachment_id, (int) $user['id']);

        if (empty($attachment)) {
            show_404();
            return;
        }

        $this->streamAttachment($attachment);
    }

    private function validateComplaintInput(array $data, array $files)
    {
        $errors = [];
        $category_keys = array_keys($this->Complaint_model->categories());

        if ($data['subject'] === '') {
            $errors[] = 'Subject is required.';
        } elseif (strlen($data['subject']) > 160) {
            $errors[] = 'Subject must be 160 characters or fewer.';
        }

        if (!in_array($data['category'], $category_keys, true)) {
            $errors[] = 'Choose a valid complaint category.';
        }

        if ($data['description'] === '') {
            $errors[] = 'Description is required.';
        } elseif (strlen($data['description']) > 3000) {
            $errors[] = 'Description must be 3000 characters or fewer.';
        }

        if ($data['location'] === '') {
            $errors[] = 'Incident location is required.';
        } elseif (strlen($data['location']) > 255) {
            $errors[] = 'Location must be 255 characters or fewer.';
        }

        if ($data['complainant_contact'] === '') {
            $errors[] = 'Contact number is required so staff can follow up.';
        } elseif (strlen($data['complainant_contact']) > 30) {
            $errors[] = 'Contact number must be 30 characters or fewer.';
        }

        if (strlen($data['respondent_name']) > 160) {
            $errors[] = 'Respondent name must be 160 characters or fewer.';
        }

        if ($data['incident_date'] !== '') {
            $date = DateTime::createFromFormat('Y-m-d', $data['incident_date']);

            if (!$date || $date->format('Y-m-d') !== $data['incident_date']) {
                $errors[] = 'Incident date must be a valid date.';
            } elseif ($data['incident_date'] > date('Y-m-d')) {
                $errors[] = 'Incident date cannot be in the future.';
            }
        }

        foreach ($files as $file) {
            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                $errors[] = 'One of the evidence files could not be read.';
                continue;
            }

            $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));

            if (!in_array($extension, $this->allowed_extensions, true)) {
                $errors[] = 'Allowed evidence file types are PDF, JPG, PNG, DOC, and DOCX.';
            }

            if (($file['size'] ?? 0) > ($this->max_upload_mb * 1024 * 1024)) {
                $errors[] = 'Each evidence file must be ' . $this->max_upload_mb . 'MB or smaller.';
            }
        }

        return array_values(array_unique($errors));
    }

    private function saveAttachments($complaint_id, array $files, array &$moved_files)
    {
        $errors = [];
        $upload_dir = ROOT_DIR . 'runtime/uploads/complaints/complaint_' . (int) $complaint_id;

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
            $relative_path = 'runtime/uploads/complaints/complaint_' . (int) $complaint_id . '/' . $stored_name;
            $moved_files[] = $absolute_path;

            $this->Complaint_attachment_model->create_attachment([
                'complaint_id' => (int) $complaint_id,
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
            $reference = 'CMP-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        } while ($this->Complaint_model->reference_exists($reference));

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

    private function streamAttachment(array $attachment)
    {
        $real_path = safe_storage_path($attachment['file_path'], 'runtime/uploads/complaints');

        if ($real_path === null) {
            show_404();
            return;
        }

        stream_protected_file($real_path, $attachment['file_type'], $attachment['original_name'], 'inline');
    }

    private function redirectBackToForm(array $errors, array $old)
    {
        $this->session->set_flashdata('errors', $errors);
        $this->session->set_flashdata('old', $old);
        redirect('resident/complaints/create');
        exit;
    }

    private function deleteMovedFiles(array $paths)
    {
        foreach ($paths as $path) {
            safe_delete_storage_file($path, 'runtime/uploads/complaints');
        }
    }
}
