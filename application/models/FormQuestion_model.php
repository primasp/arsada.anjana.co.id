<?php defined('BASEPATH') or exit('No direct script access allowed');


class FormQuestion_model extends CI_Model
{
    protected $table = 'form_question';


    public function list_by_form_grouped($form_id)
    {
        // return list pertanyaan per section (untuk builder)
        $rows = $this->db->order_by('section_id ASC, sort_order ASC')
            ->get_where($this->table, ['form_id' => $form_id])->result();
        $grouped = [];
        foreach ($rows as $r) {
            $grouped[$r->section_id ?: 0][] = $r; // 0 untuk no-section
        }
        return $grouped;
    }
}
