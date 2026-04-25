<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

if (!function_exists('e')) {
    function e($value)
    {
        return html_escape($value);
    }
}

if (!function_exists('app_asset')) {
    function app_asset($path)
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('auth_user')) {
    function auth_user()
    {
        $session = load_class('session', 'libraries');
        return $session->userdata('user');
    }
}

if (!function_exists('auth_check')) {
    function auth_check()
    {
        return !empty(auth_user());
    }
}

if (!function_exists('auth_role')) {
    function auth_role()
    {
        $user = auth_user();
        return $user['role'] ?? null;
    }
}

if (!function_exists('dashboard_path_for_role')) {
    function dashboard_path_for_role($role)
    {
        switch ($role) {
            case 'admin':
                return 'admin/dashboard';
            case 'staff':
                return 'staff/dashboard';
            case 'resident':
            default:
                return 'resident/dashboard';
        }
    }
}

if (!function_exists('safe_db_rows')) {
    function safe_db_rows($sql, array $params = [])
    {
        try {
            $database_config = database_config()['main'] ?? [];

            if (($database_config['driver'] ?? '') !== 'mysql') {
                return [];
            }

            $host = $database_config['hostname'] ?? '127.0.0.1';
            $port = $database_config['port'] ?? '3306';
            $database = $database_config['database'] ?? '';
            $charset = $database_config['charset'] ?? 'utf8mb4';
            $username = $database_config['username'] ?? 'root';
            $password = $database_config['password'] ?? '';
            $dsn = "mysql:host={$host};dbname={$database};charset={$charset};port={$port}";

            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            return [];
        }
    }
}

if (!function_exists('old_value')) {
    function old_value($old, $key, $default = '')
    {
        return e($old[$key] ?? $default);
    }
}

if (!function_exists('status_label')) {
    function status_label($status)
    {
        $labels = [
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'needs_info' => 'Needs Info',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'ready_for_pickup' => 'Ready for Pickup',
            'released' => 'Released',
        ];

        return $labels[$status] ?? ucfirst(str_replace('_', ' ', (string) $status));
    }
}

if (!function_exists('status_badge_class')) {
    function status_badge_class($status)
    {
        $classes = [
            'submitted' => 'badge-info',
            'under_review' => 'badge-warning',
            'needs_info' => 'badge-danger',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
            'ready_for_pickup' => 'badge-warning',
            'released' => 'badge-success',
        ];

        return $classes[$status] ?? 'badge-neutral';
    }
}

if (!function_exists('final_document_download_allowed')) {
    function final_document_download_allowed($status)
    {
        return in_array($status, ['approved', 'ready_for_pickup', 'released'], true);
    }
}

if (!function_exists('request_status_transition_allowed')) {
    function request_status_transition_allowed($from, $to)
    {
        $from = (string) $from;
        $to = (string) $to;

        if ($from === $to) {
            return true;
        }

        $allowed = [
            'submitted' => ['under_review', 'needs_info', 'rejected'],
            'under_review' => ['needs_info', 'approved', 'rejected'],
            'needs_info' => ['under_review', 'rejected'],
            'approved' => ['ready_for_pickup', 'released'],
            'ready_for_pickup' => ['released'],
            'released' => [],
            'rejected' => ['under_review'],
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }
}

if (!function_exists('request_status_transition_message')) {
    function request_status_transition_message($from, $to)
    {
        return 'Move requests through the workflow one practical step at a time. Current status is '
            . status_label($from) . ', so it cannot be changed directly to ' . status_label($to) . '.';
    }
}

if (!function_exists('final_document_upload_allowed')) {
    function final_document_upload_allowed(array $request, $payment = null)
    {
        if (!final_document_download_allowed($request['status'] ?? '')) {
            return false;
        }

        if ((int) ($request['requires_payment'] ?? 0) !== 1) {
            return true;
        }

        return !empty($payment) && ($payment['payment_status'] ?? '') === 'payment_verified';
    }
}

if (!function_exists('final_document_block_reason')) {
    function final_document_block_reason(array $request, $payment = null)
    {
        if ((int) ($request['requires_payment'] ?? 0) === 1 && (empty($payment) || ($payment['payment_status'] ?? '') !== 'payment_verified')) {
            return 'Verify the payment proof before uploading the final document.';
        }

        if (!final_document_download_allowed($request['status'] ?? '')) {
            return 'Move the request to Approved, Ready for Pickup, or Released before uploading the final document.';
        }

        return '';
    }
}

if (!function_exists('payment_status_label')) {
    function payment_status_label($status)
    {
        $labels = [
            'pending_payment' => 'Pending Payment',
            'payment_submitted' => 'Payment Submitted',
            'payment_verified' => 'Payment Verified',
            'payment_rejected' => 'Payment Rejected',
        ];

        return $labels[$status] ?? 'Not Required';
    }
}

if (!function_exists('payment_status_badge_class')) {
    function payment_status_badge_class($status)
    {
        $classes = [
            'pending_payment' => 'badge-warning',
            'payment_submitted' => 'badge-info',
            'payment_verified' => 'badge-success',
            'payment_rejected' => 'badge-danger',
        ];

        return $classes[$status] ?? 'badge-neutral';
    }
}

if (!function_exists('payment_method_label')) {
    function payment_method_label($method)
    {
        $labels = [
            'gcash' => 'GCash',
            'maya' => 'Maya',
            'cash' => 'Cash',
        ];

        return $labels[$method] ?? 'Not selected';
    }
}

if (!function_exists('complaint_status_label')) {
    function complaint_status_label($status)
    {
        $labels = [
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'needs_info' => 'Needs Info',
            'investigating' => 'Investigating',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            'dismissed' => 'Dismissed',
        ];

        return $labels[$status] ?? ucfirst(str_replace('_', ' ', (string) $status));
    }
}

if (!function_exists('complaint_status_badge_class')) {
    function complaint_status_badge_class($status)
    {
        $classes = [
            'submitted' => 'badge-info',
            'under_review' => 'badge-warning',
            'needs_info' => 'badge-danger',
            'investigating' => 'border border-cyan-200 bg-cyan-50 text-cyan-900',
            'resolved' => 'badge-success',
            'closed' => 'badge-success',
            'dismissed' => 'badge-danger',
        ];

        return $classes[$status] ?? 'badge-neutral';
    }
}

if (!function_exists('complaint_priority_label')) {
    function complaint_priority_label($priority)
    {
        $labels = [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
        ];

        return $labels[$priority] ?? ucfirst((string) $priority);
    }
}

if (!function_exists('complaint_priority_badge_class')) {
    function complaint_priority_badge_class($priority)
    {
        $classes = [
            'low' => 'badge-info',
            'medium' => 'badge-warning',
            'high' => 'badge-danger',
        ];

        return $classes[$priority] ?? 'badge-neutral';
    }
}

if (!function_exists('complaint_status_transition_allowed')) {
    function complaint_status_transition_allowed($from, $to)
    {
        $from = (string) $from;
        $to = (string) $to;

        if ($from === $to) {
            return true;
        }

        $allowed = [
            'submitted' => ['under_review', 'needs_info', 'dismissed'],
            'under_review' => ['needs_info', 'investigating', 'resolved', 'dismissed'],
            'needs_info' => ['under_review', 'investigating', 'dismissed'],
            'investigating' => ['needs_info', 'resolved', 'dismissed'],
            'resolved' => ['closed', 'investigating'],
            'closed' => [],
            'dismissed' => ['under_review'],
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }
}

if (!function_exists('complaint_status_transition_message')) {
    function complaint_status_transition_message($from, $to)
    {
        return 'Move complaints through the handling workflow one practical step at a time. Current status is '
            . complaint_status_label($from) . ', so it cannot be changed directly to ' . complaint_status_label($to) . '.';
    }
}

if (!function_exists('complaint_category_label')) {
    function complaint_category_label($category)
    {
        $labels = [
            'noise_complaint' => 'Noise complaint',
            'sanitation' => 'Sanitation',
            'neighborhood_dispute' => 'Neighborhood dispute',
            'public_disturbance' => 'Public disturbance',
            'property_concern' => 'Property concern',
            'business_related_concern' => 'Business-related concern',
            'other' => 'Other',
        ];

        return $labels[$category] ?? ucwords(str_replace('_', ' ', (string) $category));
    }
}

if (!function_exists('safe_storage_path')) {
    function safe_storage_path($relative_path, $storage_root_relative)
    {
        $relative_path = trim((string) $relative_path);
        $storage_root_relative = trim((string) $storage_root_relative, "/\\");

        if ($relative_path === '' || $storage_root_relative === '') {
            return null;
        }

        $storage_root = realpath(ROOT_DIR . $storage_root_relative);

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
}

if (!function_exists('safe_delete_storage_file')) {
    function safe_delete_storage_file($relative_or_absolute_path, $storage_root_relative)
    {
        $path = (string) $relative_or_absolute_path;
        $storage_root_relative = trim((string) $storage_root_relative, "/\\");

        if ($path === '' || $storage_root_relative === '') {
            return false;
        }

        $storage_root = realpath(ROOT_DIR . $storage_root_relative);
        $real_path = realpath($path);

        if ($real_path === false && strpos($path, ROOT_DIR) !== 0) {
            $real_path = realpath(ROOT_DIR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path));
        }

        if ($storage_root === false || $real_path === false) {
            return false;
        }

        $storage_root = rtrim($storage_root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (strpos($real_path, $storage_root) !== 0 || !is_file($real_path)) {
            return false;
        }

        return @unlink($real_path);
    }
}

if (!function_exists('safe_download_filename')) {
    function safe_download_filename($filename, $fallback = 'download')
    {
        $filename = basename((string) $filename);
        $filename = str_replace(['"', "\r", "\n"], '', $filename);

        return $filename !== '' ? $filename : $fallback;
    }
}

if (!function_exists('stream_protected_file')) {
    function stream_protected_file($path, $mime, $filename, $disposition = 'inline')
    {
        $mime = trim((string) $mime);
        $filename = safe_download_filename($filename);
        $disposition = in_array($disposition, ['inline', 'attachment'], true) ? $disposition : 'inline';

        if ($mime === '') {
            $mime = 'application/octet-stream';
        }

        while (ob_get_level() > 0) {
            @ob_end_clean();
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: ' . $disposition . '; filename="' . $filename . '"');
        header('X-Content-Type-Options: nosniff');
        readfile($path);
        exit;
    }
}

if (!function_exists('community_category_label')) {
    function community_category_label($category)
    {
        $labels = [
            'announcement' => 'Announcement',
            'event' => 'Event',
            'program' => 'Program',
            'advisory' => 'Advisory',
            'resource' => 'Resource',
        ];

        return $labels[$category] ?? ucwords(str_replace('_', ' ', (string) $category));
    }
}

if (!function_exists('community_category_badge_class')) {
    function community_category_badge_class($category)
    {
        $classes = [
            'announcement' => 'badge-info',
            'event' => 'badge-warning',
            'program' => 'border border-cyan-200 bg-cyan-50 text-cyan-900',
            'advisory' => 'badge-danger',
            'resource' => 'badge-neutral',
        ];

        return $classes[$category] ?? 'badge-neutral';
    }
}

if (!function_exists('community_post_summary')) {
    function community_post_summary(array $post, $length = 140)
    {
        $summary = trim((string) ($post['excerpt'] ?? ''));

        if ($summary === '') {
            $summary = trim(strip_tags((string) ($post['content'] ?? '')));
        }

        if (strlen($summary) <= $length) {
            return $summary;
        }

        return rtrim(substr($summary, 0, $length - 3)) . '...';
    }
}

if (!function_exists('community_event_schedule')) {
    function community_event_schedule(array $post)
    {
        if (empty($post['event_date'])) {
            return '';
        }

        $date = date('M d, Y', strtotime($post['event_date']));
        $time = !empty($post['event_time']) ? date('h:i A', strtotime($post['event_time'])) : '';

        return trim($date . ' ' . $time);
    }
}

if (!function_exists('audit_action_label')) {
    function audit_action_label($action)
    {
        $labels = [
            'changed_status' => 'Changed Status',
            'updated_staff_notes' => 'Updated Staff Notes',
            'created_service' => 'Created Service',
            'updated_service' => 'Updated Service',
            'toggled_service' => 'Toggled Service',
            'created_user' => 'Created User',
            'updated_user' => 'Updated User',
            'toggled_user' => 'Toggled User',
            'created_announcement' => 'Created Announcement',
            'updated_announcement' => 'Updated Announcement',
            'toggled_announcement' => 'Toggled Announcement',
            'uploaded_final_document' => 'Uploaded Final Document',
            'replaced_final_document' => 'Replaced Final Document',
            'downloaded_final_document' => 'Resident Downloaded Document',
            'downloaded_final_document_internal' => 'Internal Document Download',
            'submitted_payment' => 'Submitted Payment',
            'verified_payment' => 'Verified Payment',
            'rejected_payment' => 'Rejected Payment',
            'reviewed_payment_proof' => 'Reviewed Payment Proof',
            'submitted_complaint' => 'Submitted Complaint',
            'updated_complaint_status' => 'Updated Complaint Status',
            'updated_complaint_priority' => 'Updated Complaint Priority',
            'updated_complaint_notes' => 'Updated Complaint Notes',
            'updated_complaint_resolution' => 'Updated Complaint Resolution',
            'updated_complaint_assignment' => 'Updated Complaint Assignment',
            'reviewed_complaint_attachment' => 'Reviewed Complaint Evidence',
            'created_community_post' => 'Created Community Post',
            'updated_community_post' => 'Updated Community Post',
            'toggled_community_post' => 'Toggled Community Post',
            'toggled_community_feature' => 'Toggled Community Feature',
            'exported_summary_report' => 'Exported Summary Report',
            'exported_request_report' => 'Exported Request Report',
            'exported_payment_report' => 'Exported Payment Report',
            'exported_complaint_report' => 'Exported Complaint Report',
            'exported_community_report' => 'Exported Community Report',
        ];

        return $labels[$action] ?? ucwords(str_replace('_', ' ', (string) $action));
    }
}

if (!function_exists('format_money')) {
    function format_money($amount)
    {
        return 'PHP ' . number_format((float) $amount, 2);
    }
}

if (!function_exists('format_file_size')) {
    function format_file_size($bytes)
    {
        $bytes = (int) $bytes;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }
}

if (!function_exists('slugify')) {
    function slugify($value)
    {
        $value = strtolower(trim((string) $value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        $value = trim($value, '-');

        return $value !== '' ? $value : 'item';
    }
}
