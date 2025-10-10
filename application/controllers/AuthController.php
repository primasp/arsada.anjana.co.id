<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel', 'um');
        $this->load->library('email');
        $this->load->helper(array('url', 'form'));
        $this->load->library('form_validation');
    }

    public function index()
    {
        // return var_dump("qwq");
        // die;
        $this->form_validation->set_rules('userIdTxt', 'User Name', 'required');
        $this->form_validation->set_rules('passwordTxt', 'Password', 'required');


        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/login_form');
        } else {
            $username = $this->input->post('userIdTxt');
            $password = $this->input->post('passwordTxt');

            $user = $this->um->getUserByUserId($username);




            if ($user && password_verify($password, $user->password)) {
                if ($user->is_active === 't') {
                    $userdata = [
                        'user_id_ap' => $user->user_id,
                        'username_ap' => $user->username,
                        'role_id_ap' => $user->role_id,
                        'logged_in_ap' => TRUE
                    ];




                    $this->session->set_userdata($userdata);



                    switch ($user->role_id) {
                        case "RU0001":
                            redirect('Dashboard-Admin');
                            // redirect('Admin_C');
                            break;
                        case "RU0002":
                            redirect('Dashboard-Karyawan');
                            // redirect('PetugasRegistrasi_C');
                            break;
                        case "RU0003":
                            redirect('Dashboard-User');
                            // redirect('User_C');
                            break;
                        default:
                            redirect('AuthController');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Account not activated. Please check your email.');
                    redirect('AuthController');
                }
            } else {
                $this->session->set_flashdata('error', 'Invalid username or password');
                redirect('AuthController');
            }
        }

        // $this->load->view('welcome_message');
    }

    public function register()
    {

        $this->form_validation->set_rules('fullNameTxt', 'Full Name', 'required');
        $this->form_validation->set_rules('userNameTxt', 'User Name', 'required|is_unique[pc01_gen_user_data.user_id]');
        $this->form_validation->set_rules('emailTxt', 'Email', 'required|valid_email|is_unique[pc01_gen_user_data.email]');

        $this->form_validation->set_rules('password', 'Password', 'required|callback_valid_password');
        $this->form_validation->set_rules('confirmPassword', 'Confirm Password', 'required|matches[password]');


        // return var_dump($this->form_validation->set_rules('password', 'Password', 'required|callback_valid_password'));
        // die;
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/register_form');
        } else {


            $fullName = $this->input->post('fullNameTxt');
            $userName = $this->input->post('userNameTxt');
            $email = $this->input->post('emailTxt');
            $password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
            $activationCode = md5(uniqid(rand(), true));


            $data = [
                'nama' => $fullName,
                'user_id' => $userName,
                'email' => $email,
                'password' => $password
                // 'activation_code' => $activationCode
            ];

            if ($this->um->addUser($data)) {
                $this->session->set_flashdata('message', 'Registration successful! ');

                redirect('AuthController');
            } else {
                $this->session->set_flashdata('error', 'Registration failed. Please try again.');
                redirect('Register');
            }
        }
    }


    public function valid_password($password)
    {
        $password = trim($password);

        // return var_dump($password);
        // die;

        if (strlen($password) < 8) {
            $this->form_validation->set_message('valid_password', 'The {field} must be at least 8 characters in length.');
            return FALSE;
        }

        if (!preg_match('#[0-9]+#', $password)) {
            $this->form_validation->set_message('valid_password', 'The {field} must contain at least one number.');
            return FALSE;
        }

        if (!preg_match('#[a-zA-Z]+#', $password)) {
            $this->form_validation->set_message('valid_password', 'The {field} must contain at least one letter.');
            return FALSE;
        }

        return TRUE;
    }



    public function logout()
    {
        $this->session->unset_userdata(['user_id_ap', 'username', 'role_id', 'logged_in']);
        $this->session->set_flashdata('message', 'Logged out successfully');
        redirect('Login');
    }


    public function forgot_password()
    {


        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/forgot_form');
        } else {
            $email = $this->input->post('email');

            $user = $this->um->get_user_by_email($email);
            // return var_dump($user);
            // die;
            // return var_dump($user);
            // die;
            if ($user) {
                $reset_code = md5(uniqid(rand(), true));

                $this->um->set_reset_code($user->user_id, $reset_code);

                // return var_dump($this->_sendEmail($email, $reset_code, 'forgot'));
                // die;
                if ($this->_sendEmail($email, $reset_code, 'forgot')) {
                    $this->session->set_flashdata('message', 'Password reset link sent to your email.');
                    redirect('Forgot-Password');
                } else {
                    $this->session->set_flashdata('error', 'Password reset failed. Unable to send reset password by email. Please try again.');
                    redirect('Forgot-Password');
                }
                // return var_dump($user->user_id);
                // die;
            } else {
                $this->session->set_flashdata('error', 'Email not found');
                redirect('Forgot-Password');
            }
        }
    }


    public function reset_password($reset_code = null)
    {

        // $this->load->view('auth/reset_passwd_form');
        if (!$reset_code) {
            show_404();
        }
        $user = $this->um->get_user_by_reset_code($reset_code);
        // return var_dump($user);
        // die;
        if ($user) {
            $this->form_validation->set_rules('password', 'Password', 'required');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('auth/reset_passwd_form', ['reset_code' => $reset_code]);
            } else {
                $password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
                $this->um->update_password($user->user_id, $password);
                $this->session->set_flashdata('message', 'Password reset successfully! You can now log in.');
                redirect('Login');
            }
        } else {
        }
    }



    private function _sendEmail($email, $token, $type, $sender = 'admin@anjana.co.id')
    {
        $mail = new PHPMailer(true);

        try {
            // Konfigurasi SMTP
            $mail->isSMTP();
            $mail->SMTPAuth   = true;
            $mail->SMTPDebug  = 2; // Debugging untuk melihat error
            $mail->Debugoutput = 'html'; // Output error dalam HTML

            // Pilih konfigurasi pengirim berdasarkan email pengirim
            if ($sender === 'rsudpasarminggu@jakarta.go.id') {
                $mail->Host       = '10.15.39.87'; // Server SMTP RSUD Pasar Minggu
                $mail->Port       = 465;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Sesuai dengan 'ssl'
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];
                $mail->Username   = 'rsudpasarminggu@jakarta.go.id';
                $mail->Password   = '#rsudPM@2012';
            } else if ($sender === 'admin@anjana.co.id') {
                $mail->Host       = 'smtp.hostinger.com';
                $mail->Port       = 465;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

                $mail->Username   = $sender;
                $mail->Password   = 'AsiahAmien@23';
            } else {
                throw new Exception('Email pengirim tidak valid.');
            }

            $mail->isHTML(true);
            $mail->setFrom($mail->Username, 'SIMRS - RSUD Pasar Minggu'); // Nama pengirim
            $mail->addAddress($email);

            // Judul & Isi Email berdasarkan tipe
            if ($type == 'forgot') {
                $mail->Subject = 'Password Reset';
                $mail->Body    = 'Klik link ini untuk mereset password Anda: <a href="' . base_url() . 'Reset-Password/' . urlencode($token) . '">Reset Password</a>';
            } else {
                $mail->Subject = 'Aktivasi Akun';
                $mail->Body    = 'Klik link ini untuk mengaktifkan akun Anda: <a href="' . base_url() . 'Activate-Account/' . urlencode($token) . '">Aktivasi</a>';
            }

            // Kirim Email
            if ($mail->send()) {
                return true;
            } else {
                log_message('error', 'Gagal mengirim email ke ' . $email . '. Error: ' . $mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            log_message('error', 'Mailer Error: ' . $mail->ErrorInfo);
            return false;
        }
    }



    public function send()
    {
        // Buat instance PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Konfigurasi SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;
            $mail->Username = 'primacare@anjana.co.id';
            $mail->Password = 'Primacare@123';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->isHTML(true);

            // Pengaturan Email
            $mail->setFrom('primacare@anjana.co.id', 'ANJANA BHAKTI NEGERI');
            $mail->addAddress('syahputraprima@gmail.com', 'Recipient Name'); // Email tujuan

            $mail->Subject = 'Test Email dari PHPMailer';
            $mail->Body    = '<h1>Berhasil Mengirim Email!</h1>';
            $mail->isHTML(true);

            // Kirim Email
            if ($mail->send()) {
                echo "Email berhasil dikirim!";
            }
        } catch (Exception $e) {
            echo "Gagal mengirim email: {$mail->ErrorInfo}";
        }
    }
}
