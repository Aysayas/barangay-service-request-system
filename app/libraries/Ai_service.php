<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Ai_service
{
    private static $logged_config_skip = false;

    private $enabled;
    private $provider;
    private $api_key;
    private $model;
    private $timeout;
    private $max_tokens;
    private $temperature;
    private $assistant_enabled;
    private $log_verbose;
    private $max_assistant_chars;
    private $max_report_summary_chars;

    public function __construct()
    {
        $this->enabled = filter_var(config_item('ai_enabled'), FILTER_VALIDATE_BOOLEAN);
        $this->provider = strtolower(trim((string) config_item('ai_provider')));
        $this->api_key = trim((string) config_item('ai_api_key'));
        $this->model = trim((string) config_item('ai_model'));
        $this->timeout = max(5, (int) config_item('ai_timeout'));
        $this->max_tokens = max(100, (int) config_item('ai_max_tokens'));
        $this->temperature = max(0, min(1, (float) config_item('ai_temperature')));
        $this->assistant_enabled = filter_var(config_item('ai_assistant_enabled'), FILTER_VALIDATE_BOOLEAN);
        $this->log_verbose = filter_var(config_item('ai_log_verbose'), FILTER_VALIDATE_BOOLEAN);
        $this->max_assistant_chars = max(300, min(3000, (int) config_item('ai_max_assistant_chars')));
        $this->max_report_summary_chars = max(200, min(2000, (int) config_item('ai_max_report_summary_chars')));
    }

    public function modeLabel()
    {
        if (!$this->enabled) {
            return 'Rule-based fallback mode';
        }

        if (!$this->assistant_enabled) {
            return 'Rule-based fallback mode';
        }

        if (!$this->isConfigured()) {
            return 'AI disabled by incomplete configuration';
        }

        if ($this->provider !== 'openai') {
            return 'Rule-based fallback mode';
        }

        return 'AI-assisted mode';
    }

    public function answer($question, array $context)
    {
        $question = trim((string) $question);

        if ($question === '') {
            return $this->failed('empty_question');
        }

        if (!$this->enabled) {
            $this->logConfigSkip('Skipped AI because AI_ENABLED is false.');
            return $this->failed('disabled');
        }

        if (!$this->assistant_enabled) {
            $this->logConfigSkip('Skipped assistant AI because AI_ASSISTANT_ENABLED is false.');
            return $this->failed('assistant_disabled');
        }

        if (!$this->isConfigured()) {
            $this->logConfigSkip('Skipped AI because configuration is incomplete.');
            return $this->failed('incomplete_config');
        }

        if ($this->provider !== 'openai') {
            $this->log('Skipped AI because provider is unsupported.', [
                'provider' => $this->provider,
            ]);

            return $this->failed('unsupported_provider');
        }

        if ($this->isLikelyOutOfScopeQuestion($question)) {
            $this->logVerbose('Skipped assistant AI because the question appears outside eBarangayHub scope.', [
                'task' => 'assistant_answer',
            ]);

            return $this->failed('out_of_scope');
        }

        return $this->callOpenAi($question, $context);
    }

    public function fallbackAnswer(array $fallback_answer, $reason = 'fallback')
    {
        $this->logFallback($reason);

        $fallback_answer['source'] = 'fallback';
        $fallback_answer['source_label'] = 'Rule-based fallback';
        $fallback_answer['fallback_reason'] = $reason;

        return $fallback_answer;
    }

    public function generateText($input, $instructions, array $metadata = [], array $options = [])
    {
        $input = trim((string) $input);
        $instructions = trim((string) $instructions);

        if ($input === '' || $instructions === '') {
            return $this->failed('empty_prompt');
        }

        if (!$this->enabled) {
            $this->logConfigSkip('Skipped AI because AI_ENABLED is false.');
            return $this->failed('disabled');
        }

        if (!$this->isConfigured()) {
            $this->logConfigSkip('Skipped AI because configuration is incomplete.');
            return $this->failed('incomplete_config');
        }

        if ($this->provider !== 'openai') {
            $this->log('Skipped AI because provider is unsupported.', [
                'provider' => $this->provider,
                'task' => $metadata['task'] ?? 'text_generation',
            ]);

            return $this->failed('unsupported_provider');
        }

        return $this->callOpenAiText($input, $instructions, $metadata, $options);
    }

    private function callOpenAi($question, array $context)
    {
        if (!function_exists('curl_init')) {
            $this->log('Skipped AI because the PHP cURL extension is not available.');
            return $this->failed('curl_unavailable');
        }

        $result = $this->callOpenAiText(
            $this->buildInput($question, $context),
            $this->buildInstructions($context['system_instructions'] ?? ''),
            ['task' => 'assistant_answer'],
            [
                'max_tokens' => $this->max_tokens,
                'temperature' => $this->temperature,
                'max_chars' => $this->max_assistant_chars,
            ]
        );

        if (empty($result['success'])) {
            return $result;
        }

        $reply = $this->cleanText($result['text'] ?? '', $this->max_assistant_chars);

        if ($reply === '') {
            return $this->failed('empty_reply');
        }

        if ($this->isUnsafeAssistantReply($reply, $question)) {
            $this->log('AI assistant reply failed scope or quality checks. Fallback will be used.', [
                'task' => 'assistant_answer',
            ]);

            return $this->failed('unsafe_reply');
        }

        return [
            'success' => true,
            'answer' => [
                'category' => 'ai_assisted',
                'reply' => $reply,
                'suggestions' => $this->suggestionsForContext($context['role_context'] ?? ''),
                'source' => 'ai',
                'source_label' => 'AI-assisted mode',
            ],
        ];
    }

    private function callOpenAiText($input, $instructions, array $metadata = [], array $options = [])
    {
        if (!function_exists('curl_init')) {
            $this->log('Skipped AI because the PHP cURL extension is not available.', [
                'task' => $metadata['task'] ?? 'text_generation',
            ]);
            return $this->failed('curl_unavailable');
        }

        $max_tokens = max(100, (int) ($options['max_tokens'] ?? $this->max_tokens));
        $temperature = max(0, min(1, (float) ($options['temperature'] ?? $this->temperature)));
        $task = $metadata['task'] ?? 'text_generation';
        $default_max_chars = strpos((string) $task, 'report_summary') === 0
            ? $this->max_report_summary_chars
            : $this->max_assistant_chars;
        $max_chars = max(200, min(4000, (int) ($options['max_chars'] ?? $default_max_chars)));

        $payload = [
            'model' => $this->model,
            'instructions' => $instructions,
            'input' => $input,
            'max_output_tokens' => $max_tokens,
            'temperature' => $temperature,
            'store' => false,
        ];

        $ch = curl_init('https://api.openai.com/v1/responses');

        if ($ch === false) {
            $this->log('AI request could not initialize cURL.');
            return $this->failed('curl_unavailable');
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->api_key,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($response === false || $status < 200 || $status >= 300) {
            $this->log('AI provider request failed. Fallback will be used.', [
                'provider' => $this->provider,
                'model' => $this->model,
                'task' => $task,
                'status' => $status,
                'curl_error' => $curl_error,
                'response' => $this->safeSnippet($response),
            ]);

            return $this->failed('provider_failed');
        }

        $decoded = json_decode((string) $response, true);

        if (!is_array($decoded)) {
            $this->log('AI provider returned invalid JSON. Fallback will be used.', [
                'provider' => $this->provider,
                'model' => $this->model,
                'task' => $task,
            ]);

            return $this->failed('invalid_json');
        }

        $reply = $this->cleanText($this->extractReply($decoded), $max_chars);

        if ($reply === '') {
            $this->log('AI provider returned an empty reply. Fallback will be used.', [
                'provider' => $this->provider,
                'model' => $this->model,
                'task' => $task,
                'response_id' => $decoded['id'] ?? '',
            ]);

            return $this->failed('empty_reply');
        }

        if ($this->looksLikeProviderArtifact($reply)) {
            $this->log('AI provider reply failed output checks. Fallback will be used.', [
                'provider' => $this->provider,
                'model' => $this->model,
                'task' => $task,
                'response_id' => $decoded['id'] ?? '',
            ]);

            return $this->failed('bad_output');
        }

        $this->log('AI response generated.', [
            'provider' => $this->provider,
            'model' => $this->model,
            'task' => $task,
            'response_id' => $decoded['id'] ?? '',
        ]);

        return [
            'success' => true,
            'text' => $reply,
            'response_id' => $decoded['id'] ?? '',
        ];
    }

    private function buildInstructions($context_instructions)
    {
        return implode("\n", [
            'You are eBarangayHub Assistant, a focused virtual help assistant for the eBarangayHub web app.',
            'Primary mission: help users understand services, requirements, requests, simulated payments, complaints, final documents, community information, reports, exports, charts, and navigation inside eBarangayHub.',
            'Use only the provided app context. If the context does not contain the answer, say what can be checked inside eBarangayHub instead of inventing facts.',
            'Stay in scope. For unrelated world knowledge, coding help, homework, entertainment, legal advice, medical advice, personal advice, or general chatbot questions, briefly redirect to eBarangayHub topics.',
            'Respect the user role guidance. Do not describe admin-only tools as available to guests, residents, or staff.',
            'Do not ask for or reveal passwords, API keys, SMTP credentials, uploaded file contents, private records, or database internals.',
            'Do not claim you performed actions. You can guide users to pages, but you cannot submit forms, approve requests, verify payments, upload files, or change records.',
            'When useful, provide short numbered steps using actual page names such as My Requests, My Complaints, Request Queue, Reports, Community, Services, or Audit Logs.',
            'Keep the tone warm, practical, and concise. Prefer 2 to 4 short sentences unless the user asks for steps.',
            trim((string) $context_instructions),
        ]);
    }

    private function buildInput($question, array $context)
    {
        return implode("\n\n", [
            'Grounding context:',
            trim((string) ($context['app_context'] ?? '')),
            'Role guidance:',
            trim((string) ($context['role_context'] ?? '')),
            'User question:',
            trim((string) $question),
            'Response requirements: answer naturally, stay scoped to eBarangayHub, avoid private data, and suggest the next page or action when helpful.',
        ]);
    }

    private function extractReply(array $decoded)
    {
        if (!empty($decoded['output_text']) && is_string($decoded['output_text'])) {
            return trim($decoded['output_text']);
        }

        $parts = [];

        foreach (($decoded['output'] ?? []) as $output_item) {
            foreach (($output_item['content'] ?? []) as $content_item) {
                if (($content_item['type'] ?? '') === 'output_text' && isset($content_item['text'])) {
                    $parts[] = trim((string) $content_item['text']);
                }
            }
        }

        return trim(implode("\n\n", array_filter($parts)));
    }

    private function isConfigured()
    {
        return $this->provider !== ''
            && $this->api_key !== ''
            && $this->model !== ''
            && $this->timeout > 0
            && $this->max_tokens > 0;
    }

    private function failed($reason)
    {
        return [
            'success' => false,
            'reason' => $reason,
        ];
    }

    private function logConfigSkip($message)
    {
        if (self::$logged_config_skip) {
            return;
        }

        $this->log($message, [
            'enabled' => $this->enabled ? 'true' : 'false',
            'provider' => $this->provider,
            'has_api_key' => $this->api_key !== '' ? 'true' : 'false',
            'model' => $this->model,
        ]);

        self::$logged_config_skip = true;
    }

    private function defaultSuggestions()
    {
        return [
            'How do I request Barangay Clearance?',
            'What does Under Review mean?',
            'How do simulated payments work?',
            'How do complaints work?',
        ];
    }

    private function suggestionsForContext($role_context)
    {
        $role_context = strtolower((string) $role_context);

        if (strpos($role_context, 'role: resident') !== false) {
            return [
                'How do I track my request?',
                'Why cannot I download my final document yet?',
                'How do I submit payment proof?',
                'How do I file a complaint?',
            ];
        }

        if (strpos($role_context, 'role: staff') !== false) {
            return [
                'How should staff review paid requests?',
                'When can staff upload final documents?',
                'How do complaint statuses work?',
                'What should staff do with Needs Info?',
            ];
        }

        if (strpos($role_context, 'role: admin') !== false) {
            return [
                'Where can admins view reports?',
                'How do CSV exports work?',
                'How do I manage community posts?',
                'What are audit logs for?',
            ];
        }

        return $this->defaultSuggestions();
    }

    private function logFallback($reason)
    {
        if (in_array($reason, ['disabled', 'incomplete_config'], true)) {
            return;
        }

        $this->log('AI fallback response used.', [
            'reason' => $reason,
            'provider' => $this->provider,
            'model' => $this->model,
        ]);
    }

    private function cleanText($text, $max_chars)
    {
        $text = trim((string) $text);
        $text = preg_replace('/\r\n|\r/', "\n", $text);
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        if (strlen($text) <= $max_chars) {
            return $text;
        }

        $text = substr($text, 0, max(0, $max_chars - 1));
        $last_period = strrpos($text, '.');

        if ($last_period !== false && $last_period > ($max_chars * 0.55)) {
            return trim(substr($text, 0, $last_period + 1));
        }

        return rtrim($text) . '...';
    }

    private function looksLikeProviderArtifact($text)
    {
        $normalized = $this->normalize($text);

        return $this->hasAny($normalized, [
            'as an ai language model',
            'my training data',
            'i do not have access to real time information',
            'i cannot access the provided context',
            'i cannot view your database',
            'openai policy',
            'system prompt',
            'developer message',
        ]);
    }

    private function isUnsafeAssistantReply($reply, $question)
    {
        if ($this->looksLikeProviderArtifact($reply)) {
            return true;
        }

        if ($this->isLikelyOutOfScopeQuestion($question)) {
            $normalized = $this->normalize($reply);

            return !$this->hasAny($normalized, [
                'ebarangayhub',
                'barangay',
                'service',
                'request',
                'payment',
                'complaint',
                'community',
                'document',
                'report',
                'assistant',
            ]);
        }

        return false;
    }

    private function isLikelyOutOfScopeQuestion($question)
    {
        $normalized = $this->normalize($question);

        if ($normalized === '') {
            return false;
        }

        return !$this->hasAny($normalized, [
            'ebarangayhub',
            'barangay',
            'service',
            'clearance',
            'certificate',
            'residency',
            'indigency',
            'business',
            'requirement',
            'requirements',
            'request',
            'requests',
            'status',
            'track',
            'tracking',
            'payment',
            'pay',
            'fee',
            'gcash',
            'maya',
            'cash',
            'proof',
            'reference',
            'complaint',
            'complaints',
            'incident',
            'evidence',
            'document',
            'download',
            'upload',
            'community',
            'announcement',
            'event',
            'advisory',
            'program',
            'resource',
            'report',
            'reports',
            'chart',
            'charts',
            'csv',
            'export',
            'audit',
            'login',
            'register',
            'account',
            'dashboard',
            'resident',
            'staff',
            'admin',
            'help',
            'assistant',
            'hello',
            'hi',
        ]);
    }

    private function normalize($value)
    {
        $value = strtolower((string) $value);
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value);
    }

    private function hasAny($text, array $keywords)
    {
        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    private function logVerbose($message, array $context = [])
    {
        if (!$this->log_verbose) {
            return;
        }

        $this->log($message, $context);
    }

    private function log($message, array $context = [])
    {
        $log_dir = ROOT_DIR . 'runtime/logs';

        if (!is_dir($log_dir)) {
            @mkdir($log_dir, 0775, true);
        }

        unset($context['api_key'], $context['authorization'], $context['password']);

        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message;

        if (!empty($context)) {
            $line .= ' ' . json_encode($context, JSON_UNESCAPED_SLASHES);
        }

        @file_put_contents($log_dir . DIRECTORY_SEPARATOR . 'ai.log', $line . PHP_EOL, FILE_APPEND);
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
