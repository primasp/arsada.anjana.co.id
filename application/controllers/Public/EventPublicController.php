<?php
defined('BASEPATH') or exit('No direct script access allowed');


class EventPublicController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Event_model');
    }

    public function index()
    {

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
        $this->load->model(['Event_model', 'EventForm_model', 'FormSection_model', 'FormQuestion_model']);
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

    public function detail($event_code)
    {
        $this->load->model(['Event_model', 'EventForm_model', 'FormSection_model', 'FormQuestion_model']);
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
        $this->load->view('public/event_detail', $data);
        // $this->load->view('public/event_form_render', $data);
        $this->load->view('templates/footer');
    }
}
