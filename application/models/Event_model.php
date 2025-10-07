<?php defined('BASEPATH') or exit('No direct script access allowed');


class Event_model extends CI_Model
{
    protected $table = 'event';

    public function list_all()
    {
        return $this->db->order_by('created_at', 'DESC')->get($this->table)->result();
    }

    public function find($id)
    {
        return $this->db->get_where($this->table, ['event_id' => $id])->row();
    }

    public function create($payload)
    {
        // Query PostgreSQL pakai RETURNING untuk ambil event_id otomatis
        $sql = "INSERT INTO {$this->table}
            (organizer_id, event_code, title, description, venue, timezone, start_at, end_at,
             status, max_participants, is_public)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            RETURNING event_id";

        $query = $this->db->query($sql, [
            $payload['organizer_id'],
            $payload['event_code'],
            $payload['title'],
            $payload['description'],
            $payload['venue'],
            $payload['timezone'],
            $payload['start_at'],
            $payload['end_at'],
            $payload['status'],
            $payload['max_participants'],
            $payload['is_public']
        ]);

        $row = $query->row();
        return $row ? $row->event_id : null;
    }
}
