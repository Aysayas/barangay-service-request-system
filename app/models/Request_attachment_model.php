<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Request_attachment_model extends Model
{
    protected $table = 'request_attachments';
    protected $primary_key = 'id';
    protected $timestamps = false;

    protected $fillable = [
        'request_id',
        'original_name',
        'stored_name',
        'file_path',
        'file_size',
        'file_type',
        'created_at',
    ];

    public function create_attachment(array $data)
    {
        return $this->insert([
            'request_id' => (int) $data['request_id'],
            'original_name' => $data['original_name'],
            'stored_name' => $data['stored_name'],
            'file_path' => $data['file_path'],
            'file_size' => (int) $data['file_size'],
            'file_type' => $data['file_type'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function for_request($request_id)
    {
        return $this->db
            ->table($this->table)
            ->where('request_id', (int) $request_id)
            ->order_by('created_at', 'ASC')
            ->get_all();
    }

    public function find_for_staff($attachment_id)
    {
        $sql = "SELECT ra.*, sr.reference_no
                FROM request_attachments ra
                INNER JOIN service_requests sr ON sr.id = ra.request_id
                WHERE ra.id = ?
                LIMIT 1";

        return $this->db->raw($sql, [(int) $attachment_id])->fetch(PDO::FETCH_ASSOC);
    }
}
