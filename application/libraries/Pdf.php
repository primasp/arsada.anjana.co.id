<?php

use Mpdf\Mpdf;

class Pdf
{
    protected $mpdf;

    public function __construct()
    {
        include_once APPPATH . '../vendor/autoload.php';

        $this->mpdf = new Mpdf([
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'default_font' => 'dejavusans', // ✅ Gunakan font bawaan
        ]);
    }

    public function createPDF($html, $filename = '', $download = false, $path = null)
    {
        $this->mpdf->WriteHTML($html);

        if ($path) {
            $this->mpdf->Output($path, \Mpdf\Output\Destination::FILE);
        } elseif ($download) {
            $this->mpdf->Output($filename . '.pdf', 'D');
        } else {
            $this->mpdf->Output($filename . '.pdf', 'I');
        }
    }
}
