<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ClientController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Client_m', 'cm');
    }

    public function index()
    {
        $data['script'] = 'js/client.js';


        $data['greeting'] = get_greeting();
        $data['current_time'] = date('H:i');
        $data['current_date'] = format_tanggal_indonesia_greeting();

        $this->load->view('templates/headerClient');
        // $this->load->view('templates/sidebar');
        $this->load->view('client/property_view', $data);
        $this->load->view('templates/footer');
    }


    public function auto_renew()
    {
        $this->cm->run_auto_renew();

        // Output CLI atau curl response
        echo "[" . date('Y-m-d H:i:s') . "] Auto-renew selesai.\n";
    }


    public function available_properties()
    {
        $rent_period = $this->input->post('rent_period') ? $this->input->post('rent_period') : $this->input->get('rent_period');
        $quantity = $this->input->post('quantity') ? (int)$this->input->post('quantity') : (int)$this->input->get('quantity');
        $start_date = $this->input->post('start_date') ? $this->input->post('start_date') : $this->input->get('start_date');
        $end_date = $this->input->post('end_date') ? $this->input->post('end_date') : $this->input->get('end_date');

        $start_date = convertDateTime($start_date, 'isoDate');
        $end_date = convertDateTime($end_date, 'isoDate');

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['rent_period'] = $rent_period; // <-- Tambahkan ini
        $data['quantity'] = $quantity;

        // return var_dump($start_date);
        // die;

        $available_properties = $this->cm->get_available_properties($start_date, $end_date);

        if ($available_properties) {
            $data['properties'] = $available_properties;
            $data['script'] = 'js/client.js';
            $this->load->view('client/available_properties_v', $data);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada2 properti yang tersedia untuk tanggal yang dipilih']);
        }
    }


    public function available_rooms()
    {
        $property_id = $this->input->post('property_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $rent_period = $this->input->post('rent_period');
        $quantity = $this->input->post('quantity');
        $property_name = $this->input->post('propertyName');

        // return var_dump($property_name);
        // die;

        if (empty($property_id) || empty($start_date) || empty($end_date)) {
            echo json_encode(['status' => 'error', 'message' => 'Data input tidak lengkap.']);
            return;
        }
        if (!$start_date || !$end_date) {
            echo json_encode(['status' => 'error', 'message' => 'Format tanggal tidak valid.']);
            return;
        }

        $available_rooms = $this->cm->get_available_rooms($property_id, $start_date, $end_date);


        // return var_dump($available_rooms);
        // die;
        if ($available_rooms) {
            $data['rooms'] = $available_rooms;
            $data['rent_period'] = $rent_period;
            $data['quantity'] = $quantity;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['property_name'] = $property_name;

            $this->load->view('client/available_rooms_v', $data); // Load view untuk menampilkan kamar yang tersedia
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada kamar yang tersedia untuk properti ini pada periode yang dipilih.']);
        }
    }

    public function reserve_room()
    {
        $room_Id = $this->input->post('room_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $rent_period = $this->input->post('rent_period');
        $quantity = $this->input->post('quantity');

        $room = $this->cm->get_room_by_id($room_Id);

        if ($room) {
            $totalSewa = $this->cm->get_total_sewa_tr($room_Id, $quantity, $rent_period);

            $data['room'] = $room;
            $data['totalSewa'] = $totalSewa;
            $data['start_date'] = $start_date;

            $data['end_date'] = $end_date;
            $data['rent_period'] = $rent_period;
            $data['quantity'] = $quantity;

            $this->load->view('client/reserve_rooms_v', $data); // Load view untuk menampilkan kamar yang tersedia
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada kamar yang tersedia untuk properti ini pada periode yang dipilih.']);
        }
    }

    public function save_rental_transaction()
    {
        // Cek jika request adalah POST
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid request method.'
            ]);
            return;
        }

        $tenant_data = [
            'full_name' => $this->input->post('full_name'),
            'email' => $this->input->post('email'),
            'phone_number' => $this->input->post('phone_number'),
            'address' => $this->input->post('address'),
            'nik_ktp' => $this->input->post('nik_ktp')
        ];

        $room_id        = $this->input->post('room_id');
        $property_id    = $this->input->post('property_id');
        $quantity       = $this->input->post('quantity');
        $rent_period    = $this->input->post('rent_period');
        $start_date     = $this->input->post('start_date');
        $end_date       = $this->input->post('end_date');

        // Konversi end_date ke format yang benar jika diperlukan
        if (strpos($end_date, '.') !== false) {
            $date = DateTime::createFromFormat('d.m.Y', $end_date);
            if ($date) {
                $end_date = $date->format('Y-m-d'); // Ubah ke format YYYY-MM-DD
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Format tanggal tidak valid untuk end_date.'
                ]);
                return;
            }
        }




        $total_rent     = $this->input->post('total_rent');
        $payment_method = $this->input->post('payment_method');

        $is_bringing_partner = $this->input->post('is_bringing_partner');
        $number_of_people = $this->input->post('number_of_people');

        // Validasi Input
        if (
            empty($tenant_data['full_name']) || empty($tenant_data['email']) ||
            empty($tenant_data['phone_number']) || empty($tenant_data['nik_ktp']) ||
            empty($room_id) || empty($property_id) || empty($quantity) ||
            empty($rent_period) || empty($start_date) || empty($end_date) || empty($total_rent) ||
            empty($payment_method) || empty($number_of_people) ||
            ($is_bringing_partner === 'yes' && empty($_FILES['marriage_proof']['name']))
        ) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Harap isi semua data yang diperlukan.'
            ]);
            return;
        }



        $upload_path  = './assets/img/upload/';

        // Pastikan folder tujuan ada
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true); // Buat folder jika belum ada
        }

        $this->load->library('upload');

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'jpg|jpeg|png|pdf';
        // $config['max_size'] = 2048; // Maksimum ukuran file 2MB
        $config['max_size'] = 5120; // Maksimum ukuran file 5MB
        // $config['file_name'] = $this->input->post('nik_ktp') . '_ktp.' . pathinfo($_FILES['upload_ktp']['name'], PATHINFO_EXTENSION);
        // $config['file_name'] = $this->input->post('nik_ktp') . '_ktp';
        $config['file_name']     = $tenant_data['nik_ktp'] . '_ktp';
        $config['overwrite'] = TRUE;

        $this->upload->initialize($config);




        // if (!$this->upload->do_upload('upload_ktp')) {
        //     $error = $this->upload->display_errors(); // Ambil pesan error
        //     var_dump($error); // Tampilkan pesan error untuk debugging
        //     die; // Hentikan eksekusi untuk debug
        // }
        if (!$this->upload->do_upload('upload_ktp')) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal mengunggah file KTP: ' . $this->upload->display_errors('', '')
            ]);
            return;
        }

        $ktp_file = $this->upload->data('file_name');



        // Upload Bukti Pembayaran
        $config['file_name'] = $tenant_data['nik_ktp'] . '_proof';
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('proof_of_payment')) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal mengunggah bukti pembayaran: ' . $this->upload->display_errors('', '')
            ]);
            return;
        }

        $proof_file = $this->upload->data('file_name');



        // Upload Bukti Pernikahan (jika ada)
        $marriage_proof_file = null;

        if ($is_bringing_partner === 'yes') {
            $config['file_name'] = $tenant_data['nik_ktp'] . '_marriage_proof';
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('marriage_proof')) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Gagal mengunggah bukti pernikahan: ' . $this->upload->display_errors('', '')
                ]);
                return;
            }
            $marriage_proof_file = $this->upload->data('file_name');
        }










        $tenant_id = $this->cm->get_or_create_tenant($tenant_data);


        $transaction_data = [
            'tenant_id'        => $tenant_id,
            'room_id'          => $room_id,
            'property_id'      => $property_id,
            'rent_period'      => $rent_period,
            'start_date'       => $start_date,
            'end_date'         => $end_date,
            'quantity'         => $quantity,
            'payment_method'   => $payment_method,
            'ktp_upload'       => $ktp_file,
            'proof_of_payment' => $proof_file,
            'is_bringing_partner' => $is_bringing_partner,
            'marriage_proof' => $marriage_proof_file,
            'total_sewa'       => floatval(str_replace(['Rp', '.', ','], ['', '', '.'], $total_rent)),
            'number_of_people' => $number_of_people
        ];



        $this->cm->save_transaction($transaction_data);
        $this->cm->update_room_status($room_id, '02');

        // Kirim respon JSON sukses
        echo json_encode([
            'status' => 'success',
            'message' => 'Reservasi berhasil disimpan! Kami akan menghubungi Anda lebih lanjut.'
        ]);
        return;


        // $this->session->set_flashdata('success', 'Reservasi berhasil disimpan!');

        // redirect('ClientController/success');



        // return var_dump($transaction_data);
        // die;
    }

    public function success()
    {
        $this->load->view('client/reservation_success_v');
    }
}
