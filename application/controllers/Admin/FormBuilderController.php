<?php
defined('BASEPATH') or exit('No direct script access allowed');


class FormBuilderController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('role_id_ap') !== "RU0001") {
            redirect('Login');
        }
        $this->load->model(['Event_model', 'EventForm_model', 'FormQuestion_model', 'FormSection_model', 'FormQuestionOption_model']);
        $this->load->model('UserModel', 'um');
    }

    public function index($event_id)
    {
        $data['script'] = 'js/admin.js';

        $data['css'] = ['css/event.css'];


        $event = $this->Event_model->find($event_id);
        if (!$event) show_404();


        $form = $this->EventForm_model->get_default_by_event($event_id);
        // return var_dump($form);
        // die;

        // return var_dump($form->form_id);
        // die;
        $data['title'] = 'Form Builder untuk ' . $event->title;
        $data['event'] = $event;
        $data['form'] = $form;
        $data['sections'] = $this->FormSection_model->list_by_form($form->form_id);
        $data['questions'] = $this->FormQuestion_model->list_by_form_grouped($form->form_id);



        // return var_dump($data['questions']);
        // die;







        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');

        $this->load->view('admin/builder/index', $data);
        $this->load->view('templates/footer');
        $this->load->view('modal/modalRenderQuestion');
        $this->load->view('modal/modalOptionQuestion');
        $this->load->view('modal/modalQuestion');
        $this->load->view('modal/modalSection');
    }

    public function store_section()
    {
        $form_id = $this->input->post('form_id');

        // return var_dump($form_id);
        // die;
        $payload = [
            'form_id' => $form_id,
            'title' => $this->input->post('title', true),
            'description' => $this->input->post('description', true),
            'sort_order' => (int)$this->input->post('sort_order') ?: 1,
        ];
        $id = $this->FormSection_model->create($payload);

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true, 'section_id' => $id]));
        // return $this->_json_ok(['section_id' => $id]);
    }

    public function store_question()
    {
        // $isRequired = $this->input->post('is_required', true);

        $payload = [
            'form_id'      => $this->input->post('form_id', true),
            'section_id'   => $this->input->post('section_id', true),
            'question_type' => $this->input->post('question_type', true),
            'label'        => $this->input->post('label', true),
            'placeholder'  => $this->input->post('placeholder', true),
            // 'is_required'  => $this->input->post('is_required') ? true : false,
            // 'is_required'   => ($isRequired == '1' || $isRequired === true),
            'is_required'  => filter_var($this->input->post('is_required'), FILTER_VALIDATE_BOOLEAN),
            'sort_order'   => 1
        ];

        // return var_dump($payload);
        // die;

        $id = $this->FormQuestion_model->create($payload);

        // === Jika tipe select atau single_choice, siapkan slot opsi default
        // if (in_array($payload['question_type'], ['select', 'single_choice'])) {
        //     $defaultOption = [
        //         'question_id'  => $id,
        //         'option_label' => 'Pilihan 1',
        //         'option_value' => 'pilihan_1',
        //         'sort_order'   => 1,
        //         'is_active'    => true,
        //         'created_at'   => date('Y-m-d H:i:s'),
        //         'updated_at'   => date('Y-m-d H:i:s')
        //     ];
        //     $this->FormQuestionOption_model->create($defaultOption);
        // }




        return $this->_json_ok(['question_id' => $id]);
    }

    public function store_option()
    {

        $question_id = $this->input->post('question_id', true);
        $label = trim($this->input->post('option_label', true));
        $value = trim($this->input->post('option_value', true));

        if (!$question_id || !$label) {
            return $this->_json_error("Data tidak lengkap.");
        }

        $question = $this->FormQuestion_model->find($question_id);

        if (!$question) {
            return $this->_json_error("Pertanyaan tidak ditemukan.");
        }

        // Validasi khusus untuk single_choice
        // if ($question->question_type === 'single_choice') {
        //     $count = $this->FormQuestionOption_model->count_by_question($question_id);
        //     if ($count >= 1) {
        //         return $this->_json_error("Tipe single_choice hanya boleh memiliki satu opsi.");
        //     }
        // }

        // $payload = [
        //     'question_id'  => $this->input->post('question_id', true),
        //     'option_label' => $this->input->post('option_label', true),
        //     'option_value' => $this->input->post('option_value', true),
        //     'sort_order'   => 1
        // ];

        $payload = [
            'question_id'  => $question_id,
            'option_label' => $label,
            'option_value' => $value ?: strtolower(preg_replace('/\s+/', '_', $label)),
            'sort_order'   => 1,
            'is_active'    => true,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s')
        ];

        $id = $this->FormQuestionOption_model->create($payload);
        return $this->_json_ok(['option_id' => $id]);
    }



    public function delete_section($id)
    {
        // Pastikan section ada
        $section = $this->FormSection_model->find($id);
        if (!$section) {
            return $this->_json_error("Section tidak ditemukan.");
        }

        $ok = $this->FormSection_model->delete($id);

        // Jika berhasil, nonaktifkan semua pertanyaan dalam section ini
        if ($ok) {
            $this->FormSection_model->deactivate_by_section($id);
            return $this->_json_ok([
                'deleted' => true,
                'message' => 'Section dan semua pertanyaan terkait dinonaktifkan.'
            ]);
        } else {
            return $this->_json_error("Gagal menonaktifkan section.");
        }





        // return $this->_json_ok(['deleted' => $ok]);
    }



    public function sort_items()
    {
        // return var_dump("212");
        // die;
        $type = $this->input->post('type'); // 'section'|'question'|'option'
        $items = $this->input->post('items'); // [{id:1, sort_order:1}, ...]
        if ($type === 'section') {
            $this->FormSection_model->bulk_sort($items);
        } elseif ($type === 'question') {
            $this->FormQuestion_model->bulk_sort($items);
        } else {
            $this->FormOption_model->bulk_sort($items);
        }
        return $this->_json_ok();
    }




    //delete question
    // public function delete($question_id = null)
    // {
    //     // Validasi ID
    //     if (empty($question_id)) {
    //         return $this->_json_error("ID pertanyaan tidak ditemukan.");
    //     }

    //     // Pastikan pertanyaan masih ada
    //     $question = $this->FormQuestion_model->find($question_id);
    //     if (!$question) {
    //         return $this->_json_error("Pertanyaan tidak ditemukan di database.");
    //     }
    // }

    public function delete_question($id)
    {
        // return $this->db->where('question_id',$id)->delete($this->table);
        $ok = $this->FormQuestion_model->delete($id);
        return $this->_json_ok(['deleted' => $ok]);
    }


    private function _json_ok($data = [])
    {
        $data = array_merge(['ok' => true], $data);
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
    private function _json_error($message)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['ok' => false, 'error' => $message]));
    }
}
