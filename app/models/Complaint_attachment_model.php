<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Complaint_attachment_model extends Model
{
    protected $table = 'complaint_attachments';
    protected $primary_key = 'id';
    protected $timestamps = false;

    protected $fillable = [
        'complaint_id',
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
            'complaint_id' => (int) $data['complaint_id'],
            'original_name' => $data['original_name'],
            'stored_name' => $data['stored_name'],
            'file_path' => $data['file_path'],
            'file_size' => (int) $data['file_size'],
            'file_type' => $data['file_type'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function for_complaint($complaint_id)
    {
        return $this->db
            ->table($this->table)
            ->where('complaint_id', (int) $complaint_id)
            ->order_by('created_at', 'ASC')
            ->get_all();
    }

    public function find_for_resident($attachment_id, $user_id)
    {
        $sql = "SELECT ca.*, c.reference_no, c.user_id
                FROM complaint_attachments ca
                INNER JOIN complaints c ON c.id = ca.complaint_id
                WHERE ca.id = ? AND c.user_id = ?
                LIMIT 1";

        return $this->db->raw($sql, [(int) $attachment_id, (int) $user_id])->fetch(PDO::FETCH_ASSOC);
    }

    public function find_for_staff($attachment_id)
    {
        $sql = "SELECT ca.*, c.reference_no
                FROM complaint_attachments ca
                INNER JOIN complaints c ON c.id = ca.complaint_id
                WHERE ca.id = ?
                LIMIT 1";

        return $this->db->raw($sql, [(int) $attachment_id])->fetch(PDO::FETCH_ASSOC);
    }
}
