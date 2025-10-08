<?php
defined('BASEPATH') or exit('No direct script access allowed');


class EventPublicController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Event_model', 'EventForm_model', 'FormSection_model', 'FormQuestion_model']);
    }

    public function index()
    {
        $data['css'] = ['css/event.css'];
        $data['script'] = 'js/public.js';
        $data['greeting'] = get_greeting();
        $data['current_time'] = date('H:i');
        $data['current_date'] = format_tanggal_indonesia_greeting();


        $data['title'] = 'Daftar Event - Arsada Anjana';
        $data['events'] = $this->Event_model->list_public();
        // return var_dump($data['event']);
        // die;

        $this->load->view('templates/headerPublic', $data);
        // $this->load->view('templates/public_header', $data);
        $this->load->view('public/event_list', $data);
        $this->load->view('templates/footer');
        // $this->load->view('templates/public_footer');
    }
    public function daftar($event_code)
    {
        // $this->load->model(['Event_model', 'EventForm_model', 'FormSection_model', 'FormQuestion_model']);
        $event = $this->Event_model->find_by_code($event_code);

        // return var_dump($event);
        // die;
        if (!$event || !$event->is_public || $event->status !== 'open') {
            show_404();
        }

        $form = $this->EventForm_model->get_default_by_event($event->event_id);
        $sections = $this->FormSection_model->list_by_form($form->form_id);
        $questions = $this->FormQuestion_model->list_by_form_grouped($form->form_id);


        $data = [
            'script' => 'js/public.js',
            'greeting' => get_greeting(),
            'current_time' => date('H:i'),
            'current_date' => format_tanggal_indonesia_greeting(),
            'title' => $event->title . ' | Arsada Event',
            'event' => $event,
            'form' => $form,
            'sections' => $sections,
            'questions' => $questions,
        ];



        // $data['script'] = 'js/public.js';
        // $data['greeting'] = get_greeting();
        // $data['current_time'] = date('H:i');
        // $data['current_date'] = format_tanggal_indonesia_greeting();
        // $data['title'] = $event->title . ' - Arsada Event';
        // $data['event'] = $event;

        // $this->load->view('templates/public_header', $data);
        $this->load->view('templates/headerPublic', $data);
        // $this->load->view('public/event_detail', $data);
        $this->load->view('public/event_form_render', $data);
        $this->load->view('templates/footer');
    }

    public function submit($event_code)
    {
        $event = $this->Event_model->find_by_code($event_code);

        if (!$event || !$event->is_public || $event->status !== 'open') {
            show_404();
        }

        // --- Ambil semua pertanyaan
        $form = $this->EventForm_model->get_default_by_event($event->event_id);
        $questions = $this->FormQuestion_model->list_by_form_grouped($form->form_id);

        $systemQuestions = $this->db->query("SELECT question_id, label 
        FROM event_manager.form_question 
        WHERE form_id = ? AND is_system = TRUE
        ORDER BY sort_order
    ", [$form->form_id])->result();

        // Mapping untuk memudahkan lookup
        $qNama   = null;
        $qEmail  = null;
        $qPhone  = null;

        foreach ($systemQuestions as $q) {
            $label = strtolower(trim($q->label));
            if (strpos($label, 'nama') !== false) {
                $qNama = $q->question_id;
            } elseif (strpos($label, 'email') !== false) {
                $qEmail = $q->question_id;
            } elseif (strpos($label, 'telepon') !== false || strpos($label, 'hp') !== false) {
                $qPhone = $q->question_id;
            }
        }

        // --- Ambil value peserta dari input berdasarkan q_question_id
        $participant_name  = $this->input->post('q_' . $qNama) ?? 'Peserta Tidak Dikenal';
        $participant_email = $this->input->post('q_' . $qEmail) ?? null;
        $participant_phone = $this->input->post('q_' . $qPhone) ?? null;


        // $participant_name  = $this->input->post('q_nama_peserta') ?? 'Peserta Tidak Dikenal';

        // --- Simpan data registrasi utama
        // $regData = [
        //     'event_id'          => $event->event_id,
        //     'participant_name'  => $participant_name,
        //     'participant_email' => $participant_email,
        //     'participant_phone' => $participant_phone,
        //     'ip_address'        => $this->input->ip_address(),
        //     'user_agent'        => $this->input->user_agent(),
        //     'created_at'        => date('Y-m-d H:i:s')
        // ];

        // $this->db->insert('event_manager.event_registration', $regData);
        // $registration_id = $this->db->query("SELECT currval(pg_get_serial_sequence('event_manager.event_registration','id')) AS id")->row()->id ?? null;


        $sql = "INSERT INTO event_manager.event_registration 
        (event_id, participant_name, participant_email, participant_phone, ip_address, user_agent, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        RETURNING registration_id
    ";
        $query = $this->db->query($sql, [
            $event->event_id,
            $participant_name,
            $participant_email,
            $participant_phone,
            $this->input->ip_address(),
            $this->input->user_agent(),
            date('Y-m-d H:i:s')
        ]);


        $registration_id = $query->row()->registration_id; // ✅ langsung dapat dari RETURNING


        // --- Simpan jawaban tiap pertanyaan
        foreach ($questions as $section) {
            foreach ($section as $q) {
                $qid  = $q->question_id;
                $name = 'q_' . $qid;
                $answerValue = null;
                $filePath = null;
                $fileName = null;

                if ($q->question_type === 'file' && !empty($_FILES[$name]['name'])) {
                    $uploadDir = FCPATH . 'uploads/event/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $fileName = time() . '_' . preg_replace('/\s+/', '_', $_FILES[$name]['name']);
                    $filePath = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES[$name]['tmp_name'], $filePath)) {
                        $answerValue = '[uploaded file]';
                    }
                } else {
                    $answerValue = $this->input->post($name);
                    if (is_array($answerValue)) {
                        $answerValue = json_encode($answerValue);
                    }
                }

                $this->db->query("INSERT INTO event_manager.event_registration_answer
                (registration_id, question_id, answer_text, answer_file_path, answer_file_name, created_at)
                VALUES (?, ?, ?, ?, ?, ?)
            ", [
                    $registration_id,
                    $qid,
                    $answerValue,
                    $filePath,
                    $fileName,
                    date('Y-m-d H:i:s')
                ]);
            }
        }


        // --- Simpan log pendaftaran
        $this->db->query("INSERT INTO event_manager.event_registration_log
        (registration_id, action, ip_address, created_at)
        VALUES (?, ?, ?, ?)
    ", [
            $registration_id,
            'Submitted',
            $this->input->ip_address(),
            date('Y-m-d H:i:s')
        ]);


        // --- Redirect dengan pesan sukses
        $this->session->set_flashdata('msg_success', 'Pendaftaran berhasil dikirim. Terima kasih telah berpartisipasi!');
        redirect('event/' . $event_code);


        // return var_dump($registration_id);
        // die;
    }

    public function detail($event_code)
    {
        // $this->load->model(['Event_model', 'EventForm_model', 'FormSection_model', 'FormQuestion_model']);
        $event = $this->Event_model->find_by_code($event_code);


        // return var_dump($event);
        // die;
        if (!$event || !$event->is_public || $event->status !== 'open') {
            show_404();
        }

        $form = $this->EventForm_model->get_default_by_event($event->event_id);
        $sections = $this->FormSection_model->list_by_form($form->form_id);
        $questions = $this->FormQuestion_model->list_by_form_grouped($form->form_id);


        $data = [
            'script' => 'js/public.js',
            'css' => 'css/event.css',
            'greeting' => get_greeting(),
            'current_time' => date('H:i'),
            'current_date' => format_tanggal_indonesia_greeting(),
            'title' => $event->title . ' | Arsada Event',
            'event' => $event,
            'form' => $form,
            'sections' => $sections,
            'questions' => $questions,
        ];



        // $data['script'] = 'js/public.js';
        // $data['greeting'] = get_greeting();
        // $data['current_time'] = date('H:i');
        // $data['current_date'] = format_tanggal_indonesia_greeting();
        // $data['title'] = $event->title . ' - Arsada Event';
        // $data['event'] = $event;

        // $this->load->view('templates/public_header', $data);
        $this->load->view('templates/headerPublic', $data);
        $this->load->view('public/event_detail', $data);
        // $this->load->view('public/event_form_render', $data);
        $this->load->view('templates/footer');
    }
}
