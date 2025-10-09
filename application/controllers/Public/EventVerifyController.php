<?php
defined('BASEPATH') or exit('No direct script access allowed');

class EventVerifyController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model(['Event_model']);
    }

    /**
     * Menampilkan halaman verifikasi berdasarkan QR
     */
    public function verify($registration_id)
    {
        $query = $this->db->query("
            SELECT r.registration_id, r.participant_name, r.participant_email, 
                   r.participant_phone, r.event_id, r.status_kehadiran,
                   e.title AS event_title, e.venue, e.start_at, e.end_at
            FROM event_manager.event_registration r
            JOIN event_manager.event e ON e.event_id = r.event_id
            WHERE r.registration_id = ?
        ", [$registration_id]);

        $data = $query->row();

        if (!$data) {
            show_error('Kode registrasi tidak ditemukanw atau tidak valid.', 404);
            return;
        }

        // Jika sudah hadir sebelumnya
        $already = ($data->status_kehadiran === 'HADIR');

        $viewData = [
            'title'        => 'Verifikasi Kehadiran | Arsada Event',
            'participant'  => $data,
            'already'      => $already,
        ];

        // return var_dump($viewData);
        // die;

        $this->load->view('templates/headerPublic', $viewData);
        $this->load->view('public/event_verify', $viewData);
        $this->load->view('templates/footer');
    }

    public function verify_by_adm()
    {
        $data['title'] = 'Verifikasi Kehadiran | Arsada Event System';
        $data['events'] = $this->db->query("SELECT event_id, title, venue, start_at, end_at 
            FROM event_manager.event 
            WHERE status = 'open' AND is_public = TRUE
            ORDER BY start_at DESC
        ")->result();

        // $event_id = $this->input->post('event_id');

        // Ambil dari POST atau query string
        $event_id = $this->input->post('event_id') ?? $this->input->get('event_id');
        $registration_code = trim($this->input->post('registration_code') ?? '');

        // return var_dump($event_id);
        // die;

        // $registration_code = $this->input->post('registration_code');

        $data['selected_event_id'] = $event_id;
        $data['participant'] = null;
        $data['not_found'] = false;

        // if ($event_id && $registration_code) {
        if (!empty($event_id) && !empty($registration_code)) {

            // return var_dump($registration_code);
            // die;
            // Cari registrasi
            $query = $this->db->query("
                SELECT r.registration_id, r.participant_name, r.participant_email, r.participant_phone,r.event_id, 
                       r.status_kehadiran, e.title AS event_title, e.venue, e.start_at
                FROM event_manager.event_registration r
                JOIN event_manager.event e ON e.event_id = r.event_id
                WHERE r.event_id = ? AND r.registration_id = ?
            ", [$event_id, $registration_code]);

            // $data['participant'] = $query->row();
            $participant = $query->row();

            if ($participant) {
                // ✅ Data ditemukan
                $data['participant'] = $participant;
                $data['selected_event_id'] = $participant->event_id; // event mengikuti data peserta
            } else {
                // ❌ Data tidak ditemukan — tampilkan form manual
                $data['not_found'] = true;
                $data['selected_event_id'] = $event_id; // tetap gunakan event yang dipilih
            }
        }

        $this->load->view('templates/headerPublic', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('public/verify_admin', $data);
        $this->load->view('templates/footer');
    }



    public function confirm_manual_ttd()
    {
        return var_dump("232");
        die;
    }





    /**
     * Konfirmasi kehadiran manual (jika data tidak ditemukan)
     */
    public function confirm_manual()
    {


        $event_id = $this->input->post('event_id');
        $nama     = $this->input->post('participant_name');
        $email    = $this->input->post('participant_email');
        $phone    = $this->input->post('participant_phone');
        $ttdData  = $this->input->post('signature_data'); // base64 dari canvas

        // Simpan tanda tangan digital (jika ada)
        $signaturePath = null;
        if (!empty($ttdData)) {
            $signatureDir = FCPATH . 'uploads/signature/';
            if (!is_dir($signatureDir)) mkdir($signatureDir, 0777, true);
            $fileName = 'sign_' . time() . '.png';
            $filePath = $signatureDir . $fileName;
            $base64   = str_replace('data:image/png;base64,', '', $ttdData);
            file_put_contents($filePath, base64_decode($base64));
            $signaturePath = 'uploads/signature/' . $fileName;
        }

        // Simpan ke tabel registrasi baru
        $sql = "INSERT INTO event_manager.event_registration 
                (event_id, participant_name, participant_email, participant_phone, 
                 status_kehadiran, verified_at, manual_verified, signature_path, created_at)
                VALUES (?, ?, ?, ?, 'HADIR', NOW(), TRUE, ?, NOW())
                RETURNING registration_id";

        $query = $this->db->query($sql, [
            $event_id, $nama, $email, $phone, $signaturePath
        ]);

        $newId = $query->row()->registration_id;

        $this->session->set_flashdata('msg_success', '✅ Kehadiran berhasil diverifikasi secara manual.');
        redirect('verify');
    }








    /**
     * Menandai peserta sebagai hadir
     */
    public function confirmx($registration_id)
    {
        $sql = "UPDATE event_manager.event_registration
                SET status_kehadiran = 'HADIR', verified_at = NOW()
                WHERE registration_id = ? AND (status_kehadiran IS NULL OR status_kehadiran <> 'HADIR')";
        $this->db->query($sql, [$registration_id]);

        $affected = $this->db->affected_rows();

        if ($affected > 0) {
            $this->session->set_flashdata('msg_success', '✅ Kehadiran berhasil diverifikasi.');
        } else {
            $this->session->set_flashdata('msg_error', '⚠️ Peserta ini sudah tercatat hadir sebelumnya.');
        }

        redirect('verify/' . $registration_id);
    }

    public function confirm($registration_id)
    {
        // Ambil data peserta & event sebelum update
        $query = $this->db->query("SELECT r.registration_id, r.participant_name, r.participant_email, r.event_id,
               e.title AS event_title, e.venue
        FROM event_manager.event_registration r
        JOIN event_manager.event e ON e.event_id = r.event_id
        WHERE r.registration_id = ?
    ", [$registration_id]);

        $participant = $query->row();

        // return var_dump($participant->event_id);
        // die;

        if (!$participant) {
            show_error('Data peserta tidak ditemukan.', 404);
            return;
        }

        // Update status kehadiran
        $sql = "UPDATE event_manager.event_registration
            SET status_kehadiran = 'HADIR', verified_at = NOW()
            WHERE registration_id = ? 
              AND (status_kehadiran IS NULL OR status_kehadiran <> 'HADIR')";
        $this->db->query($sql, [$registration_id]);
        $affected = $this->db->affected_rows();

        $data = [
            'title'        => 'Verifikasi Kehadiran Berhasil',
            'participant'  => $participant,
            'verified_at'  => date('d M Y H:i:s'),
            'success'      => $affected > 0,
            'event_id'     => $participant->event_id,
        ];

        // Tampilkan halaman sukses
        $this->load->view('templates/headerPublic', $data);
        $this->load->view('public/verify_success', $data);
        $this->load->view('templates/footer');
    }
}
