<?php
defined('BASEPATH') or exit('No direct script access allowed');


class EventController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('role_id_ap') !== "RU0001") {
            redirect('Login');
        }
        $this->load->model(['Event_model', 'EventForm_model']);
        $this->load->model('UserModel', 'um');
    }

    public function index()
    {
        $data['greeting'] = get_greeting();
        $data['current_time'] = date('H:i');
        $data['current_date'] = format_tanggal_indonesia_greeting();

        $data['user'] = $this->db->get_where('users', ['user_id' => $this->session->userdata('user_id_ap')])->row_array();

        $data['title'] = 'Events';
        // $data['events'] = $this->Event_model->list_all();
        $events = $this->Event_model->list_all();

        foreach ($events as &$event) {
            if (!empty($event->start_at)) {
                $event->start_at = date('d.m.Y', strtotime($event->start_at));
            }
            if (!empty($event->end_at)) {
                $event->end_at = date('d.m.Y', strtotime($event->end_at));
            }
        }
        unset($event);
        $data['events'] = $events;

        // return var_dump($data['events']);
        // die;

        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('admin/events/index', $data);
        $this->load->view('templates/footer');
    }

    public function create()
    {
        $data['title'] = 'Create Event';
        $data['event'] = null; // for _form defaults

        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');

        $this->load->view('admin/events/create', $data);
        $this->load->view('templates/footer');
    }


    public function store()
    {
        $this->_set_event_rules();

        if (!$this->form_validation->run()) {
            // kirim kembali ke form dengan error validation
            return $this->create();
        }

        $payload = $this->_event_payload();
        $user_id = $this->session->userdata('user_id_ap');

        // Pastikan event & default form tercipta bersama-sama
        $this->db->trans_begin();

        $event_id = $this->Event_model->create($payload);

        $this->EventForm_model->create_default($event_id, $user_id);

        $this->db->trans_commit();


        $this->session->set_flashdata('success', 'Event dan form default berhasil dibuat.');
        // redirect ke list event
        redirect('admin/events');
    }



    public function storex()
    {

        $this->_set_event_rules();




        if (!$this->form_validation->run()) {
            return $this->create();
        }


        $payload = $this->_event_payload();
        return var_dump($payload);
        die;
        // Insert data ke tabel event, dan ambil event_id otomatis dari database
        $this->db->insert('event', $payload);
        $event_id = $this->db->insert_id(); // ambil nilai auto_increment terakhir





        $this->EventForm_model->create_default($event_id, $this->session->userdata('user_id'));
    }

    private function _event_payload()
    {
        // $data['user'] = $this->db->get_where('users', ['username' => $this->session->userdata('user_id_ap')])->row_array();
        $organizer = $this->session->userdata('user_id_ap');
        // return var_dump($organizer);
        // die;

        return [
            'organizer_id' => $organizer,
            'event_code' => $this->input->post('event_code', true),
            'title' => $this->input->post('title', true),
            'description' => $this->input->post('description', false),
            'venue' => $this->input->post('venue', true),
            'timezone' => $this->input->post('timezone', true) ?: 'Asia/Jakarta',
            'start_at' => $this->input->post('start_at', true),
            'end_at' => $this->input->post('end_at', true),
            'status' => $this->input->post('status', true) ?: 'draft',
            'max_participants' => $this->input->post('max_participants', true) ?: null,
            // 'is_public' => $this->input->post('is_public') ? 1 : 0,
            'is_public' => $this->input->post('is_public') ? true : false,
        ];
    }

    private function _set_event_rules()
    {
        $this->form_validation->set_rules('title', 'Title', 'required|min_length[3]');
        $this->form_validation->set_rules('event_code', 'Event Code', 'required');
        $this->form_validation->set_rules('start_at', 'Start', 'required');
        $this->form_validation->set_rules('end_at', 'End', 'required');
    }

    public function edit($id)
    {
        // return var_dump("oke");
        // die;
        $data['event'] = $this->Event_model->find($id);
        if (!$data['event']) show_404();
        $data['title'] = 'Edit Event';

        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('admin/events/edit', $data);
        $this->load->view('templates/footer');
    }
}
