<?php defined('BASEPATH') or exit('No direct script access allowed');


class FormQuestion_model extends CI_Model
{
    protected $table = 'form_question';




    public function list_by_form_grouped($form_id)
    {
        // Ambil semua pertanyaan + opsi-nya
        $sql = "SELECT q.*, o.option_id, o.option_label, o.option_value,q.placeholder
            FROM event_manager.form_question q
            LEFT JOIN event_manager.form_question_option o 
                ON o.question_id = q.question_id
            WHERE q.form_id = ?
             AND q.aktif = '1'  
            ORDER BY q.section_id, q.sort_order, o.sort_order
        ";

        $result = $this->db->query($sql, [$form_id])->result();

        $grouped = [];
        foreach ($result as $row) {
            $sid = $row->section_id;
            $qid = $row->question_id;

            if (!isset($grouped[$sid][$qid])) {
                $grouped[$sid][$qid] = (object)[
                    'question_id'   => $row->question_id,
                    'section_id'    => $row->section_id,
                    'label'         => $row->label,
                    'question_type' => $row->question_type,
                    'is_required'   => $row->is_required,
                    'placeholder'   => $row->placeholder,
                    'options'       => []
                ];
            }

            if (!empty($row->option_id)) {
                $grouped[$sid][$qid]->options[] = (object)[
                    'option_id'    => $row->option_id,
                    'option_label' => $row->option_label,
                    'option_value' => $row->option_value
                ];
            }
        }

        return $grouped;
    }


    public function create($data)
    {
        $this->db->insert($this->table, $data);
        // return $this->db->insert_id();
    }

    public function delete($id)
    {
        // return $this->db->delete($this->table, ['question_id' => $id]);
        return $this->db->update($this->table, ['aktif' => '0'], ['question_id' => $id]);
    }

    public function find($question_id)
    {
        return $this->db->get_where($this->table, ['question_id' => $question_id])->row();
    }

    public function bulk_sort($items)
    {
        foreach ($items as $row) {
            $this->db->where('question_id', $row['id'])
                ->update($this->table, ['sort_order' => (int)$row['sort_order']]);
        }
    }
}
