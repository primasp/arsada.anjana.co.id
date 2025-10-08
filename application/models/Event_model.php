<?php defined('BASEPATH') or exit('No direct script access allowed');


class Event_model extends CI_Model
{
    protected $table = 'event';

    public function list_all()
    {
        // return $this->db->order_by('created_at', 'DESC')->get($this->table)->result();
        return $this->db
            ->where('aktif', '1') // hanya event aktif
            ->order_by('created_at', 'DESC')
            ->get($this->table)
            ->result();
    }

    public function list_public()
    {
        return $this->db
            ->where('is_public', true)
            ->where('is_active', true)
            ->where('aktif', '1')
            ->where('status', 'open')
            ->order_by('start_at', 'DESC')
            ->get($this->table)
            ->result();
        // ->result_array();
    }
    public function find_by_code($event_code)
    {
        return $this->db
            ->where('event_code', $event_code)
            ->get($this->table)
            ->row();
    }



    public function find($id)
    {
        // return $this->db->get_where($this->table, ['event_id' => $id])->row();
        return $this->db
            ->get_where($this->table, [
                'event_id' => $id,
                'aktif'    => '1'
            ])
            ->row();
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

    public function delete($id)
    {
        return $this->db->update($this->table, ['aktif' => '0'], ['event_id' => $id]);
    }
}
