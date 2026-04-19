<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Assistant extends Controller
{
    private $max_question_length = 500;

    public function __construct()
    {
        parent::__construct();
        $this->call->library('Assistant_service');
        $this->call->library('Ai_service');
        $this->call->library('Ai_context_service');
    }

    public function index()
    {
        $this->renderAssistant('', null);
    }

    public function ask()
    {
        $question = trim($_POST['question'] ?? '');
        $errors = [];

        if ($question === '') {
            $errors[] = 'Type a question or choose a suggested question.';
        }

        if (strlen($question) > $this->max_question_length) {
            $errors[] = 'Question must be ' . $this->max_question_length . ' characters or fewer.';
        }

        if (!empty($errors)) {
            $this->session->set_flashdata('errors', $errors);
            $this->renderAssistant($question, null);
            return;
        }

        $services = $this->services();
        $user = auth_user() ?: [];
        $context = $this->Ai_context_service->build($services, $user);
        $ai_result = $this->Ai_service->answer($question, $context);

        if (!empty($ai_result['success']) && !empty($ai_result['answer'])) {
            $answer = $ai_result['answer'];
        } else {
            $fallback = $this->Assistant_service->answer($question, $services);
            $answer = $this->Ai_service->fallbackAnswer($fallback, $ai_result['reason'] ?? 'fallback');
        }

        $this->renderAssistant($question, $answer, $services, $user);
    }

    private function renderAssistant($question, $answer, $services = null, $user = null)
    {
        $services = is_array($services) ? $services : $this->services();
        $user = is_array($user) ? $user : (auth_user() ?: []);

        $this->call->view('assistant/index', [
            'title' => 'Virtual Help Assistant',
            'question' => $question,
            'answer' => $answer,
            'welcome_message' => $this->Assistant_service->welcomeMessage(),
            'assistant_mode_label' => $this->Ai_service->modeLabel(),
            'suggestions' => !empty($answer['suggestions'])
                ? $answer['suggestions']
                : $this->suggestionsForRole($user['role'] ?? 'guest'),
            'services' => $services,
            'current_role' => $user['role'] ?? 'guest',
            'max_question_length' => $this->max_question_length,
        ]);
    }

    private function services()
    {
        return safe_db_rows(
            "SELECT id, name, slug, description, requirements_text, fee, requires_payment, is_active
             FROM services
             WHERE is_active = 1
             ORDER BY name ASC"
        );
    }

    private function suggestionsForRole($role)
    {
        if ($role === 'resident') {
            return [
                'How do I track my request?',
                'Why cannot I download my final document yet?',
                'How do I submit payment proof?',
                'How do I file a complaint?',
            ];
        }

        if ($role === 'staff') {
            return [
                'How should staff review paid requests?',
                'When can staff upload a final document?',
                'How do complaint statuses work?',
                'What does Needs Info mean?',
            ];
        }

        if ($role === 'admin') {
            return [
                'Where can admins view reports?',
                'How do CSV exports work?',
                'How do I manage community posts?',
                'How do admin audit logs help?',
            ];
        }

        return $this->Assistant_service->defaultSuggestions();
    }
}
