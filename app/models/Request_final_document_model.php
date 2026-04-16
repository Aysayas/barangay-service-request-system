<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Request_final_document_model extends Model
{
    protected $table = 'request_final_documents';
    protected $primary_key = 'id';

    protected $fillable = [
        'request_id',
        'original_name',
        'stored_name',
        'file_path',
        'file_size',
        'file_type',
        'uploaded_by',
        'uploaded_at',
    ];

    public function find_for_request($request_id)
    {
        $sql = "SELECT rfd.*, CONCAT(u.first_name, ' ', u.last_name) AS uploaded_by_name
                FROM request_final_documents rfd
                INNER JOIN users u ON u.id = rfd.uploaded_by
                WHERE rfd.request_id = ?
                LIMIT 1";

        return $this->db->raw($sql, [(int) $request_id])->fetch(PDO::FETCH_ASSOC);
    }

    public function find_for_resident($request_id, $user_id)
    {
        $sql = "SELECT rfd.*, sr.status, sr.user_id, sr.reference_no,
                       CONCAT(u.first_name, ' ', u.last_name) AS uploaded_by_name
                FROM request_final_documents rfd
                INNER JOIN service_requests sr ON sr.id = rfd.request_id
                INNER JOIN users u ON u.id = rfd.uploaded_by
                WHERE rfd.request_id = ? AND sr.user_id = ?
                LIMIT 1";

        return $this->db->raw($sql, [(int) $request_id, (int) $user_id])->fetch(PDO::FETCH_ASSOC);
    }

    public function save_for_request($request_id, array $data)
    {
        $existing = $this->find_for_request((int) $request_id);

        $fields = [
            'request_id' => (int) $request_id,
            'original_name' => $data['original_name'],
            'stored_name' => $data['stored_name'],
            'file_path' => $data['file_path'],
            'file_size' => (int) $data['file_size'],
            'file_type' => $data['file_type'],
            'uploaded_by' => (int) $data['uploaded_by'],
            'uploaded_at' => date('Y-m-d H:i:s'),
        ];

        if (!empty($existing)) {
            $this->update((int) $existing['id'], $fields);
            return (int) $existing['id'];
        }

        return $this->insert($fields);
    }
}
