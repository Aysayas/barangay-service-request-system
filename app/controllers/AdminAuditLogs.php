<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AdminAuditLogs extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->model('Audit_log_model');
    }

    public function index()
    {
        $search = trim($_GET['search'] ?? '');
        $action = trim($_GET['action'] ?? '');

        $this->call->view('admin/audit_logs/index', [
            'title' => 'Audit Logs',
            'logs' => $this->Audit_log_model->list_for_admin($search, $action),
            'search' => $search,
            'action' => $action,
        ]);
    }
}
