<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Logo\Logo;

class Ciqrcode
{
    public function generate($params = [])
    {
        $data       = $params['data'] ?? 'EMPTY';
        $size       = $params['size'] ?? 300;
        $levelCode  = strtoupper($params['level'] ?? 'M'); // default Medium
        $savename   = $params['savename'] ?? (FCPATH . 'uploads/qr_reg/default.png');
        $labelText  = $params['label'] ?? '';
        $logoPath   = $params['logo'] ?? null; // opsional: path logo kecil di tengah QR

        // Pastikan folder tujuan ada
        $dir = dirname($savename);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // Tentukan level koreksi error berdasarkan input
        switch ($levelCode) {
            case 'H':
                $level = new ErrorCorrectionLevelHigh();
                break;
            case 'L':
                $level = new ErrorCorrectionLevelLow();
                break;
            default:
                $level = new ErrorCorrectionLevelMedium();
        }

        // Buat QR code
        $qr = QrCode::create($data)
            ->setSize($size)
            ->setMargin(10)
            ->setErrorCorrectionLevel($level);

        // Label opsional
        $label = null;
        if (!empty($labelText)) {
            try {
                $label = Label::create($labelText)->setFont(new NotoSans(12));
            } catch (Exception $e) {
                $label = null; // Jika font tidak ditemukan, tetap lanjut
            }
        }

        // Logo opsional (jika disertakan)
        $logo = null;
        if ($logoPath && file_exists($logoPath)) {
            try {
                $logo = Logo::create($logoPath)->setResizeToWidth(50);
            } catch (Exception $e) {
                $logo = null;
            }
        }

        $writer = new PngWriter();

        try {
            // Tulis QR
            $result = $writer->write($qr, $logo, $label);
        } catch (Exception $e) {
            // Jika gagal karena data terlalu panjang, retry otomatis
            $qr = QrCode::create($data)
                ->setSize($size + 100) // perbesar otomatis
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelMedium());
            $result = $writer->write($qr);
        }

        // Simpan ke file
        $result->saveToFile($savename);

        return $savename;
    }
}
