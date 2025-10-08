<?php defined('BASEPATH') or exit('No direct script access allowed');


class FormSection_model extends CI_Model
{
    protected $table = 'form_section';


    public function list_by_form($form_id)
    {
        // return $this->db->order_by('sort_order')
        //     ->get_where($this->table, ['form_id' => $form_id])->result();

        return $this->db->order_by('sort_order')
            ->get_where($this->table, [
                'form_id' => $form_id,
                'aktif'   => '1'  // hanya ambil section aktif
            ])->result();
    }
    public function create($data)
    {
        $this->db->insert($this->table, $data);
        // return $this->db->insert_id();
    }
    public function delete($id)
    {
        // return $this->db->delete($this->table, ['section_id' => $id]);
        return $this->db->update($this->table, ['aktif' => '0'], ['section_id' => $id]);
    }

    public function find($id)
    {
        return $this->db->get_where($this->table, ['section_id' => $id])->row();
    }

    public function deactivate_by_section($section_id)
    {
        // return $this->db->where('section_id', $section_id)
        //     ->update($this->table, [
        //         'aktif' => 0,
        //         'updated_at' => date('Y-m-d H:i:s')
        //     ]);

        $this->db->where('section_id', $section_id)
            ->update($this->table, [
                'aktif' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        // 2️⃣ Nonaktifkan semua pertanyaan di section tersebut
        $this->db->where('section_id', $section_id)
            ->update('event_manager.form_question', [
                'aktif' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        // 3️⃣ (Opsional) Nonaktifkan semua opsi pertanyaan juga
        $this->db->query("UPDATE event_manager.form_question_option o
        SET aktif = 0,
            updated_at = NOW()
        WHERE o.question_id IN (
            SELECT q.question_id 
            FROM event_manager.form_question q
            WHERE q.section_id = ?
        )
    ", [$section_id]);

        return true;
    }


    // FormSection_model
    public function bulk_sort($items)
    {
        foreach ($items as $row) {
            $this->db->where('section_id', $row['id'])
                ->update($this->table, ['sort_order' => (int)$row['sort_order']]);
        }
    }

    // FormQuestion_model

}
