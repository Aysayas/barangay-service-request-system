<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Report_model extends Model
{
    public function request_statuses()
    {
        return [
            'submitted',
            'under_review',
            'needs_info',
            'approved',
            'rejected',
            'ready_for_pickup',
            'released',
        ];
    }

    public function payment_statuses()
    {
        return [
            'pending_payment',
            'payment_submitted',
            'payment_verified',
            'payment_rejected',
        ];
    }

    public function complaint_statuses()
    {
        return [
            'submitted',
            'under_review',
            'needs_info',
            'investigating',
            'resolved',
            'closed',
            'dismissed',
        ];
    }

    public function complaint_priorities()
    {
        return ['low', 'medium', 'high'];
    }

    public function complaint_categories()
    {
        return [
            'noise_complaint' => 'Noise complaint',
            'sanitation' => 'Sanitation',
            'neighborhood_dispute' => 'Neighborhood dispute',
            'public_disturbance' => 'Public disturbance',
            'property_concern' => 'Property concern',
            'business_related_concern' => 'Business-related concern',
            'other' => 'Other',
        ];
    }

    public function community_categories()
    {
        return [
            'announcement' => 'Announcement',
            'event' => 'Event',
            'program' => 'Program',
            'advisory' => 'Advisory',
            'resource' => 'Resource',
        ];
    }

    public function services()
    {
        $sql = "SELECT id, name FROM services ORDER BY name ASC";
        return $this->db->raw($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function overview_summary()
    {
        $requests = $this->db->raw(
            "SELECT
                COUNT(*) AS total_requests,
                SUM(CASE WHEN status IN ('submitted', 'under_review', 'needs_info') THEN 1 ELSE 0 END) AS pending_requests
             FROM service_requests"
        )->fetch(PDO::FETCH_ASSOC);

        $payments = $this->db->raw(
            "SELECT
                COUNT(*) AS total_payments,
                SUM(CASE WHEN payment_status = 'payment_verified' THEN 1 ELSE 0 END) AS verified_payments,
                SUM(CASE WHEN payment_status = 'payment_verified' THEN amount ELSE 0 END) AS verified_amount
             FROM payments"
        )->fetch(PDO::FETCH_ASSOC);

        $complaints = $this->db->raw(
            "SELECT
                COUNT(*) AS total_complaints,
                SUM(CASE WHEN status IN ('submitted', 'under_review', 'needs_info', 'investigating') THEN 1 ELSE 0 END) AS open_complaints
             FROM complaints"
        )->fetch(PDO::FETCH_ASSOC);

        $community = $this->db->raw(
            "SELECT
                COUNT(*) AS total_posts,
                SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) AS published_posts,
                SUM(CASE WHEN category = 'event' AND event_date >= CURDATE() THEN 1 ELSE 0 END) AS upcoming_events
             FROM community_posts"
        )->fetch(PDO::FETCH_ASSOC);

        return [
            'total_requests' => (int) ($requests['total_requests'] ?? 0),
            'pending_requests' => (int) ($requests['pending_requests'] ?? 0),
            'total_payments' => (int) ($payments['total_payments'] ?? 0),
            'verified_payments' => (int) ($payments['verified_payments'] ?? 0),
            'verified_amount' => (float) ($payments['verified_amount'] ?? 0),
            'total_complaints' => (int) ($complaints['total_complaints'] ?? 0),
            'open_complaints' => (int) ($complaints['open_complaints'] ?? 0),
            'total_posts' => (int) ($community['total_posts'] ?? 0),
            'published_posts' => (int) ($community['published_posts'] ?? 0),
            'upcoming_events' => (int) ($community['upcoming_events'] ?? 0),
        ];
    }

    public function request_summary(array $filters)
    {
        [$where_sql, $params] = $this->requestWhere($filters);

        $row = $this->db->raw(
            "SELECT
                COUNT(*) AS total_requests,
                SUM(CASE WHEN sr.status = 'submitted' THEN 1 ELSE 0 END) AS submitted_count,
                SUM(CASE WHEN sr.status = 'under_review' THEN 1 ELSE 0 END) AS under_review_count,
                SUM(CASE WHEN sr.status = 'needs_info' THEN 1 ELSE 0 END) AS needs_info_count,
                SUM(CASE WHEN sr.status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
                SUM(CASE WHEN sr.status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count,
                SUM(CASE WHEN sr.status = 'ready_for_pickup' THEN 1 ELSE 0 END) AS ready_for_pickup_count,
                SUM(CASE WHEN sr.status = 'released' THEN 1 ELSE 0 END) AS released_count
             FROM service_requests sr
             INNER JOIN services s ON s.id = sr.service_id
             {$where_sql}",
            $params
        )->fetch(PDO::FETCH_ASSOC);

        $month_row = $this->db->raw(
            "SELECT COUNT(*) AS requests_this_month
             FROM service_requests
             WHERE created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
               AND created_at < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)"
        )->fetch(PDO::FETCH_ASSOC);

        $top_service = $this->db->raw(
            "SELECT s.name, COUNT(*) AS total
             FROM service_requests sr
             INNER JOIN services s ON s.id = sr.service_id
             {$where_sql}
             GROUP BY s.id, s.name
             ORDER BY total DESC, s.name ASC
             LIMIT 1",
            $params
        )->fetch(PDO::FETCH_ASSOC);

        return [
            'total_requests' => (int) ($row['total_requests'] ?? 0),
            'submitted_count' => (int) ($row['submitted_count'] ?? 0),
            'under_review_count' => (int) ($row['under_review_count'] ?? 0),
            'needs_info_count' => (int) ($row['needs_info_count'] ?? 0),
            'approved_count' => (int) ($row['approved_count'] ?? 0),
            'rejected_count' => (int) ($row['rejected_count'] ?? 0),
            'ready_for_pickup_count' => (int) ($row['ready_for_pickup_count'] ?? 0),
            'released_count' => (int) ($row['released_count'] ?? 0),
            'requests_this_month' => (int) ($month_row['requests_this_month'] ?? 0),
            'most_requested_service' => $top_service['name'] ?? 'None yet',
            'most_requested_service_total' => (int) ($top_service['total'] ?? 0),
        ];
    }

    public function request_rows(array $filters, $limit = 200)
    {
        [$where_sql, $params] = $this->requestWhere($filters);
        $limit_sql = $this->limitSql($limit);

        $sql = "SELECT sr.reference_no, sr.status, sr.created_at,
                       s.name AS service_name, s.requires_payment,
                       CONCAT(u.first_name, ' ', u.last_name) AS resident_name,
                       CASE
                           WHEN s.requires_payment = 1 THEN COALESCE(p.payment_status, 'pending_payment')
                           ELSE NULL
                       END AS payment_status,
                       CASE WHEN rfd.id IS NULL THEN 0 ELSE 1 END AS has_final_document
                FROM service_requests sr
                INNER JOIN services s ON s.id = sr.service_id
                INNER JOIN users u ON u.id = sr.user_id
                LEFT JOIN payments p ON p.request_id = sr.id
                LEFT JOIN request_final_documents rfd ON rfd.request_id = sr.id
                {$where_sql}
                ORDER BY sr.created_at DESC
                {$limit_sql}";

        return $this->db->raw($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function payment_summary(array $filters)
    {
        [$where_sql, $params] = $this->paymentWhere($filters);

        $row = $this->db->raw(
            "SELECT
                COUNT(*) AS total_payments,
                SUM(CASE WHEN p.payment_status = 'pending_payment' THEN 1 ELSE 0 END) AS pending_payment_count,
                SUM(CASE WHEN p.payment_status = 'payment_submitted' THEN 1 ELSE 0 END) AS payment_submitted_count,
                SUM(CASE WHEN p.payment_status = 'payment_verified' THEN 1 ELSE 0 END) AS payment_verified_count,
                SUM(CASE WHEN p.payment_status = 'payment_rejected' THEN 1 ELSE 0 END) AS payment_rejected_count,
                SUM(p.amount) AS expected_amount,
                SUM(CASE WHEN p.payment_status = 'payment_verified' THEN p.amount ELSE 0 END) AS verified_amount
             FROM payments p
             INNER JOIN service_requests sr ON sr.id = p.request_id
             INNER JOIN services s ON s.id = sr.service_id
             {$where_sql}",
            $params
        )->fetch(PDO::FETCH_ASSOC);

        return [
            'total_payments' => (int) ($row['total_payments'] ?? 0),
            'pending_payment_count' => (int) ($row['pending_payment_count'] ?? 0),
            'payment_submitted_count' => (int) ($row['payment_submitted_count'] ?? 0),
            'payment_verified_count' => (int) ($row['payment_verified_count'] ?? 0),
            'payment_rejected_count' => (int) ($row['payment_rejected_count'] ?? 0),
            'expected_amount' => (float) ($row['expected_amount'] ?? 0),
            'verified_amount' => (float) ($row['verified_amount'] ?? 0),
        ];
    }

    public function payment_rows(array $filters, $limit = 200)
    {
        [$where_sql, $params] = $this->paymentWhere($filters);
        $limit_sql = $this->limitSql($limit);

        $sql = "SELECT p.amount, p.payment_method, p.payment_status, p.submitted_at, p.verified_at, p.remarks,
                       sr.reference_no,
                       s.name AS service_name,
                       CONCAT(u.first_name, ' ', u.last_name) AS resident_name
                FROM payments p
                INNER JOIN service_requests sr ON sr.id = p.request_id
                INNER JOIN services s ON s.id = sr.service_id
                INNER JOIN users u ON u.id = sr.user_id
                {$where_sql}
                ORDER BY p.created_at DESC
                {$limit_sql}";

        return $this->db->raw($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function complaint_summary(array $filters)
    {
        [$where_sql, $params] = $this->complaintWhere($filters);

        $row = $this->db->raw(
            "SELECT
                COUNT(*) AS total_complaints,
                SUM(CASE WHEN c.status = 'submitted' THEN 1 ELSE 0 END) AS submitted_count,
                SUM(CASE WHEN c.status = 'under_review' THEN 1 ELSE 0 END) AS under_review_count,
                SUM(CASE WHEN c.status = 'needs_info' THEN 1 ELSE 0 END) AS needs_info_count,
                SUM(CASE WHEN c.status = 'investigating' THEN 1 ELSE 0 END) AS investigating_count,
                SUM(CASE WHEN c.status = 'resolved' THEN 1 ELSE 0 END) AS resolved_count,
                SUM(CASE WHEN c.status = 'closed' THEN 1 ELSE 0 END) AS closed_count,
                SUM(CASE WHEN c.status = 'dismissed' THEN 1 ELSE 0 END) AS dismissed_count,
                SUM(CASE WHEN c.status IN ('submitted', 'under_review', 'needs_info', 'investigating') THEN 1 ELSE 0 END) AS open_count
             FROM complaints c
             {$where_sql}",
            $params
        )->fetch(PDO::FETCH_ASSOC);

        $top_category = $this->db->raw(
            "SELECT c.category, COUNT(*) AS total
             FROM complaints c
             {$where_sql}
             GROUP BY c.category
             ORDER BY total DESC, c.category ASC
             LIMIT 1",
            $params
        )->fetch(PDO::FETCH_ASSOC);

        return [
            'total_complaints' => (int) ($row['total_complaints'] ?? 0),
            'submitted_count' => (int) ($row['submitted_count'] ?? 0),
            'under_review_count' => (int) ($row['under_review_count'] ?? 0),
            'needs_info_count' => (int) ($row['needs_info_count'] ?? 0),
            'investigating_count' => (int) ($row['investigating_count'] ?? 0),
            'resolved_count' => (int) ($row['resolved_count'] ?? 0),
            'closed_count' => (int) ($row['closed_count'] ?? 0),
            'dismissed_count' => (int) ($row['dismissed_count'] ?? 0),
            'open_count' => (int) ($row['open_count'] ?? 0),
            'most_common_category' => $top_category['category'] ?? '',
            'most_common_category_total' => (int) ($top_category['total'] ?? 0),
        ];
    }

    public function complaint_rows(array $filters, $limit = 200)
    {
        [$where_sql, $params] = $this->complaintWhere($filters);
        $limit_sql = $this->limitSql($limit);

        $sql = "SELECT c.reference_no, c.complainant_name, c.subject, c.category,
                       c.priority, c.status, c.created_at,
                       CONCAT(assigned.first_name, ' ', assigned.last_name) AS assigned_to_name
                FROM complaints c
                LEFT JOIN users assigned ON assigned.id = c.assigned_to
                {$where_sql}
                ORDER BY c.created_at DESC
                {$limit_sql}";

        return $this->db->raw($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function community_summary(array $filters)
    {
        [$where_sql, $params] = $this->communityWhere($filters);

        $row = $this->db->raw(
            "SELECT
                COUNT(*) AS total_posts,
                SUM(CASE WHEN cp.is_published = 1 THEN 1 ELSE 0 END) AS published_count,
                SUM(CASE WHEN cp.is_published = 0 THEN 1 ELSE 0 END) AS unpublished_count,
                SUM(CASE WHEN cp.is_featured = 1 THEN 1 ELSE 0 END) AS featured_count,
                SUM(CASE WHEN cp.category = 'announcement' THEN 1 ELSE 0 END) AS announcement_count,
                SUM(CASE WHEN cp.category = 'event' THEN 1 ELSE 0 END) AS event_count,
                SUM(CASE WHEN cp.category = 'advisory' THEN 1 ELSE 0 END) AS advisory_count,
                SUM(CASE WHEN cp.category = 'program' THEN 1 ELSE 0 END) AS program_count,
                SUM(CASE WHEN cp.category = 'resource' THEN 1 ELSE 0 END) AS resource_count,
                SUM(CASE WHEN cp.category = 'event' AND cp.event_date >= CURDATE() THEN 1 ELSE 0 END) AS upcoming_event_count
             FROM community_posts cp
             {$where_sql}",
            $params
        )->fetch(PDO::FETCH_ASSOC);

        return [
            'total_posts' => (int) ($row['total_posts'] ?? 0),
            'published_count' => (int) ($row['published_count'] ?? 0),
            'unpublished_count' => (int) ($row['unpublished_count'] ?? 0),
            'featured_count' => (int) ($row['featured_count'] ?? 0),
            'announcement_count' => (int) ($row['announcement_count'] ?? 0),
            'event_count' => (int) ($row['event_count'] ?? 0),
            'advisory_count' => (int) ($row['advisory_count'] ?? 0),
            'program_count' => (int) ($row['program_count'] ?? 0),
            'resource_count' => (int) ($row['resource_count'] ?? 0),
            'upcoming_event_count' => (int) ($row['upcoming_event_count'] ?? 0),
        ];
    }

    public function community_rows(array $filters, $limit = 200)
    {
        [$where_sql, $params] = $this->communityWhere($filters);
        $limit_sql = $this->limitSql($limit);

        $sql = "SELECT cp.title, cp.category, cp.is_published, cp.is_featured,
                       cp.published_at, cp.created_at, cp.event_date, cp.resource_link
                FROM community_posts cp
                {$where_sql}
                ORDER BY cp.created_at DESC
                {$limit_sql}";

        return $this->db->raw($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function charts_data()
    {
        return [
            'request_status' => $this->request_status_chart_data(),
            'request_service' => $this->request_service_chart_data(),
            'request_monthly' => $this->request_monthly_chart_data(),
            'payment_status' => $this->payment_status_chart_data(),
            'complaint_status' => $this->complaint_status_chart_data(),
            'complaint_category' => $this->complaint_category_chart_data(),
            'community_category' => $this->community_category_chart_data(),
            'community_publish' => $this->community_publish_chart_data(),
        ];
    }

    public function request_status_chart_data()
    {
        $rows = $this->db->raw(
            "SELECT status AS chart_key, COUNT(*) AS total
             FROM service_requests
             GROUP BY status"
        )->fetchAll(PDO::FETCH_ASSOC);

        return $this->chartFromKeys($this->request_statuses(), $rows, 'request_status_display_label');
    }

    public function request_service_chart_data()
    {
        $rows = $this->db->raw(
            "SELECT s.name AS label, COUNT(sr.id) AS total
             FROM services s
             LEFT JOIN service_requests sr ON sr.service_id = s.id
             GROUP BY s.id, s.name
             ORDER BY total DESC, s.name ASC
             LIMIT 10"
        )->fetchAll(PDO::FETCH_ASSOC);

        return $this->chartFromRows($rows);
    }

    public function request_monthly_chart_data()
    {
        $rows = $this->db->raw(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS chart_key, COUNT(*) AS total
             FROM service_requests
             WHERE created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), '%Y-%m-01')
             GROUP BY chart_key
             ORDER BY chart_key ASC"
        )->fetchAll(PDO::FETCH_ASSOC);

        $counts = $this->countsByKey($rows);
        $labels = [];
        $values = [];

        $start = new DateTime('first day of this month');
        $start->modify('-11 months');

        for ($i = 0; $i < 12; $i++) {
            $month = (clone $start)->modify('+' . $i . ' months');
            $key = $month->format('Y-m');
            $labels[] = $month->format('M Y');
            $values[] = (int) ($counts[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    public function payment_status_chart_data()
    {
        $rows = $this->db->raw(
            "SELECT payment_status AS chart_key, COUNT(*) AS total
             FROM payments
             GROUP BY payment_status"
        )->fetchAll(PDO::FETCH_ASSOC);

        return $this->chartFromKeys($this->payment_statuses(), $rows, 'payment_status_display_label');
    }

    public function complaint_status_chart_data()
    {
        $rows = $this->db->raw(
            "SELECT status AS chart_key, COUNT(*) AS total
             FROM complaints
             GROUP BY status"
        )->fetchAll(PDO::FETCH_ASSOC);

        return $this->chartFromKeys($this->complaint_statuses(), $rows, 'complaint_status_display_label');
    }

    public function complaint_category_chart_data()
    {
        $rows = $this->db->raw(
            "SELECT category AS chart_key, COUNT(*) AS total
             FROM complaints
             GROUP BY category"
        )->fetchAll(PDO::FETCH_ASSOC);

        return $this->chartFromKeys(array_keys($this->complaint_categories()), $rows, 'complaint_category_label');
    }

    public function community_category_chart_data()
    {
        $rows = $this->db->raw(
            "SELECT category AS chart_key, COUNT(*) AS total
             FROM community_posts
             GROUP BY category"
        )->fetchAll(PDO::FETCH_ASSOC);

        return $this->chartFromKeys(array_keys($this->community_categories()), $rows, 'community_category_label');
    }

    public function community_publish_chart_data()
    {
        $row = $this->db->raw(
            "SELECT
                SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) AS published_count,
                SUM(CASE WHEN is_published = 0 THEN 1 ELSE 0 END) AS unpublished_count,
                SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) AS featured_count
             FROM community_posts"
        )->fetch(PDO::FETCH_ASSOC);

        return [
            'labels' => ['Published', 'Unpublished', 'Featured'],
            'values' => [
                (int) ($row['published_count'] ?? 0),
                (int) ($row['unpublished_count'] ?? 0),
                (int) ($row['featured_count'] ?? 0),
            ],
        ];
    }

    private function requestWhere(array $filters)
    {
        $where = [];
        $params = [];

        $this->addDateRange($where, $params, 'sr.created_at', $filters);

        if (!empty($filters['status'])) {
            $where[] = 'sr.status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['service_id'])) {
            $where[] = 'sr.service_id = ?';
            $params[] = (int) $filters['service_id'];
        }

        return $this->buildWhere($where, $params);
    }

    private function paymentWhere(array $filters)
    {
        $where = [];
        $params = [];

        $this->addDateRange($where, $params, 'p.created_at', $filters);

        if (!empty($filters['payment_status'])) {
            $where[] = 'p.payment_status = ?';
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['service_id'])) {
            $where[] = 'sr.service_id = ?';
            $params[] = (int) $filters['service_id'];
        }

        return $this->buildWhere($where, $params);
    }

    private function complaintWhere(array $filters)
    {
        $where = [];
        $params = [];

        $this->addDateRange($where, $params, 'c.created_at', $filters);

        if (!empty($filters['status'])) {
            $where[] = 'c.status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['category'])) {
            $where[] = 'c.category = ?';
            $params[] = $filters['category'];
        }

        if (!empty($filters['priority'])) {
            $where[] = 'c.priority = ?';
            $params[] = $filters['priority'];
        }

        return $this->buildWhere($where, $params);
    }

    private function communityWhere(array $filters)
    {
        $where = [];
        $params = [];

        $this->addDateRange($where, $params, 'cp.created_at', $filters);

        if (!empty($filters['category'])) {
            $where[] = 'cp.category = ?';
            $params[] = $filters['category'];
        }

        if ($filters['is_published'] !== '') {
            $where[] = 'cp.is_published = ?';
            $params[] = (int) $filters['is_published'];
        }

        if ($filters['is_featured'] !== '') {
            $where[] = 'cp.is_featured = ?';
            $params[] = (int) $filters['is_featured'];
        }

        return $this->buildWhere($where, $params);
    }

    private function addDateRange(array &$where, array &$params, $column, array $filters)
    {
        if (!empty($filters['from_date'])) {
            $where[] = $column . ' >= ?';
            $params[] = $filters['from_date'] . ' 00:00:00';
        }

        if (!empty($filters['to_date'])) {
            $where[] = $column . ' <= ?';
            $params[] = $filters['to_date'] . ' 23:59:59';
        }
    }

    private function buildWhere(array $where, array $params)
    {
        return [
            !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '',
            $params,
        ];
    }

    private function limitSql($limit)
    {
        $limit = (int) $limit;
        return $limit > 0 ? 'LIMIT ' . $limit : '';
    }

    private function chartFromKeys(array $keys, array $rows, $label_function)
    {
        $counts = $this->countsByKey($rows);
        $labels = [];
        $values = [];

        foreach ($keys as $key) {
            $labels[] = function_exists($label_function) ? $label_function($key) : ucwords(str_replace('_', ' ', $key));
            $values[] = (int) ($counts[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function chartFromRows(array $rows)
    {
        $labels = [];
        $values = [];

        foreach ($rows as $row) {
            $labels[] = (string) ($row['label'] ?? '');
            $values[] = (int) ($row['total'] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function countsByKey(array $rows)
    {
        $counts = [];

        foreach ($rows as $row) {
            $counts[(string) $row['chart_key']] = (int) $row['total'];
        }

        return $counts;
    }
}
