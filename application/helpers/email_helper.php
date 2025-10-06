<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// function sendMailAccPinjaman($email, $id_anggota, $nama_anggota, $body, $sender = 'primacare@anjana.co.id')
function sendMailAccPinjaman($email, $id_pinjaman, $nik_anggota, $nama_anggota, $sender = 'admin@anjana.co.id')
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->SMTPAuth   = true;
        $mail->SMTPDebug  = 0;
        $mail->Debugoutput = 'html';

        if ($sender === 'rsudpasarminggu@jakarta.go.id') {
            $mail->Host       = '10.15.39.87';
            $mail->Port       = 465;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            $mail->Username   = $sender;
            $mail->Password   = '#rsudPM@2012';
        } elseif ($sender === 'admin@anjana.co.id') {
            $mail->Host       = 'smtp.hostinger.com';
            $mail->Port       = 465;
            // $mail->SMTPSecure = 'tls';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Username   = $sender;
            $mail->Password   = 'AsiahAmien@23';
        } else {
            throw new Exception('Email pengirim tidak valid.');
        }

        $mail->isHTML(true);
        $mail->setFrom($mail->Username, 'SIMRS - RSUD Pasar Minggu');
        $mail->addAddress($email);

        $mail->Subject = 'Persetujuan Pinjaman Koperasi';
        // $mail->Body    = $body;
        $mail->Body    = "Pemberitahuan : ACC Pinjaman dengan No.Pinjaman : $id_pinjaman <br>  A/n $nama_anggota ($nik_anggota)<br><br>";

        return $mail->send();
    } catch (Exception $e) {
        log_message('error', 'Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}
