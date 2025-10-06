<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Master_C extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('role_id_ap') !== "1") {
            redirect('Login');
        }

        $this->load->model('UserModel', 'um');
        $this->load->model('AdminModel', 'am');
        $this->load->model('PropertyModel', 'pm');
    }

    public function ms_property()
    {
        $data['user'] = $this->db->get_where('users', ['user_id' => $this->session->userdata('user_id_ap')])->row_array();

        // return var_dump($this->session->userdata);
        // die;

        $data['script'] = 'js/admin.js';
        $data['properties'] = $this->pm->get_all_properties();

        $data['owners'] = $this->pm->get_all_owners();


        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('master/propertyView', $data);
        $this->load->view('templates/footer');
    }

    public function add_property()
    {
        $data['user'] = $this->db->get_where('users', ['user_id' => $this->session->userdata('user_id_ap')])->row_array();

        $data['script'] = 'js/admin.js';
        $data['properties'] = $this->pm->get_all_properties();

        $data['owners'] = $this->pm->get_all_owners();


        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('master/propertyAdd', $data);
        $this->load->view('templates/footer');
    }
    public function ms_room()
    {
        $data['script'] = 'js/admin.js';
        $data['owners'] = $this->am->getAllOwners();

        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('master/roomView', $data);
        $this->load->view('templates/footer');
    }

    public function getPropertiesByOwner($owner_id)
    {
        $properties = $this->am->getPropertiesByOwner($owner_id);

        // return var_dump($properties);
        // die;
        echo json_encode($properties);
    }


    public function filter_ms_room()
    {
        $owner_id = $this->input->post('owner_id');
        $property_id = $this->input->post('property_id');
        $room_status = $this->input->post('room_status');

        $rooms = $this->am->getFilteredRooms($owner_id, $property_id, $room_status);

        echo json_encode($rooms);
    }

    public function add_room()
    {
        $data['script'] = 'js/admin.js';

        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('master/roomAdd.', $data);
        $this->load->view('templates/footer');
    }

    public function update_room_status()
    {
        $room_id = $this->input->post('room_id');
        $status = $this->input->post('status');

        // Validasi input
        if (empty($room_id) || empty($status)) {
            echo json_encode(['status' => 'error', 'message' => 'Room ID and Status are required.']);
            return;
        }

        // Update status kamar di database
        $update = $this->am->update_room_status($room_id, $status);


        if ($update) {
            echo json_encode(['status' => 'success', 'message' => 'Room status updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update room status.']);
        }
    }

    public function update_room_price()
    {
        $room_id = $this->input->post('room_id');
        $price_type = $this->input->post('price_type');
        $price = $this->input->post('price');

        // Validasi input
        if (empty($room_id) || empty($price_type) || empty($price)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            return;
        }

        // Tentukan kolom harga berdasarkan tipe
        $column = '';
        switch ($price_type) {
            case 'daily':
                $column = 'daily_price';
                break;
            case 'weekly':
                $column = 'weekly_price';
                break;
            case 'monthly':
                $column = 'monthly_price';
                break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'Invalid price type']);
                return;
        }

        // Update harga di database
        // $this->load->model('RoomModel');
        $update = $this->am->update_price($room_id, $column, $price);

        if ($update) {
            echo json_encode(['status' => 'success', 'message' => 'Price updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update price']);
        }
    }
}
