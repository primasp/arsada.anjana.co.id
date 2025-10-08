<?php
defined('BASEPATH') or exit('No direct script access allowed');

class FormQuestionOption_model extends CI_Model
{
    protected $table = 'form_question_option';

    public function create($data)
    {
        $this->db->insert($this->table, $data);
        // return $this->db->insert_id();
    }

    public function delete_by_question($question_id)
    {
        return $this->db->delete($this->table, ['question_id' => $question_id]);
    }

    public function count_by_question($question_id)
    {
        return $this->db
            ->where('question_id', $question_id)
            ->where('is_active', true)
            ->count_all_results($this->table);
    }
}
