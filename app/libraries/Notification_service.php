<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Notification_service
{
    private static $logged_config_skip = false;

    private $driver;
    private $host;
    private $port;
    private $encryption;
    private $username;
    private $password;
    private $from_email;
    private $from_name;

    public function __construct()
    {
        $this->driver = strtolower(trim((string) config_item('mail_driver')));
        $this->host = trim((string) config_item('mail_host'));
        $this->port = (int) config_item('mail_port');
        $this->encryption = strtolower(trim((string) config_item('mail_encryption')));
        $this->username = trim((string) config_item('mail_username'));
        $this->password = (string) config_item('mail_password');
        $this->from_email = trim((string) config_item('mail_from_email'));
        $this->from_name = trim((string) config_item('mail_from_name'));

        if ($this->from_email === '') {
            $this->from_email = $this->username;
        }

        // Gmail SMTP is strict about the sender. Use the authenticated mailbox
        // unless the account has a verified alias configured in Gmail.
        if (strpos($this->host, 'gmail.com') !== false && $this->username !== '') {
            $this->from_email = $this->username;
        }

        if ($this->from_name === '') {
            $this->from_name = 'eBarangayHub Notifications';
        }
    }

    public function resident_registered(array $user)
    {
        return $this->send(
            $user['email'] ?? '',
            'Welcome to eBarangayHub',
            [
                'Hello ' . ($user['first_name'] ?? 'Resident') . ',',
                'Your eBarangayHub resident account has been created successfully.',
                'You may now log in, submit service requests, track requests, and use the virtual help assistant.',
            ]
        );
    }

    public function service_request_submitted(array $resident, $reference_no, $service_name)
    {
        return $this->send(
            $resident['email'] ?? '',
            'Service request submitted: ' . $reference_no,
            [
                'Hello ' . ($resident['name'] ?? 'Resident') . ',',
                'Your ' . $service_name . ' request was submitted successfully.',
                'Reference number: ' . $reference_no,
                'You can track the request from My Requests after logging in.',
            ]
        );
    }

    public function payment_reviewed(array $request, $payment_status, $remarks = '')
    {
        $verified = $payment_status === 'payment_verified';

        $lines = [
            'Hello ' . ($request['resident_name'] ?? 'Resident') . ',',
            'The payment proof for request ' . ($request['reference_no'] ?? '') . ' was ' . ($verified ? 'verified' : 'rejected') . '.',
        ];

        if (!$verified && trim((string) $remarks) !== '') {
            $lines[] = 'Remarks: ' . trim((string) $remarks);
        }

        $lines[] = $verified
            ? 'Staff may now continue processing your paid request.'
            : 'Please review your request details and submit an updated payment proof if needed.';

        return $this->send(
            $request['resident_email'] ?? '',
            $verified ? 'Payment proof verified' : 'Payment proof rejected',
            $lines
        );
    }

    public function request_approved(array $request)
    {
        return $this->send(
            $request['resident_email'] ?? '',
            'Request approved: ' . ($request['reference_no'] ?? ''),
            [
                'Hello ' . ($request['resident_name'] ?? 'Resident') . ',',
                'Your request has been approved.',
                'Reference number: ' . ($request['reference_no'] ?? ''),
                'Please check your request details for the latest staff notes and final document availability.',
            ]
        );
    }

    public function final_document_available(array $request)
    {
        return $this->send(
            $request['resident_email'] ?? '',
            'Final document available: ' . ($request['reference_no'] ?? ''),
            [
                'Hello ' . ($request['resident_name'] ?? 'Resident') . ',',
                'A final document has been uploaded for your request.',
                'Reference number: ' . ($request['reference_no'] ?? ''),
                'You can download it from your request details page when your request status allows download.',
            ]
        );
    }

    public function complaint_submitted(array $resident, $reference_no, $subject)
    {
        return $this->send(
            $resident['email'] ?? '',
            'Complaint submitted: ' . $reference_no,
            [
                'Hello ' . ($resident['name'] ?? 'Resident') . ',',
                'Your complaint was submitted successfully.',
                'Reference number: ' . $reference_no,
                'Subject: ' . $subject,
                'You can track the complaint from My Complaints after logging in.',
            ]
        );
    }

    public function complaint_closed(array $complaint, $status)
    {
        return $this->send(
            $complaint['resident_email'] ?? $complaint['complainant_email'] ?? '',
            'Complaint ' . complaint_status_label($status) . ': ' . ($complaint['reference_no'] ?? ''),
            [
                'Hello ' . ($complaint['resident_name'] ?? $complaint['complainant_name'] ?? 'Resident') . ',',
                'Your complaint is now marked as ' . complaint_status_label($status) . '.',
                'Reference number: ' . ($complaint['reference_no'] ?? ''),
                'Please open your complaint details page for staff notes or resolution notes.',
            ]
        );
    }

    public function send($to, $subject, array $lines)
    {
        $to = trim((string) $to);

        if (!$this->isReady()) {
            if (!self::$logged_config_skip) {
                $this->log('Skipped email because SMTP config is incomplete.', [
                    'driver' => $this->driver,
                    'host' => $this->host,
                    'port' => $this->port,
                    'encryption' => $this->encryption,
                    'has_username' => $this->username !== '' ? 'true' : 'false',
                    'has_password' => $this->password !== '' ? 'true' : 'false',
                    'from_email' => $this->from_email,
                ]);

                self::$logged_config_skip = true;
            }

            return false;
        }

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->log('Skipped email because recipient is invalid.', [
                'to' => $to,
                'subject' => $subject,
            ]);
            return false;
        }

        if (!$this->loadMailer()) {
            $this->log('Skipped email because PHPMailer is not installed.', [
                'to' => $to,
                'subject' => $subject,
                'expected_autoload' => ROOT_DIR . 'vendor/autoload.php',
            ]);
            return false;
        }

        $subject = trim((string) $subject);
        $text = implode("\n\n", array_map('trim', $lines));
        $html = $this->htmlFromLines($lines);

        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->Port = $this->port;
            $mail->Timeout = 10;
            $mail->CharSet = 'UTF-8';

            if ($this->encryption === 'ssl') {
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($this->encryption === 'tls') {
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->setFrom($this->from_email, $this->from_name);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $html;
            $mail->AltBody = $text . "\n\nThis is an automated notification from eBarangayHub.";

            $mail->send();

            $this->log('Email sent through Gmail SMTP.', [
                'to' => $to,
                'subject' => $subject,
                'host' => $this->host,
                'from_email' => $this->from_email,
            ]);

            return true;
        } catch (Throwable $e) {
            $this->log('SMTP send failed.', [
                'to' => $to,
                'subject' => $subject,
                'host' => $this->host,
                'from_email' => $this->from_email,
                'error' => $this->safeSnippet($e->getMessage()),
            ]);
            return false;
        }
    }

    private function isReady()
    {
        return $this->driver === 'smtp'
            && $this->host !== ''
            && $this->port > 0
            && $this->username !== ''
            && $this->password !== ''
            && $this->from_email !== ''
            && filter_var($this->from_email, FILTER_VALIDATE_EMAIL);
    }

    private function loadMailer()
    {
        if (class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
            return true;
        }

        $autoload = ROOT_DIR . 'vendor/autoload.php';

        if (is_file($autoload) && is_readable($autoload)) {
            require_once $autoload;
        }

        return class_exists('\PHPMailer\PHPMailer\PHPMailer');
    }

    private function htmlFromLines(array $lines)
    {
        $html = '<div style="font-family:Arial,sans-serif;font-size:15px;line-height:1.6;color:#18181b;">';

        foreach ($lines as $line) {
            $html .= '<p>' . htmlspecialchars((string) $line, ENT_QUOTES, 'UTF-8') . '</p>';
        }

        $html .= '<p style="color:#71717a;font-size:13px;">This is an automated notification from eBarangayHub.</p>';
        $html .= '</div>';

        return $html;
    }

    private function log($message, array $context = [])
    {
        $log_dir = ROOT_DIR . 'runtime/logs';

        if (!is_dir($log_dir)) {
            @mkdir($log_dir, 0775, true);
        }

        $context = $this->sanitizeLogContext($context);
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message;

        if (!empty($context)) {
            $line .= ' ' . json_encode($context, JSON_UNESCAPED_SLASHES);
        }

        @file_put_contents($log_dir . DIRECTORY_SEPARATOR . 'notifications.log', $line . PHP_EOL, FILE_APPEND);
    }

    private function sanitizeLogContext(array $context)
    {
        unset(
            $context['api_key'],
            $context['authorization'],
            $context['password'],
            $context['mail_password'],
            $context['smtp_password']
        );

        if (!empty($context['error'])) {
            $context['error'] = $this->safeSnippet($context['error']);
        }

        return $context;
    }

    private function safeSnippet($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        return substr($value, 0, 500);
    }
}
