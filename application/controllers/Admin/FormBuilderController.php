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
        $this->load->model(['Event_model', 'EventForm_model', 'FormQuestion_model', 'FormSection_model']);
        $this->load->model('UserModel', 'um');
    }

    public function index($event_id)
    {
        $data['script'] = 'js/admin.js';

        $event = $this->Event_model->find($event_id);
        if (!$event) show_404();


        $form = $this->EventForm_model->get_default_by_event($event_id);

        // return var_dump($form->form_id);
        // die;
        $data['title'] = 'Form Builder untuk ' . $event->title;
        $data['event'] = $event;
        $data['form'] = $form;
        $data['sections'] = $this->FormSection_model->list_by_form($form->form_id);
        $data['questions'] = $this->FormQuestion_model->list_by_form_grouped($form->form_id);







        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');

        $this->load->view('admin/builder/index', $data);
        $this->load->view('templates/footer');
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
        $payload = [
            'form_id' => (int)$this->input->post('form_id'),
            'section_id' => $this->input->post('section_id') ?: null,
            'question_type' => $this->input->post('question_type', true),
            'label' => $this->input->post('label', true),
            'help_text' => $this->input->post('help_text', true),
            'placeholder' => $this->input->post('placeholder', true),
            'is_required' => $this->input->post('is_required') ? 1 : 0,
            'sort_order' => (int)$this->input->post('sort_order') ?: 1,
            'min_length' => $this->input->post('min_length') ?: null,
            'max_length' => $this->input->post('max_length') ?: null,
            'min_value' => $this->input->post('min_value') ?: null,
            'max_value' => $this->input->post('max_value') ?: null,
            'regex_pattern' => $this->input->post('regex_pattern', true) ?: null,
            'allow_other' => $this->input->post('allow_other') ? 1 : 0,
            'is_unique' => $this->input->post('is_unique') ? 1 : 0,
        ];
        $qid = $this->FormQuestion_model->create($payload);
        return $this->_json_ok(['question_id' => $qid]);
    }
}
