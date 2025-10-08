<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ClientController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // $this->load->model('Client_m', 'cm');
    }

    public function index()
    {
        $data['script'] = 'js/client.js';


        $data['greeting'] = get_greeting();
        $data['current_time'] = date('H:i');
        $data['current_date'] = format_tanggal_indonesia_greeting();

        $this->load->view('templates/headerClient');
        $this->load->view('client/property_view', $data);
        $this->load->view('templates/footer');
    }
}
