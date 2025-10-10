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
        $data['script'] = 'js/event.js';
        // $data['script'] = 'js/admin.js';
        // $data['greeting'] = get_greeting();
        // $data['current_time'] = date('H:i');
        // $data['current_date'] = format_tanggal_indonesia_greeting();
        $data['css'] = ['css/event.css'];
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

        $this->load->view('templates/header', $data);
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


    private function _handle_poster_upload(array &$payload, $existing = null)
    {
        if (empty($_FILES['poster']['name'])) {
            return; // tidak ada file diunggah
        }

        $upload_dir = FCPATH . 'uploads/events/';
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0755, true);
        }

        $config = [
            'upload_path'   => $upload_dir,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size'      => 4096,      // KB = 4MB
            'encrypt_name'  => true,
        ];
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('poster')) {
            // simpan error ke flash lalu lanjut (payload tidak diubah)
            $this->session->set_flashdata('error', strip_tags($this->upload->display_errors('', '')));
            return;
        }

        $data = $this->upload->data(); // file_name, file_type, full_path, etc.

        // Hapus file lama jika ganti (saat update)
        if ($existing && !empty($existing->poster_path)) {
            $old = FCPATH . $existing->poster_path;
            if (is_file($old)) {
                @unlink($old);
            }
        }

        // Simpan ke payload untuk DB
        $payload['poster_path'] = 'uploads/events/' . $data['file_name'];
        $payload['poster_mime'] = $data['file_type'];
        // Karena pakai file lokal, kosongkan poster_url (biar view pakai poster_path)
        $payload['poster_url']  = null;
    }



    public function store()
    {
        $this->_set_event_rules();

        if (!$this->form_validation->run()) {
            // kirim kembali ke form dengan error validation
            return $this->create();
        }

        $payload = $this->_event_payload();

        // >>> handle upload poster (jika ada file)
        $this->_handle_poster_upload($payload, null);


        $user_id = $this->session->userdata('user_id_ap');

        // Pastikan event & default form tercipta bersama-sama
        $this->db->trans_begin();

        $event_id = $this->Event_model->create($payload);

        $this->EventForm_model->create_default($event_id, $user_id);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'Gagal menyimpan event.');
            return redirect('admin/events/create');
        }

        $this->db->trans_commit();


        $this->session->set_flashdata('success', 'Event dan form default berhasil dibuat.');

        redirect('admin/events');
    }

    public function delete($id)
    {
        $event = $this->Event_model->find($id);
        if (!$event) show_404();
        $ok = $this->Event_model->delete($id);

        if ($ok) {
            return $this->_json_ok(['deleted' => true]);
        } else {
            return $this->_json_error("Gagal menghapus event.");
        }
    }
    public function update($id)
    {

        $event = $this->Event_model->find($id);

        if (!$event) show_404();

        $this->_set_event_rules();
        if (!$this->form_validation->run()) {
            return $this->edit($id);
        }

        $payload = $this->_event_payload();

        // >>> handle upload poster (jika ada file), auto hapus poster lama
        $this->_handle_poster_upload($payload, $event);


        $this->Event_model->update($id, $payload);
        $this->session->set_flashdata('success', 'Event updated');
        redirect('admin/events');
    }

    private function _json_error($message)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['ok' => false, 'error' => $message]));
    }
    private function _json_ok($data = [])
    {
        $data = array_merge(['ok' => true], $data);
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
    private function _event_payload()
    {
        $organizer = $this->session->userdata('user_id_ap');

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
        $data['event'] = $this->Event_model->find($id);
        if (!$data['event']) show_404();
        $data['title'] = 'Edit Event';

        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('admin/events/edit', $data);
        $this->load->view('templates/footer');
    }
}
