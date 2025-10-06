<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Transaksi_C extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('role_id_ap') !== "1") {
            redirect('Login');
        }

        $this->load->model('UserModel', 'um');
        $this->load->model('Client_m', 'cm');
        $this->load->model('transaksiModel', 'tm');
    }



    public function pendingTransaksi()
    {
        $data['user'] = $this->db->get_where('users', ['username' => $this->session->userdata('user_id_ap')])->row_array();
        $transaksi = $this->tm->get_all_Transaksi();

        // return var_dump($transaksi);
        // die;


        // Format tanggal untuk setiap transaksi
        foreach ($transaksi as $key => $value) {
            $transaksi[$key]->start_date_formatted = format_tanggal_indonesia($value->start_date);
            $transaksi[$key]->end_date_formatted = format_tanggal_indonesia($value->end_date);


            $qty = (int)($value->quantity ?? 1);
            if ($qty < 1) {
                $qty = 1;
            }

            switch (strtolower($value->rent_period)) {
                case 'bulanan':
                    $totalDays = 30 * $qty;
                    break;
                case 'mingguan':
                    $totalDays = 7 * $qty;
                    break;
                default: // harian
                    $totalDays = 1 * $qty;
                    break;
            }

            $transaksi[$key]->total_days = $totalDays;

            // ====== Decor: Pembayaran (DP vs FULL) ======
            $isDP  = (strtoupper($value->payment_mode ?? 'FULL') === 'DP');
            $stat  = strtoupper($value->payment_status ?? 'PENDING');  // PENDING | DP_PAID | PAID | CANCELLED

            $dpAmt = (float)($value->dp_amount ?? 0);
            $remain = (float)($value->remaining_amount ?? max(($value->total_sewa ?? 0) - $dpAmt, 0));
            // return var_dump( $isDP)

            // Label pembayaran
            if ($isDP) {
                $transaksi[$key]->pay_label = 'DP';
                $transaksi[$key]->pay_text  = 'DP Rp ' . number_format($dpAmt, 0, ',', '.') . ' | Sisa Rp ' . number_format($remain, 0, ',', '.');
                $transaksi[$key]->pay_badge = 'badge bg-info';
            } else {
                $transaksi[$key]->pay_label = 'FULL';
                $transaksi[$key]->pay_text  = 'Full Rp ' . number_format(($value->total_sewa ?? 0), 0, ',', '.');
                $transaksi[$key]->pay_badge = 'badge bg-primary';
            }

            // Badge status approval
            if ($stat === 'PAID') {
                $transaksi[$key]->status_text  = 'Lunas';
                $transaksi[$key]->status_badge = 'badge bg-success';
            } elseif ($stat === 'DP_PAID') {
                $transaksi[$key]->status_text  = 'DP Approved (Pelunasan pending)';
                $transaksi[$key]->status_badge = 'badge bg-info';
            } elseif ($stat === 'CANCELLED') {
                $transaksi[$key]->status_text  = 'Dibatalkan';
                $transaksi[$key]->status_badge = 'badge bg-secondary';
            } else { // PENDING
                $transaksi[$key]->status_text  = 'Butuh Approval';
                $transaksi[$key]->status_badge = 'badge bg-warning text-dark';
            }

            // Tombol yang ditampilkan
            $transaksi[$key]->canApproveDP   = ($isDP && $stat === 'PENDING' && $dpAmt > 0);
            $transaksi[$key]->canApproveFull = (!$isDP && $stat !== 'PAID') || ($isDP && $stat === 'DP_PAID' && $remain > 0);

            // Hitung jumlah hari
            // $start = new DateTime($value->start_date);
            // $end = new DateTime($value->end_date);
            // // $interval = $start->diff($end);
            // // $transaksi[$key]->total_days = $interval->days + 1; // Tambahkan 1 untuk menyertakan hari mulai
            // $days = floor(($end->getTimestamp() - $start->getTimestamp()) / (60 * 60 * 24)) + 1;
            // $transaksi[$key]->total_days = $days;
        }

        $data['pendingTransaksi'] = $transaksi;
        $data['script'] = 'js/transaksi.js';

        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('admin/pendingTransaksiView', $data);
        $this->load->view('templates/footer');
    }

    public function allTransaksi()
    {
        $data['user'] = $this->db->get_where('users', ['username' => $this->session->userdata('user_id_ap')])->row_array();
        $data['script'] = 'js/transaksi.js';
        $data['properties'] = $this->tm->getAllProperies();
        // $data['allTransaksi'] = $this->tm->getAllTransaksi();



        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('admin/allTransaksiView', $data);
        $this->load->view('templates/footer');
        $this->load->view('modal/modalEditTransaksi');
    }


    public function filter_transaksi()
    {
        $property = $this->input->post('property');
        $status_sewa = $this->input->post('status_sewa');
        $bayar_status = $this->input->post('bayar_status');

        $transaksi = $this->tm->filter_transaksi($property, $status_sewa, $bayar_status);



        echo json_encode($transaksi);
    }

    public function stop_transaction()
    {
        $id = $this->input->post('transaction_id');
        $room_id = $this->input->post('room_id');

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID kosong']);
            return;
        }

        $this->db->where('transaction_id', $id)->update('rental_transactions', [
            'done_status' => '02',
            'auto_renew'  => false,
        ]);

        // Optional: set room available lagi (sesuaikan kode status Anda)
        if ($room_id) {
            $this->db->where('room_id', $room_id)->update('rooms', ['status' => '01']);
        }

        echo json_encode(['status' => 'success', 'message' => 'Transaksi dihentikan.']);
    }
    public function update_transaction()
    {

        $id          = $this->input->post('transaction_id');
        $rent_period = $this->input->post('rent_period');
        $quantity    = (int) $this->input->post('quantity');
        $start_date  = $this->input->post('start_date');
        $end_date    = $this->input->post('end_date'); // sudah dihitung di front, tapi hitung ULANG di server demi safety
        $auto_renew  = $this->input->post('auto_renew') ? true : false;
        $verif_bayar = $this->input->post('verif_bayar');

        // $data = [
        //     'transaction_id' => $this->input->post('transaction_id'),
        //     'rent_period'    => $this->input->post('rent_period'),
        //     'quantity'       => (int) $this->input->post('quantity'),
        //     'start_date'     => $this->input->post('start_date'),
        //     // end_date dihitung ulang demi safety
        //     'end_date'       => $this->input->post('end_date'),
        //     'auto_renew'     => $this->input->post('auto_renew') ? true : false,
        //     'verif_bayar'    => $this->input->post('verif_bayar')
        // ];


        if (!$id || !$rent_period || !$quantity || !$start_date) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
            return;
        }

        // hitung ulang end_date di server (konsisten dengan aturan checkout)
        $start = new DateTime($start_date);

        if ($rent_period === 'mingguan') {
            $days = $quantity * 7;
            $start->modify("+{$days} days");
            $safe_end = $start->format('Y-m-d');
        } elseif ($rent_period === 'harian') {
            $start->modify("+{$quantity} days");
            $safe_end = $start->format('Y-m-d');
        } else { // bulanan
            $start2 = new DateTime($start_date);
            $start2->modify("+{$quantity} months");
            $safe_end = $start2->format('Y-m-d');
        }

        $data = [
            'rent_period'  => $rent_period,
            'quantity'     => $quantity,
            'start_date'   => $this->input->post('start_date'),
            'end_date'     => $safe_end,
            'auto_renew'   => $auto_renew,
            'verif_bayar'  => $verif_bayar,
            // 'updated_at'   => date('Y-m-d H:i:s'),
        ];

        if ($data['verif_bayar'] == 'pending') {
            $data['done_status'] = '00';
        }

        $this->db->where('transaction_id', $id)->update('rental_transactions', $data);
        echo json_encode(['status' => 'success', 'message' => 'Perubahan disimpan.']);

        // return var_dump($data);
        // die;
    }
    public function addTransaksi()
    {
        $data['user'] = $this->db->get_where('users', ['username' => $this->session->userdata('user_id_ap')])->row_array();
        $data['pendingTransaksi'] = $this->tm->get_all_Transaksi();
        $data['script'] = 'js/transaksi.js';

        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('admin/addTransaksiView', $data);
        $this->load->view('templates/footer');
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
        $auto_renew = $this->input->post('auto_renew') ?? 0; // Default ke 0 jika tidak dicentang

        $payment_method = "Transfer Bank";
        // "Rp 700.000,00"


        $payment_mode = $this->input->post('payment_mode') ?: 'FULL'; // FULL | DP
        $dp_amount_raw = $this->input->post('dp_amount');
        $dp_percent    = $this->input->post('dp_percent');   // 0-100
        $dp_due_date   = $this->input->post('dp_due_date');

        // Normalisasi end_date bila perlu
        if (strpos($end_date, '.') !== false) {
            $date = DateTime::createFromFormat('d.m.Y', $end_date);
            if ($date) $end_date = $date->format('Y-m-d');
            else {
                echo json_encode(['status' => 'error', 'message' => 'Format tanggal tidak valid untuk end_date.']);
                return;
            }
        }


        //  if (
        //     empty($tenant_data['full_name']) || empty($tenant_data['email']) ||
        //     empty($tenant_data['phone_number']) || empty($tenant_data['nik_ktp']) ||
        //     empty($room_id) || empty($property_id) || empty($quantity) ||
        //     empty($rent_period) || empty($start_date) || empty($end_date) || empty($total_rent) ||
        //     empty($payment_method)
        // ){
        if (
            empty($tenant_data['full_name']) || empty($room_id) || empty($property_id) || empty($quantity) ||
            empty($rent_period) || empty($start_date) || empty($end_date) || empty($total_rent) ||
            empty($payment_method)
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
        //     echo json_encode([
        //         'status' => 'error',
        //         'message' => 'Gagal mengunggah file KTP: ' . $this->upload->display_errors('', '')
        //     ]);
        //     return;
        // }

        $ktp_file = $this->upload->data('file_name');



        // Upload Bukti Pembayaran
        $config['file_name'] = $tenant_data['nik_ktp'] . '_proof';
        $this->upload->initialize($config);

        // if (!$this->upload->do_upload('proof_of_payment')) {
        //     echo json_encode([
        //         'status' => 'error',
        //         'message' => 'Gagal mengunggah bukti pembayaran: ' . $this->upload->display_errors('', '')
        //     ]);
        //     return;
        // }

        $proof_file = $this->upload->data('file_name');

        // Bukti DP (baru)
        $config['file_name'] = $tenant_data['nik_ktp'] . '_dp';
        $this->upload->initialize($config);
        // $this->upload->do_upload('dp_proof_of_payment'); // optional
        $dp_proof_file = $this->upload->data('file_name');



        $tenant_id = $this->cm->get_or_create_tenant($tenant_data);

        // return var_dump($tenant_id);
        // die;
        // ==== Normalisasi angka rupiah ====
        $total_rent = floatval(str_replace(',', '.', preg_replace('/\s+|Rp|[^0-9,]/', '', $total_rent)));
        $dp_amount  = floatval(str_replace(',', '.', preg_replace('/\s+|Rp|[^0-9,]/', '', $dp_amount_raw)));

        // $total_rent = preg_replace('/\s+|Rp|[^0-9,]/', '', $total_rent);
        // // Ganti koma (`,`) menjadi titik (`.`) untuk pemisah desimal
        // $total_rent = str_replace(',', '.', $total_rent);
        // // Konversi ke float
        // $total_rent = floatval($total_rent);

        // Debug hasilnya


        // Hitung dp_amount dari persen jika nominal kosong
        if (empty($dp_amount) && !empty($dp_percent)) {
            $dp_amount = round(($dp_percent / 100) * $total_rent, 2);
        }
        if ($payment_mode === 'FULL') {
            $dp_amount = 0;
            $dp_percent = null;
            $dp_due_date = null;
        }


        // Sisa bayar & status
        $remaining = max($total_rent - $dp_amount, 0);
        $payment_status = 'PENDING';
        if ($payment_mode === 'FULL') {
            $payment_status = 'PENDING';           // menunggu verifikasi pelunasan
        } else { // DP
            $payment_status = ($dp_amount > 0) ? 'DP_PAID' : 'PENDING';
        }

        $payment_method = "Transfer Bank";


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
            'total_sewa'       =>  $total_rent,
            'auto_renew'       => $auto_renew, // ✅ Tambahan ini

            // DP fields
            'payment_mode'         => $payment_mode,
            'dp_amount'            => $dp_amount ?: 0,
            'dp_percent'           => $dp_percent ?: null,
            'dp_due_date'          => $dp_due_date ?: null,
            'dp_paid_at'           => ($dp_amount > 0 ? date('Y-m-d H:i:s') : null),
            'dp_proof_of_payment'  => $dp_proof_file ?: null,
            'remaining_amount'     => $remaining,
            'payment_status'       => $payment_status,    // 'PENDING'|'DP_PAID'|'PAID'|'CANCELLED'

        ];





        $this->cm->save_transaction($transaction_data);
        // return var_dump($data);
        // die;



        // $room_id = $this->input->post('room_id');

        $this->cm->update_room_status($room_id, '02');
        // $data['script'] = 'js/client.js';


        // Tampilkan SweetAlert2

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

    public function available_properties()
    {
        $rent_period = $this->input->post('rent_period');
        $quantity = $this->input->post('quantity');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');


        $start_date = convertDateTime($start_date, 'isoDate');
        $end_date = convertDateTime($end_date, 'isoDate');

        // return var_dump($start_date);



        if (empty($rent_period) || empty($quantity) || empty($start_date) || empty($end_date)) {
            echo json_encode(['error' => 'Data tidak lengkap.']);
            return;
        }

        $available_properties = $this->cm->get_available_properties($start_date, $end_date);

        if (empty($available_properties)) {
            echo json_encode([]);
            return;
        }

        echo json_encode($available_properties);
    }

    public function available_rooms()
    {
        $rent_period = $this->input->post('rent_period');
        $quantity = $this->input->post('quantity');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $property_id = $this->input->post('property_Id');


        $start_date = convertDateTime($start_date, 'isoDate');
        $end_date = convertDateTime($end_date, 'isoDate');

        // return var_dump($property_id);
        // die;

        if (empty($rent_period) || empty($quantity) || empty($start_date) || empty($end_date)) {
            echo json_encode(['error' => 'Data tidak lengkap.']);
            return;
        }



        $available_rooms = $this->cm->get_available_rooms($property_id, $start_date, $end_date);

        // return var_dump($available_rooms);
        // die;



        if (empty($available_rooms)) {
            echo json_encode([]);
            return;
        }

        echo json_encode($available_rooms);
    }

    public function getTotalHarga()
    {
        // return var_dump("oke");
        // die;
        $room_id = $this->input->post('room_id');
        $isUpdtHrg = $this->input->post('isUpdtHrg');


        $rent_period = $this->input->post('rent_period');
        $quantity = $this->input->post('quantity');
        $override    = $this->input->post('total_price');
        // return var_dump($isUpdtHrg);
        // die;

        if (empty($room_id) || empty($rent_period) || empty($quantity)) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
            return;
        }

        // Ambil total harga dari model
        try {
            if ($isUpdtHrg === "true") {
                $totalSewa = (float) $override; // atau floatval($override)
            } else {
                $totalSewa = $this->cm->get_total_sewa_tr($room_id, $quantity, $rent_period);
            }



            // return var_dump($totalSewa);
            // die;

            echo json_encode([
                'status' => 'success',
                'price' => $totalSewa,
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }
    public function reject_selected()
    {
        $transactions = $this->input->post('transactions');
        if (empty($transactions)) {
            echo json_encode(['status' => 'error', 'message' => 'No transactions selected.']);
            return;
        }

        foreach ($transactions as $transaction) {
            $transaction_id = $transaction['transaction_id'];
            $room_id = $transaction['room_id'];

            // Update status transaksi jadi "rejected"
            $this->db->where('transaction_id', $transaction_id);
            $this->db->update('rental_transactions', [
                'verif_bayar' => 'rejected',
                'payment_status' => 'CANCELLED',
                'aktif' => '0',
                'updated_at'     => date('Y-m-d H:i:s')
            ]);

            // Jika ingin mengubah status room kembali tersedia:
            $this->db->where('room_id', $room_id);
            $this->db->update('rooms', ['status' => '01']); // 01 = available


        }
        echo json_encode(['status' => 'success', 'message' => 'Transactions rejected successfully.']);
    }


    public function approve_selected()
    {
        $transactions = $this->input->post('transactions');
        // if (empty($transactions)) {
        //     echo json_encode(['status' => 'error', 'message' => 'No transactions selected.']);
        //     return;
        // }

        if (empty($transactions) || !is_array($transactions)) {
            echo json_encode(['status' => 'error', 'message' => 'No transactions selected.']);
            return;
        }

        $errors = [];
        $ok     = [];

        // mulai transaksi DB (atomic)
        $this->db->trans_start();

        foreach ($transactions as $t) {
            $transaction_id = $t['transaction_id'];
            $room_id_req     = $t['room_id'];
            $stage          = $t['stage'] ?? 'full'; // 'dp' | 'full'
            // Update status transaksi di database
            // $this->db->where('transaction_id', $transaction_id);
            // $this->db->update('transactions', ['status' => 'approved']);
            // if (!$transaction_id) continue;

            if (!$transaction_id) {
                $errors[] = ['transaction_id' => $transaction_id, 'message' => 'Missing transaction_id'];
                continue;
            }


            // Ambil transaksi untuk validasi
            $trx = $this->db->get_where('rental_transactions', ['transaction_id' => $transaction_id])->row();
            // if (!$trx) continue;
            if (!$trx) {
                $errors[] = ['transaction_id' => $transaction_id, 'message' => 'Transaksi tidak ditemukan'];
                continue;
            }

            $room_id    = $room_id_req ?: $trx->room_id;
            $start_date = $trx->start_date; // Y-m-d
            $end_date   = $trx->end_date;

            if (!$room_id || !$start_date || !$end_date) {
                $errors[] = ['transaction_id' => $transaction_id, 'message' => 'Data transaksi tidak lengkap'];
                continue;
            }


            $conflict_sql = "SELECT 1
            FROM rental_transactions a
            WHERE a.room_id = ?
              AND a.aktif = '1'
              AND a.transaction_id <> ?
              AND COALESCE(a.done_status,'00') <> '02'           -- abaikan transaksi dihentikan
              AND UPPER(COALESCE(a.payment_status,'PENDING')) IN ('DP_PAID','PAID')
              AND (
                    a.auto_renew = TRUE
                 OR (a.start_date < ?::date AND a.end_date > ?::date)
              )
            LIMIT 1";

            $conflict_q = $this->db->query($conflict_sql, [$room_id, $transaction_id, $end_date, $start_date]);

            if ($conflict_q->num_rows() > 0) {
                $errors[] = [
                    'transaction_id' => $transaction_id,
                    'message' => 'Tidak bisa approve: jadwal bertabrakan dengan transaksi approved lain untuk kamar ini.'
                ];
                continue;
            }


            // ===== Jika aman, lakukan approve =====



            if ($stage === 'dp') {
                // Approve DP: tandai DP_PAID, JANGAN aktifkan kamar menjadi '03' dulu.
                $this->db->where('transaction_id', $transaction_id)->update('rental_transactions', [
                    'payment_status' => 'DP_PAID',
                    'dp_paid_at'     => date('Y-m-d H:i:s'),
                    'verif_bayar'    => 'pending', // tetap pending untuk pelunasan
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);
                // Room tetap status booking ('02') kalau Anda memakainya sebagai reserved.
                if ($room_id) {
                    $this->db->where('room_id', $room_id)->update('rooms', ['status' => '02']);
                }
                $ok[] = $transaction_id;
            } else { // stage === 'full' (pelunasan / full payment)
                $this->db->where('transaction_id', $transaction_id)->update('rental_transactions', [
                    'payment_status'   => 'PAID',
                    'remaining_amount' => 0,
                    'verif_bayar'      => 'approve',
                    'updated_at'       => date('Y-m-d H:i:s'),
                ]);
                // Aktifkan kamar
                if ($room_id) {
                    $this->db->where('room_id', $room_id)->update('rooms', ['status' => '03']);
                    $ok[] = $transaction_id;
                }
            }
        }
        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            echo json_encode(['status' => 'error', 'message' => 'DB error saat menyimpan perubahan.']);
            return;
        }


        if (!empty($errors) && empty($ok)) {
            // semua gagal
            echo json_encode(['status' => 'error', 'message' => $errors[0]['message'], 'errors' => $errors]);
            return;
        }

        if (!empty($errors) && !empty($ok)) {
            // sebagian sukses
            echo json_encode([
                'status'  => 'success',
                'message' => 'Sebagian transaksi berhasil di-approve. Beberapa ditolak karena bentrok.',
                'errors'  => $errors,
                'ok'      => $ok
            ]);
            return;
        }


        echo json_encode(['status' => 'success', 'message' => 'Transactions approved successfully.']);
    }
}
