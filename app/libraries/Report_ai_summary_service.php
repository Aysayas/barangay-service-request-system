<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Report_ai_summary_service
{
    private $enabled;
    private $ai;
    private $max_summary_chars;

    public function __construct()
    {
        $this->enabled = filter_var(config_item('ai_report_summaries_enabled'), FILTER_VALIDATE_BOOLEAN);
        $this->max_summary_chars = max(200, min(2000, (int) config_item('ai_max_report_summary_chars')));

        if (class_exists('Ai_service')) {
            $this->ai = new Ai_service();
        }
    }

    public function overview(array $summary, array $charts = [])
    {
        $payload = [
            'scope' => 'overall reports dashboard',
            'metrics' => $summary,
            'chart_highlights' => [
                'requests_by_status' => $this->chartPairs($charts['request_status'] ?? []),
                'requests_by_service' => $this->chartPairs($charts['request_service'] ?? []),
                'payments_by_status' => $this->chartPairs($charts['payment_status'] ?? []),
                'complaints_by_status' => $this->chartPairs($charts['complaint_status'] ?? []),
                'community_by_category' => $this->chartPairs($charts['community_category'] ?? []),
            ],
        ];

        return $this->summarize('overview', $payload, $this->fallbackOverview($summary, $charts));
    }

    public function requests(array $summary, array $filters)
    {
        $payload = [
            'scope' => 'filtered request report',
            'filters' => $this->cleanFilters($filters),
            'metrics' => $summary,
        ];

        return $this->summarize('requests', $payload, $this->fallbackRequests($summary, $filters));
    }

    public function payments(array $summary, array $filters)
    {
        $payload = [
            'scope' => 'filtered payment report',
            'filters' => $this->cleanFilters($filters),
            'metrics' => $summary,
        ];

        return $this->summarize('payments', $payload, $this->fallbackPayments($summary, $filters));
    }

    public function complaints(array $summary, array $filters)
    {
        $payload = [
            'scope' => 'filtered complaint report',
            'filters' => $this->cleanFilters($filters),
            'metrics' => array_merge($summary, [
                'most_common_category_label' => !empty($summary['most_common_category'])
                    ? complaint_category_label($summary['most_common_category'])
                    : 'None yet',
            ]),
        ];

        return $this->summarize('complaints', $payload, $this->fallbackComplaints($summary, $filters));
    }

    public function community(array $summary, array $filters)
    {
        $payload = [
            'scope' => 'filtered community report',
            'filters' => $this->cleanFilters($filters),
            'metrics' => $summary,
        ];

        return $this->summarize('community', $payload, $this->fallbackCommunity($summary, $filters));
    }

    private function summarize($type, array $payload, $fallback_text)
    {
        if (!$this->enabled || empty($this->ai)) {
            return $this->fallback($fallback_text, $this->enabled ? 'ai_service_unavailable' : 'disabled');
        }

        $instructions = implode("\n", [
            'You write admin-facing report summaries for eBarangayHub.',
            'Use only the JSON data provided by PHP. Do not invent counts, trends, dates, percentages, names, or causes.',
            'If data is zero or unavailable, say that plainly.',
            'Mention when filters are active by using the provided filter values.',
            'Write one concise paragraph of 2 to 3 sentences.',
            'Tone: practical, calm, and useful for barangay admins.',
            'Avoid vague phrasing such as "interesting trend" unless the provided numbers clearly support it.',
            'Do not give legal, policy, or disciplinary advice.',
            'Do not recommend actions beyond simple follow-up observations like review pending items, verify submitted proofs, or publish prepared content.',
        ]);

        $input = implode("\n\n", [
            'Report type: ' . $type,
            'Structured report data:',
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'Write the summary now. Stay grounded in these numbers only.',
        ]);

        $result = $this->ai->generateText(
            $input,
            $instructions,
            ['task' => 'report_summary_' . $type],
            [
                'max_tokens' => 240,
                'temperature' => 0.15,
                'max_chars' => $this->max_summary_chars,
            ]
        );

        if (!empty($result['success']) && !empty($result['text'])) {
            $summary_text = $this->cleanSummaryText($result['text']);

            if ($this->isValidSummaryText($summary_text)) {
                return [
                    'text' => $summary_text,
                    'source' => 'ai',
                    'source_label' => 'AI-Assisted Summary',
                ];
            }

            return $this->fallback($fallback_text, 'bad_output');
        }

        return $this->fallback($fallback_text, $result['reason'] ?? 'fallback');
    }

    private function fallback($text, $reason)
    {
        $this->logFallback($reason);

        return [
            'text' => $text,
            'source' => 'fallback',
            'source_label' => 'Fallback Summary',
            'fallback_reason' => $reason,
        ];
    }

    private function fallbackOverview(array $summary, array $charts)
    {
        $top_service = $this->topChartLabel($charts['request_service'] ?? []);

        $text = 'The reports dashboard currently includes ' . (int) ($summary['total_requests'] ?? 0) . ' request(s), '
            . (int) ($summary['total_payments'] ?? 0) . ' payment record(s), '
            . (int) ($summary['total_complaints'] ?? 0) . ' complaint(s), and '
            . (int) ($summary['total_posts'] ?? 0) . ' community post(s). '
            . (int) ($summary['pending_requests'] ?? 0) . ' request(s) are still pending review, and '
            . (int) ($summary['open_complaints'] ?? 0) . ' complaint(s) remain open.';

        if ($top_service !== '') {
            $text .= ' The most active request service in the chart is ' . $top_service . '.';
        }

        return $text;
    }

    private function fallbackRequests(array $summary, array $filters)
    {
        $focus = $this->filterPrefix($filters);
        $status_counts = [
            'submitted' => (int) ($summary['submitted_count'] ?? 0),
            'under review' => (int) ($summary['under_review_count'] ?? 0),
            'needs info' => (int) ($summary['needs_info_count'] ?? 0),
            'approved' => (int) ($summary['approved_count'] ?? 0),
            'rejected' => (int) ($summary['rejected_count'] ?? 0),
            'ready for pickup' => (int) ($summary['ready_for_pickup_count'] ?? 0),
            'released' => (int) ($summary['released_count'] ?? 0),
        ];

        return $focus . 'There are ' . (int) ($summary['total_requests'] ?? 0) . ' request(s) in this view. '
            . 'The largest status group is ' . $this->largestLabel($status_counts) . ', and '
            . ($summary['most_requested_service'] ?? 'None yet') . ' is the most requested service with '
            . (int) ($summary['most_requested_service_total'] ?? 0) . ' request(s).';
    }

    private function fallbackPayments(array $summary, array $filters)
    {
        $focus = $this->filterPrefix($filters);

        return $focus . 'There are ' . (int) ($summary['total_payments'] ?? 0) . ' payment record(s) in this view. '
            . (int) ($summary['payment_verified_count'] ?? 0) . ' are verified, '
            . (int) ($summary['payment_submitted_count'] ?? 0) . ' are submitted for review, and '
            . (int) ($summary['payment_rejected_count'] ?? 0) . ' are rejected. '
            . 'The verified amount is ' . format_money($summary['verified_amount'] ?? 0) . ' out of '
            . format_money($summary['expected_amount'] ?? 0) . ' expected.';
    }

    private function fallbackComplaints(array $summary, array $filters)
    {
        $focus = $this->filterPrefix($filters);
        $category = !empty($summary['most_common_category'])
            ? complaint_category_label($summary['most_common_category'])
            : 'None yet';

        return $focus . 'There are ' . (int) ($summary['total_complaints'] ?? 0) . ' complaint(s) in this view, with '
            . (int) ($summary['open_count'] ?? 0) . ' still open. '
            . 'The most common category is ' . $category . ' with '
            . (int) ($summary['most_common_category_total'] ?? 0) . ' complaint(s).';
    }

    private function fallbackCommunity(array $summary, array $filters)
    {
        $focus = $this->filterPrefix($filters);

        return $focus . 'There are ' . (int) ($summary['total_posts'] ?? 0) . ' community post(s) in this view. '
            . (int) ($summary['published_count'] ?? 0) . ' are published, '
            . (int) ($summary['unpublished_count'] ?? 0) . ' are unpublished, and '
            . (int) ($summary['featured_count'] ?? 0) . ' are featured. '
            . 'There are ' . (int) ($summary['upcoming_event_count'] ?? 0) . ' upcoming event(s).';
    }

    private function cleanFilters(array $filters)
    {
        $clean = [];

        foreach ($filters as $key => $value) {
            if ($value === '' || $value === 0 || $value === null) {
                continue;
            }

            $clean[$key] = $value;
        }

        return !empty($clean) ? $clean : ['scope' => 'default all records'];
    }

    private function filterPrefix(array $filters)
    {
        $clean = $this->cleanFilters($filters);

        return count($clean) > 1 || !isset($clean['scope'])
            ? 'For the selected filters, '
            : '';
    }

    private function chartPairs(array $chart)
    {
        $pairs = [];
        $labels = $chart['labels'] ?? [];
        $values = $chart['values'] ?? [];

        foreach ($labels as $index => $label) {
            $pairs[(string) $label] = (int) ($values[$index] ?? 0);
        }

        return $pairs;
    }

    private function topChartLabel(array $chart)
    {
        $pairs = $this->chartPairs($chart);

        if (empty($pairs)) {
            return '';
        }

        arsort($pairs);
        $label = (string) array_key_first($pairs);

        return ((int) ($pairs[$label] ?? 0) > 0) ? $label : '';
    }

    private function largestLabel(array $counts)
    {
        arsort($counts);
        $label = (string) array_key_first($counts);

        return $label . ' (' . (int) ($counts[$label] ?? 0) . ')';
    }

    private function cleanSummaryText($text)
    {
        $text = trim((string) $text);
        $text = preg_replace('/\s+/', ' ', $text);

        if ($text === '') {
            return '';
        }

        if (strlen($text) <= $this->max_summary_chars) {
            return $text;
        }

        $text = substr($text, 0, max(0, $this->max_summary_chars - 1));
        $last_period = strrpos($text, '.');

        if ($last_period !== false && $last_period > ($this->max_summary_chars * 0.55)) {
            return trim(substr($text, 0, $last_period + 1));
        }

        return rtrim($text) . '...';
    }

    private function isValidSummaryText($text)
    {
        if ($text === '') {
            return false;
        }

        $normalized = strtolower($text);

        foreach ([
            'as an ai language model',
            'my training data',
            'i cannot access',
            'system prompt',
            'developer message',
            'json data is missing',
        ] as $bad_phrase) {
            if (strpos($normalized, $bad_phrase) !== false) {
                return false;
            }
        }

        return true;
    }

    private function logFallback($reason)
    {
        if (in_array($reason, ['disabled', 'incomplete_config'], true)) {
            return;
        }

        $log_dir = ROOT_DIR . 'runtime/logs';

        if (!is_dir($log_dir)) {
            @mkdir($log_dir, 0775, true);
        }

        $line = '[' . date('Y-m-d H:i:s') . '] AI report fallback summary used. '
            . json_encode(['reason' => $reason], JSON_UNESCAPED_SLASHES);

        @file_put_contents($log_dir . DIRECTORY_SEPARATOR . 'ai.log', $line . PHP_EOL, FILE_APPEND);
    }
}
