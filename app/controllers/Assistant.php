<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Assistant extends Controller
{
    private $max_question_length = 300;

    public function __construct()
    {
        parent::__construct();
        $this->call->library('Assistant_service');
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

        $answer = $this->Assistant_service->answer($question, $this->services());
        $this->renderAssistant($question, $answer);
    }

    private function renderAssistant($question, $answer)
    {
        $this->call->view('assistant/index', [
            'title' => 'Virtual Help Assistant',
            'question' => $question,
            'answer' => $answer,
            'welcome_message' => $this->Assistant_service->welcomeMessage(),
            'suggestions' => !empty($answer['suggestions'])
                ? $answer['suggestions']
                : $this->Assistant_service->defaultSuggestions(),
            'services' => $this->services(),
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
}
