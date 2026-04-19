<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Reports extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('Report_model');
        $this->call->model('Audit_log_model');
        $this->call->library('Ai_service');
        $this->call->library('Report_ai_summary_service');
    }

    public function index()
    {
        $summary = $this->Report_model->overview_summary();
        $charts = $this->Report_model->charts_data();

        $this->call->view('admin/reports/index', [
            'title' => 'Reports',
            'summary' => $summary,
            'charts' => $charts,
            'report_summary' => $this->Report_ai_summary_service->overview($summary, $charts),
        ]);
    }

    public function exportSummary()
    {
        $summary = $this->Report_model->overview_summary();
        $rows = [
            ['Requests', 'Total Requests', $summary['total_requests'] ?? 0],
            ['Requests', 'Pending Requests', $summary['pending_requests'] ?? 0],
            ['Payments', 'Total Payment Records', $summary['total_payments'] ?? 0],
            ['Payments', 'Verified Payments', $summary['verified_payments'] ?? 0],
            ['Payments', 'Verified Amount', number_format((float) ($summary['verified_amount'] ?? 0), 2, '.', '')],
            ['Complaints', 'Total Complaints', $summary['total_complaints'] ?? 0],
            ['Complaints', 'Open Complaints', $summary['open_complaints'] ?? 0],
            ['Community', 'Total Posts', $summary['total_posts'] ?? 0],
            ['Community', 'Published Posts', $summary['published_posts'] ?? 0],
            ['Community', 'Upcoming Events', $summary['upcoming_events'] ?? 0],
        ];

        $this->recordExport('exported_summary_report', 'Exported combined reports summary CSV.');
        $this->downloadCsv('overall_summary_report_' . date('Y-m-d') . '.csv', [
            'Section',
            'Metric',
            'Value',
        ], $rows);
    }

    public function requests()
    {
        $filters = $this->requestFilters();
        $summary = $this->Report_model->request_summary($filters);

        $this->call->view('admin/reports/requests', [
            'title' => 'Request Reports',
            'filters' => $filters,
            'services' => $this->Report_model->services(),
            'statuses' => $this->Report_model->request_statuses(),
            'summary' => $summary,
            'report_summary' => $this->Report_ai_summary_service->requests($summary, $filters),
            'rows' => $this->Report_model->request_rows($filters),
            'export_url' => $this->exportUrl('admin/reports/requests/export', $filters),
        ]);
    }

    public function payments()
    {
        $filters = $this->paymentFilters();
        $summary = $this->Report_model->payment_summary($filters);

        $this->call->view('admin/reports/payments', [
            'title' => 'Payment Reports',
            'filters' => $filters,
            'services' => $this->Report_model->services(),
            'payment_statuses' => $this->Report_model->payment_statuses(),
            'summary' => $summary,
            'report_summary' => $this->Report_ai_summary_service->payments($summary, $filters),
            'rows' => $this->Report_model->payment_rows($filters),
            'export_url' => $this->exportUrl('admin/reports/payments/export', $filters),
        ]);
    }

    public function complaints()
    {
        $filters = $this->complaintFilters();
        $summary = $this->Report_model->complaint_summary($filters);

        $this->call->view('admin/reports/complaints', [
            'title' => 'Complaint Reports',
            'filters' => $filters,
            'statuses' => $this->Report_model->complaint_statuses(),
            'categories' => $this->Report_model->complaint_categories(),
            'priorities' => $this->Report_model->complaint_priorities(),
            'summary' => $summary,
            'report_summary' => $this->Report_ai_summary_service->complaints($summary, $filters),
            'rows' => $this->Report_model->complaint_rows($filters),
            'export_url' => $this->exportUrl('admin/reports/complaints/export', $filters),
        ]);
    }

    public function community()
    {
        $filters = $this->communityFilters();
        $summary = $this->Report_model->community_summary($filters);

        $this->call->view('admin/reports/community', [
            'title' => 'Community Reports',
            'filters' => $filters,
            'categories' => $this->Report_model->community_categories(),
            'summary' => $summary,
            'report_summary' => $this->Report_ai_summary_service->community($summary, $filters),
            'rows' => $this->Report_model->community_rows($filters),
            'export_url' => $this->exportUrl('admin/reports/community/export', $filters),
        ]);
    }

    public function exportRequests()
    {
        $filters = $this->requestFilters();
        $rows = $this->Report_model->request_rows($filters, 0);
        $csv_rows = [];

        foreach ($rows as $row) {
            $csv_rows[] = [
                $row['reference_no'],
                $row['resident_name'],
                $row['service_name'],
                status_label($row['status']),
                (int) $row['requires_payment'] === 1 ? payment_status_label($row['payment_status']) : 'Not Required',
                (int) $row['has_final_document'] === 1 ? 'Yes' : 'No',
                $this->csvDate($row['created_at']),
            ];
        }

        $this->recordExport('exported_request_report', 'Exported request report CSV.');
        $this->downloadCsv('requests_report_' . date('Y-m-d') . '.csv', [
            'Reference Number',
            'Resident Name',
            'Service',
            'Request Status',
            'Payment Status',
            'Final Document Available',
            'Created Date',
        ], $csv_rows);
    }

    public function exportPayments()
    {
        $filters = $this->paymentFilters();
        $rows = $this->Report_model->payment_rows($filters, 0);
        $csv_rows = [];

        foreach ($rows as $row) {
            $csv_rows[] = [
                $row['reference_no'],
                $row['resident_name'],
                $row['service_name'],
                number_format((float) $row['amount'], 2, '.', ''),
                payment_method_label($row['payment_method']),
                payment_status_label($row['payment_status']),
                $this->csvDate($row['submitted_at']),
                $this->csvDate($row['verified_at']),
                $row['remarks'] ?? '',
            ];
        }

        $this->recordExport('exported_payment_report', 'Exported payment report CSV.');
        $this->downloadCsv('payments_report_' . date('Y-m-d') . '.csv', [
            'Request Reference Number',
            'Resident Name',
            'Service',
            'Amount',
            'Payment Method',
            'Payment Status',
            'Submitted Date',
            'Verified Date',
            'Remarks',
        ], $csv_rows);
    }

    public function exportComplaints()
    {
        $filters = $this->complaintFilters();
        $rows = $this->Report_model->complaint_rows($filters, 0);
        $csv_rows = [];

        foreach ($rows as $row) {
            $csv_rows[] = [
                $row['reference_no'],
                $row['complainant_name'],
                $row['subject'],
                complaint_category_label($row['category']),
                complaint_priority_label($row['priority']),
                complaint_status_label($row['status']),
                !empty($row['assigned_to_name']) ? $row['assigned_to_name'] : 'Unassigned',
                $this->csvDate($row['created_at']),
            ];
        }

        $this->recordExport('exported_complaint_report', 'Exported complaint report CSV.');
        $this->downloadCsv('complaints_report_' . date('Y-m-d') . '.csv', [
            'Complaint Reference Number',
            'Complainant Name',
            'Subject',
            'Category',
            'Priority',
            'Status',
            'Assigned Staff',
            'Created Date',
        ], $csv_rows);
    }

    public function exportCommunity()
    {
        $filters = $this->communityFilters();
        $rows = $this->Report_model->community_rows($filters, 0);
        $csv_rows = [];

        foreach ($rows as $row) {
            $csv_rows[] = [
                $row['title'],
                community_category_label($row['category']),
                (int) $row['is_published'] === 1 ? 'Yes' : 'No',
                (int) $row['is_featured'] === 1 ? 'Yes' : 'No',
                $this->csvDate($row['published_at']),
                $this->csvDate($row['created_at']),
                $this->csvDate($row['event_date'], false),
                $row['resource_link'] ?? '',
            ];
        }

        $this->recordExport('exported_community_report', 'Exported community report CSV.');
        $this->downloadCsv('community_report_' . date('Y-m-d') . '.csv', [
            'Title',
            'Category',
            'Published',
            'Featured',
            'Published Date',
            'Created Date',
            'Event Date',
            'Resource Link',
        ], $csv_rows);
    }

    private function requestFilters()
    {
        return array_merge($this->dateFilters(), [
            'status' => $this->choice('status', $this->Report_model->request_statuses()),
            'service_id' => $this->integerFilter('service_id'),
        ]);
    }

    private function paymentFilters()
    {
        return array_merge($this->dateFilters(), [
            'payment_status' => $this->choice('payment_status', $this->Report_model->payment_statuses()),
            'service_id' => $this->integerFilter('service_id'),
        ]);
    }

    private function complaintFilters()
    {
        return array_merge($this->dateFilters(), [
            'status' => $this->choice('status', $this->Report_model->complaint_statuses()),
            'category' => $this->choice('category', array_keys($this->Report_model->complaint_categories())),
            'priority' => $this->choice('priority', $this->Report_model->complaint_priorities()),
        ]);
    }

    private function communityFilters()
    {
        return array_merge($this->dateFilters(), [
            'category' => $this->choice('category', array_keys($this->Report_model->community_categories())),
            'is_published' => $this->choice('is_published', ['0', '1']),
            'is_featured' => $this->choice('is_featured', ['0', '1']),
        ]);
    }

    private function dateFilters()
    {
        $from_date = $this->validDate($_GET['from_date'] ?? '') ? $_GET['from_date'] : '';
        $to_date = $this->validDate($_GET['to_date'] ?? '') ? $_GET['to_date'] : '';

        if ($from_date !== '' && $to_date !== '' && $from_date > $to_date) {
            $temp = $from_date;
            $from_date = $to_date;
            $to_date = $temp;
        }

        return [
            'from_date' => $from_date,
            'to_date' => $to_date,
        ];
    }

    private function choice($key, array $allowed)
    {
        $value = trim((string) ($_GET[$key] ?? ''));
        return in_array($value, $allowed, true) ? $value : '';
    }

    private function integerFilter($key)
    {
        $value = trim((string) ($_GET[$key] ?? ''));

        if ($value === '' || !ctype_digit($value) || (int) $value <= 0) {
            return 0;
        }

        return (int) $value;
    }

    private function validDate($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return false;
        }

        $date = DateTime::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value;
    }

    private function exportUrl($path, array $filters)
    {
        $query = [];

        foreach ($filters as $key => $value) {
            if ($value === '' || $value === 0 || $value === null) {
                continue;
            }

            $query[$key] = $value;
        }

        return site_url($path) . (!empty($query) ? '?' . http_build_query($query) : '');
    }

    private function downloadCsv($filename, array $headers, array $rows)
    {
        $filename = safe_download_filename($filename, 'report.csv');

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM helps Excel read the file cleanly.
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, $headers);

        foreach ($rows as $row) {
            fputcsv($output, array_map([$this, 'csvValue'], $row));
        }

        fclose($output);
        exit;
    }

    private function csvValue($value)
    {
        if ($value === null) {
            return '';
        }

        return (string) $value;
    }

    private function csvDate($value, $include_time = true)
    {
        if (empty($value)) {
            return '';
        }

        $timestamp = strtotime($value);

        if ($timestamp === false) {
            return '';
        }

        return date($include_time ? 'Y-m-d H:i:s' : 'Y-m-d', $timestamp);
    }

    private function recordExport($action, $description)
    {
        try {
            $user = auth_user();
            $this->Audit_log_model->record($user['id'] ?? null, $action, 'report', 0, $description);
        } catch (Throwable $e) {
            // Export should still work even if audit logging is unavailable.
        }
    }
}
