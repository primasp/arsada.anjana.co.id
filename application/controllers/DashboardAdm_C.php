<?php
defined('BASEPATH') or exit('No direct script access allowed');


class DashboardAdm_C extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('role_id_ap') !== "1") {
            redirect('Login');
        }

        $this->load->model('UserModel', 'um');
    }

    public function index()
    {
        $data['script'] = 'js/admin.js';

        $data['greeting'] = get_greeting();
        $data['current_time'] = date('H:i');
        $data['current_date'] = format_tanggal_indonesia_greeting();

        $data['user'] = $this->db->get_where('users', ['user_id' => $this->session->userdata('user_id_ap')])->row_array();

        // return var_dump($data['user']['full_name']);
        // die;

        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('admin/dashboardView', $data);
        $this->load->view('templates/footer');
    }
}
