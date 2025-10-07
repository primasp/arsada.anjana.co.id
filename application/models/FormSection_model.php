<?php defined('BASEPATH') or exit('No direct script access allowed');


class FormSection_model extends CI_Model
{
    protected $table = 'form_section';


    public function list_by_form($form_id)
    {
        return $this->db->order_by('sort_order')
            ->get_where($this->table, ['form_id' => $form_id])->result();
    }
    public function create($data)
    {
        $this->db->insert($this->table, $data);
        // return $this->db->insert_id();
    }
}
