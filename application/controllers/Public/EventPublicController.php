<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


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

        $this->load->view('templates/headerPublic', $data);
        $this->load->view('public/event_list', $data);
        $this->load->view('templates/footer');
    }

    public function daftar($event_code)
    {
        $event = $this->Event_model->find_by_code($event_code);

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

        $this->load->view('templates/headerPublic', $data);
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
        $qNama = $qEmail = $qPhone = null;

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

        $registration_id = $query->row()->registration_id;
        // $registration_id = $query->row()->registration_id; // ✅ langsung dapat dari RETURNING


        // --- Simpan jawaban tiap pertanyaan
        foreach ($questions as $section) {
            foreach ($section as $q) {
                $qid  = $q->question_id;
                $name = 'q_' . $qid;
                $answerValue = null;
                $filePath = null;
                $fileName = null;

                if ($q->question_type === 'file' && !empty($_FILES[$name]['name'])) {
                    $uploadDir = FCPATH . 'uploads/regist_event/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                    $ext = strtolower(pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION));

                    // Insert dulu untuk generate answer_id otomatis
                    $insertFile = $this->db->query("INSERT INTO event_manager.event_registration_answer
                                                        (registration_id, question_id, answer_text, created_at)
                                                        VALUES (?, ?, ?, ?)
                                                        RETURNING answer_id
                                                    ", [
                        $registration_id,
                        $qid,
                        '[uploaded file]',
                        date('Y-m-d H:i:s')
                    ]);

                    $answer_id = $insertFile->row()->answer_id; // ✅ dari DB langsung (via generator)

                    // Nama file & path relatif (tanpa full path)
                    $fileName = 'file_' . $answer_id . '.' . $ext;
                    $relativePath = 'uploads/regist_event/' . $fileName;
                    $fileFullPath = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES[$name]['tmp_name'], $fileFullPath)) {
                        $filePath = $relativePath;
                    }


                    // Update kembali record file
                    $this->db->query("
                    UPDATE event_manager.event_registration_answer
                    SET answer_file_path = ?, answer_file_name = ?
                    WHERE answer_id = ?
                ", [$filePath, $fileName, $answer_id]);
                } else {
                    $answerValue = $this->input->post($name);
                    if (is_array($answerValue)) {
                        $answerValue = json_encode($answerValue);
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
        }

        // === Generate QR Code untuk verifikasi kehadiran ===
        //  $this->load->library('ciqrcode');
        $qrDir = FCPATH . 'uploads/qr_reg/';
        if (!is_dir($qrDir)) mkdir($qrDir, 0777, true);
        $qrFile = 'QR_' . $registration_id . '.png';
        $qrPath = $qrDir . $qrFile;
        // $verifyUrl = site_url('verify/' . $registration_id);
        $verifyUrl =  $registration_id;

        // $this->ciqrcode->generate([
        //     'data' => $verifyUrl,
        //     'level' => 'H',
        //     'size' => 6,
        //     'savename' => $qrPath
        // ]);

        $this->ciqrcode->generate([
            'data'     => $verifyUrl,
            'level'    => 'M',        // medium lebih ringan dan aman
            'size'     => 350,        // cukup besar untuk PDF & scanning HP
            'savename' => $qrPath,
            'label'    => 'ARSADA Event'
        ]);

        // === Generate Bukti Registrasi (PDF) ===
        // $this->load->library('pdf');

        // $pdfPath = FCPATH . 'uploads/bukti_registrasi/' . $registration_id . '.pdf';
        // === Generate PDF Bukti Registrasi ===
        $pdfDir = FCPATH . 'uploads/bukti_registrasi/';

        if (!is_dir($pdfDir)) mkdir($pdfDir, 0777, true);
        $pdfPath = $pdfDir . $registration_id . '.pdf';

        $logoUrl = base_url('assets/img/logo-arsada.jpg');
        $qrUrl = base_url('uploads/qr_reg/' . $qrFile);


        $html = '
    <div style="font-family:Arial,sans-serif; color:#333;">
        <div style="text-align:center; border-bottom:3px solid #0d6efd; padding-bottom:8px;">
            <img src="' . $logoUrl . '" height="60">
            <h2 style="color:#0d6efd; margin:8px 0;">ARSADA EVENT</h2>
        </div>

        <h3 style="text-align:center; margin-top:20px;">BUKTI REGISTRASI EVENT</h3>
        <table style="width:100%; margin-top:20px; font-size:14px;">
            <tr><td><strong>Nama Peserta</strong></td><td>: ' . htmlspecialchars($participant_name) . '</td></tr>
            <tr><td><strong>No. Registrasi</strong></td><td>: ' . htmlspecialchars($registration_id) . '</td></tr>
            <tr><td><strong>Email</strong></td><td>: ' . htmlspecialchars($participant_email) . '</td></tr>
            <tr><td><strong>Event</strong></td><td>: ' . htmlspecialchars($event->title) . '</td></tr>
            <tr><td><strong>Tempat</strong></td><td>: ' . htmlspecialchars($event->venue) . '</td></tr>
            <tr><td><strong>Waktu</strong></td><td>: ' . date('d M Y H:i', strtotime($event->start_at)) . '</td></tr>
            <tr><td><strong>Tanggal Cetak</strong></td><td>: ' . date('d M Y H:i') . '</td></tr>
            <tr><td><strong>Status Kehadiran</strong></td><td>: Belum Hadir</td></tr>
        </table>

        <div style="position:absolute; bottom:40px; right:50px; text-align:center;">
            <img src="' . $qrUrl . '" width="120"><br>
            <small>Scan untuk verifikasi kehadiran</small>
        </div>

        <div style="margin-top:60px; text-align:center; font-size:12px; color:#777;">
            <hr>
            <p>Terima kasih telah mendaftar di <strong>ARSADA Event Management System</strong></p>
        </div>
    </div>
    ';

        // $this->pdf->createPDF($html, $registration_id, false, $pdfPath);
        $this->pdf->createPDF($html, 'Bukti_Registrasi_' . $registration_id, false, $pdfPath);

        // === Kirim email bukti registrasi ===
        if (!empty($participant_email)) {
            $qrUrlPublic = base_url('uploads/qr_reg/' . $qrFile);
            $this->_sendEmail($participant_email, $participant_name, $event, $registration_id, $pdfPath, $qrUrlPublic);
        }




        // Simpan log
        $this->db->query("INSERT INTO event_manager.event_registration_log
        (registration_id, action, ip_address, created_at)
        VALUES (?, ?, ?, ?)
    ", [$registration_id, 'Submitted', $this->input->ip_address(), date('Y-m-d H:i:s')]);


        // === Halaman Sukses ===
        // $data = [
        //     'title' => 'Registrasi Berhasil',
        //     'greeting' => get_greeting(),
        //     'event' => $event,
        //     'participant_name' => $participant_name,
        //     'registration_id' => $registration_id,
        //     'pdf_url' => base_url('uploads/bukti_registrasi/' . $registration_id . '.pdf'),
        //     'qr_url' => $qrUrl
        // ];


        $data = [
            'title' => 'Registrasi Berhasil',
            'event' => $event,
            'greeting' => get_greeting(),
            'current_time' => date('H:i'),
            'current_date' => format_tanggal_indonesia_greeting(),
            'participant_name' => $this->session->flashdata('participant_name'),
            // 'registration_id' => $this->session->flashdata('registration_id'),
            'registration_id' =>  $registration_id,
            'pdf_url' => $this->session->flashdata('pdf_url'),
            // 'qr_url' => $this->session->flashdata('qr_url'),
            'qr_url' => $qrUrl,
        ];



        $this->load->view('templates/headerPublic', $data);
        $this->load->view('public/event_success', $data);
        $this->load->view('templates/footer');


        // --- Redirect dengan pesan sukses
        // $this->session->set_flashdata('msg_success', 'Pendaftaran berhasil dikirim. Terima kasih telah berpartisipasi!');
        // redirect('event/' . $event_code);

        // $this->session->set_flashdata(
        //     'msg_success',
        //     "Registrasi berhasil! Nomor registrasi Anda: <strong>{$registration_id}</strong>. 
        // Bukti telah dikirimkan ke email: <strong>{$participant_email}</strong>."
        // );

        // redirect('event/' . $event_code . '/success');


        // return var_dump($registration_id);
        // die;
    }

    public function detail($event_code)
    {
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



    private function _sendEmail($toEmail, $participant_name, $event, $registration_code, $pdfPath, $qrUrl)
    {
        require_once FCPATH . 'vendor/autoload.php';
        $mail = new PHPMailer(true);

        try {
            // === KONFIGURASI SMTP ===
            $mail->isSMTP();
            $mail->Host       = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'admin@anjana.co.id';   // ubah sesuai email kamu
            $mail->Password   = 'AsiahAmien@23';        // ubah sesuai password kamu
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->setFrom('admin@anjana.co.id', 'ARSADA EVENT SYSTEM');
            $mail->addAddress($toEmail, $participant_name);

            // === ATTACH FILE PDF ===
            if (file_exists($pdfPath)) {
                $mail->addAttachment($pdfPath, 'Bukti_Registrasi_' . $registration_code . '.pdf');
            }

            $mail->isHTML(true);
            $mail->Subject = 'Bukti Registrasi Event: ' . $event->title;

            // === TEMPLATE BODY EMAIL ===
            $mail->Body = '
        <div style="font-family:Arial, Helvetica, sans-serif; color:#333; font-size:14px;">
            <div style="text-align:center; border-bottom:2px solid #0d6efd; padding-bottom:8px; margin-bottom:20px;">
                <img src="' . base_url('assets/img/logo-arsada.jpg') . '" alt="ARSADA" height="60">
                <h2 style="margin:5px 0; color:#0d6efd;">ARSADA EVENT SYSTEM</h2>
            </div>

            <p>Yth. <strong>' . htmlspecialchars($participant_name) . '</strong>,</p>
            <p>Terima kasih telah mendaftar pada acara <strong>' . htmlspecialchars($event->title) . '</strong>.</p>

            <p>Berikut adalah detail pendaftaran Anda:</p>
            <ul>
                <li><strong>No. Registrasi:</strong> ' . htmlspecialchars($registration_code) . '</li>
                <li><strong>Event:</strong> ' . htmlspecialchars($event->title) . '</li>
                <li><strong>Waktu:</strong> ' . date('d M Y H:i', strtotime($event->start_at)) . '</li>
                <li><strong>Lokasi:</strong> ' . htmlspecialchars($event->venue) . '</li>
            </ul>

            <p>Anda dapat mengunduh bukti registrasi pada tombol di bawah ini:</p>
            <p style="text-align:center; margin:25px 0;">
                <a href="' . base_url('uploads/bukti_registrasi/' . $registration_code . '.pdf') . '" 
                   style="background-color:#198754;color:#fff;padding:12px 24px;border-radius:6px;
                          text-decoration:none;font-weight:bold;">
                    📄 Unduh Bukti Registrasi
                </a>
            </p>

            <div style="text-align:center;">
                <img src="' . $qrUrl . '" width="150" height="150" alt="QR Code"><br>
                <small style="color:#666;">Tunjukkan QR Code ini saat registrasi kehadiran.</small>
            </div>

            <p style="margin-top:30px; font-size:12px; color:#888;">
                Salam hormat,<br>
                <strong>ARSADA Event Management Team</strong><br>
                <a href="https://anjana.co.id" style="color:#0d6efd;text-decoration:none;">anjana.co.id</a>
            </p>
        </div>
        ';

            // === Kirim ===
            $mail->send();
            log_message('info', "Bukti registrasi berhasil dikirim ke {$toEmail}");
            return true;
        } catch (Exception $e) {
            log_message('error', "Gagal mengirim email ke {$toEmail}: " . $mail->ErrorInfo);
            return false;
        }
    }
}
