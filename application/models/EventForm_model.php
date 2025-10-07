<?php defined('BASEPATH') or exit('No direct script access allowed');


class EventForm_model extends CI_Model
{
    protected $table = 'event_form';

    public function create_default($event_id, $user_id)
    {


        $exists = $this->db->get_where($this->table, [
            'event_id' => $event_id,
            'is_default' => '1'
        ])->num_rows();


        if ($exists > 0) {
            return; // sudah ada form default, tidak perlu tambah
        }







        $data = [
            'event_id' => $event_id,
            'name' => 'Form Default',
            'description' => 'Form bawaan otomatis untuk event ini',
            'status' => 'draft',
            'version' => 1,
            'is_default' => '1',
            'created_by' => $user_id
        ];
        $this->db->insert($this->table, $data);
        // return $this->db->insert_id();
    }

    public function get_default_by_event($event_id)
    {
        return $this->db
            ->where(['event_id' => $event_id, 'is_default' => '1'])
            ->get($this->table)->row();
    }
}
