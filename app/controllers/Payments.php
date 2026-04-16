<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

require_once SYSTEM_DIR . 'libraries/Upload.php';

class Payments extends Controller
{
    private $max_proof_upload_mb = 5;

    private $allowed_proof_extensions = ['jpg', 'jpeg', 'png', 'pdf'];

    private $allowed_proof_mimes = [
        'image/jpeg',
        'image/png',
        'application/pdf',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('Service_request_model');
        $this->call->model('Payment_model');
        $this->call->model('Audit_log_model');
    }

    public function residentForm($id)
    {
        $resident = auth_user();
        $request = $this->Service_request_model->find_for_user((int) $id, (int) $resident['id']);

        if (empty($request)) {
            $this->session->set_flashdata('error', 'Request not found.');
            redirect('resident/requests');
            exit;
        }

        if ((int) $request['requires_payment'] !== 1) {
            $this->session->set_flashdata('error', 'This request does not require payment.');
        }

        redirect('resident/requests/' . (int) $id);
        exit;
    }

    public function residentStore($id)
    {
        $resident = auth_user();
        $request = $this->Service_request_model->find_for_user((int) $id, (int) $resident['id']);

        if (empty($request)) {
            $this->session->set_flashdata('error', 'Request not found.');
            redirect('resident/requests');
            exit;
        }

        if ((int) $request['requires_payment'] !== 1) {
            $this->session->set_flashdata('error', 'This request does not require payment.');
            redirect('resident/requests/' . (int) $id);
            exit;
        }

        $payment = $this->Payment_model->find_for_request((int) $id);

        if (!empty($payment) && $payment['payment_status'] === 'payment_verified') {
            $this->session->set_flashdata('error', 'This payment has already been verified.');
            redirect('resident/requests/' . (int) $id);
            exit;
        }

        $method = trim($_POST['payment_method'] ?? '');
        $reference_number = trim($_POST['reference_number'] ?? '');
        $proof = $_FILES['payment_proof'] ?? [];
        $errors = $this->validatePaymentSubmission($method, $reference_number, $proof);

        if (!empty($errors)) {
            $this->session->set_flashdata('errors', $errors);
            redirect('resident/requests/' . (int) $id);
            exit;
        }

        $existing_proof_path = $payment['proof_file_path'] ?? null;
        $upload_dir = ROOT_DIR . 'runtime/uploads/payment_proofs/request_' . (int) $id;
        $upload = new Upload($proof);
        $upload
            ->set_dir($upload_dir)
            ->allowed_extensions($this->allowed_proof_extensions)
            ->allowed_mimes($this->allowed_proof_mimes)
            ->max_size($this->max_proof_upload_mb)
            ->encrypt_name();

        if (!$upload->do_upload()) {
            $this->session->set_flashdata('errors', $upload->get_errors());
            redirect('resident/requests/' . (int) $id);
            exit;
        }

        $stored_name = $upload->get_filename();
        $new_absolute_path = $upload_dir . DIRECTORY_SEPARATOR . $stored_name;
        $relative_path = 'runtime/uploads/payment_proofs/request_' . (int) $id . '/' . $stored_name;

        try {
            $this->db->transaction();

            $this->Payment_model->submit_payment((int) $id, [
                'amount' => (float) $request['fee'],
                'payment_method' => $method,
                'reference_number' => $reference_number,
                'proof_original_name' => sanitize_filename($proof['name']),
                'proof_stored_name' => $stored_name,
                'proof_file_path' => $relative_path,
                'proof_file_size' => (int) $upload->get_size(),
                'proof_file_type' => $upload->mime,
                'submitted_by' => (int) $resident['id'],
            ]);

            $this->Audit_log_model->record(
                (int) $resident['id'],
                'submitted_payment',
                'service_request',
                (int) $id,
                'Resident submitted simulated payment for ' . $request['reference_no'] . '.'
            );

            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->roll_back();
            $this->deleteProofIfSafe($new_absolute_path);

            $this->session->set_flashdata('error', 'Payment could not be saved.');
            redirect('resident/requests/' . (int) $id);
            exit;
        }

        if (!empty($existing_proof_path)) {
            $old_path = $this->safePaymentProofPath($existing_proof_path);

            if ($old_path !== null && $old_path !== realpath($new_absolute_path)) {
                @unlink($old_path);
            }
        }

        $this->session->set_flashdata('success', 'Your simulated payment was submitted for staff review.');
        redirect('resident/requests/' . (int) $id);
        exit;
    }

    public function staffProof($payment_id)
    {
        $this->streamProofForInternalRole((int) $payment_id, 'staff');
    }

    public function adminProof($payment_id)
    {
        $this->streamProofForInternalRole((int) $payment_id, 'admin');
    }

    public function staffUpdate($request_id)
    {
        $staff = auth_user();
        $request = $this->Service_request_model->find_for_staff((int) $request_id);

        if (empty($request)) {
            $this->session->set_flashdata('error', 'Request not found.');
            redirect('staff/requests');
            exit;
        }

        if ((int) $request['requires_payment'] !== 1) {
            $this->session->set_flashdata('error', 'This request does not require payment.');
            redirect('staff/requests/' . (int) $request_id);
            exit;
        }

        $payment = $this->Payment_model->find_for_request((int) $request_id);

        if (empty($payment)) {
            $this->Payment_model->create_pending_for_request((int) $request_id, (float) $request['fee']);
            $this->session->set_flashdata('error', 'The resident has not submitted a payment yet.');
            redirect('staff/requests/' . (int) $request_id);
            exit;
        }

        $payment_status = trim($_POST['payment_status'] ?? '');
        $remarks = trim($_POST['remarks'] ?? '');
        $errors = [];

        if (!in_array($payment_status, $this->Payment_model->review_statuses(), true)) {
            $errors[] = 'Choose a valid payment decision.';
        }

        if (empty($payment['proof_file_path'])) {
            $errors[] = 'The resident has not uploaded payment proof yet.';
        }

        if ($payment_status === 'payment_rejected' && $remarks === '') {
            $errors[] = 'Add remarks when rejecting a payment.';
        }

        if (strlen($remarks) > 1000) {
            $errors[] = 'Payment remarks must be 1000 characters or fewer.';
        }

        if (!empty($errors)) {
            $this->session->set_flashdata('errors', $errors);
            redirect('staff/requests/' . (int) $request_id);
            exit;
        }

        $this->Payment_model->review_payment((int) $payment['id'], $payment_status, $remarks, (int) $staff['id']);

        $this->Audit_log_model->record(
            (int) $staff['id'],
            $payment_status === 'payment_verified' ? 'verified_payment' : 'rejected_payment',
            'service_request',
            (int) $request_id,
            ($payment_status === 'payment_verified' ? 'Verified' : 'Rejected') . ' simulated payment for ' . $request['reference_no'] . '.'
        );

        $this->session->set_flashdata('success', 'Payment review was saved.');
        redirect('staff/requests/' . (int) $request_id);
        exit;
    }

    private function validatePaymentSubmission($method, $reference_number, array $proof)
    {
        $errors = [];

        if (!array_key_exists($method, $this->Payment_model->allowed_methods())) {
            $errors[] = 'Choose a valid simulated payment method.';
        }

        if ($reference_number === '') {
            $errors[] = 'Reference number is required.';
        } elseif (strlen($reference_number) > 120) {
            $errors[] = 'Reference number must be 120 characters or fewer.';
        }

        if (empty($proof) || ($proof['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Upload a proof of payment file.';
            return $errors;
        }

        if (($proof['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $errors[] = 'The uploaded payment proof could not be read.';
            return $errors;
        }

        $extension = strtolower(pathinfo($proof['name'] ?? '', PATHINFO_EXTENSION));

        if (!in_array($extension, $this->allowed_proof_extensions, true)) {
            $errors[] = 'Allowed proof types are JPG, PNG, and PDF.';
        }

        if (($proof['size'] ?? 0) > ($this->max_proof_upload_mb * 1024 * 1024)) {
            $errors[] = 'Payment proof must be ' . $this->max_proof_upload_mb . 'MB or smaller.';
        }

        return $errors;
    }

    private function streamProofForInternalRole($payment_id, $role)
    {
        $user = auth_user();
        $payment = $this->Payment_model->find_with_request((int) $payment_id);

        if (empty($payment) || empty($payment['proof_file_path'])) {
            show_404();
            return;
        }

        $path = $this->safePaymentProofPath($payment['proof_file_path']);

        if ($path === null) {
            show_404();
            return;
        }

        $this->Audit_log_model->record(
            (int) $user['id'],
            'reviewed_payment_proof',
            'service_request',
            (int) $payment['request_id'],
            ucfirst($role) . ' reviewed payment proof for ' . $payment['reference_no'] . '.'
        );

        $this->streamProof($path, $payment);
    }

    private function safePaymentProofPath($relative_path)
    {
        $storage_root = realpath(ROOT_DIR . 'runtime/uploads/payment_proofs');

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

    private function deleteProofIfSafe($absolute_path)
    {
        $storage_root = realpath(ROOT_DIR . 'runtime/uploads/payment_proofs');
        $real_path = realpath($absolute_path);

        if ($storage_root === false || $real_path === false) {
            return;
        }

        $storage_root = rtrim($storage_root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (strpos($real_path, $storage_root) === 0 && is_file($real_path)) {
            @unlink($real_path);
        }
    }

    private function streamProof($path, array $payment)
    {
        $filename = basename($payment['proof_original_name'] ?: 'payment-proof');
        $filename = str_replace(['"', "\r", "\n"], '', $filename);

        while (ob_get_level() > 0) {
            @ob_end_clean();
        }

        header('Content-Type: ' . $payment['proof_file_type']);
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('X-Content-Type-Options: nosniff');
        readfile($path);
        exit;
    }
}
