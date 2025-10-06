<?php
if (!function_exists('formatDateToIndo')) {
    function formatDateToIndo($date)
    {
        // Konversi tanggal ke format dd.mm.yyyy
        $d = new DateTime($date);
        $day = $d->format('d');
        $month = $d->format('m');
        $year = $d->format('Y');
        return $day . '.' . $month . '.' . $year;
    }
    function get_greeting()
    {
        date_default_timezone_set('Asia/Jakarta');
        $hour = date('H');

        if ($hour >= 5 && $hour < 11) {
            return "Selamat Pagi";
        } elseif ($hour >= 11 && $hour < 15) {
            return "Selamat Siang";
        } elseif ($hour >= 15 && $hour < 18) {
            return "Selamat Sore";
        } else {
            return "Selamat Malam";
        }
    }



    function format_tanggal_indonesia_greeting($date = null)
    {
        date_default_timezone_set('Asia/Jakarta');
        $hari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];

        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $date = $date ?? date('Y-m-d');
        $timestamp = strtotime($date);
        $day = date('l', $timestamp);
        $day_in_id = $hari[$day];
        $tgl = date('d', $timestamp);
        $bln = $bulan[(int)date('m', $timestamp)];
        $thn = date('Y', $timestamp);

        return "$day_in_id, $tgl $bln $thn";
    }

    function getBulanMap()
    {
        return [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
    }




    function convertDateTime($input, $to, $from = '')
    {
        $bulanMap = getBulanMap();

        $formatMap = [
            'userDate' => [
                'format' => 'd.m.Y',
                'regex' => '/^\d{2}\.\d{2}\.\d{4}$/'
            ],
            'userDateTime' => [
                'format' => 'd.m.Y H:i:s',
                'regex' => '/^\d{2}\.\d{2}\.\d{4} \d{2}\:\d{2}\:\d{2}$/'
            ],
            'isoDate' => [
                'format' => 'Y-m-d',
                'regex' => '/^\d{4}\-\d{2}\-\d{2}$/'
            ],
            'isoDateTime' => [
                'format' => 'Y-m-d H:i:s',
                'regex' => '/^\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2}$/'
            ],
            'clock' => [
                'format' => 'H:i:s',
                'regex' => '/^\d{2}\:\d{2}:\d{2}$/'
            ],
        ];

        $fromFormat = '';

        // Deteksi format asal jika tidak diberikan
        if (empty($from)) {
            $hasFromFormat = false;
            foreach ($formatMap as $key => $row) {
                if (preg_match($row['regex'], $input)) {
                    $fromFormat = $key;
                    $hasFromFormat = true;
                }
            }

            if (!$hasFromFormat) {
                return '[invalid source format: ' . $input . ']';
            }
        } else {
            if (!array_key_exists($from, $formatMap)) {
                return '[invalid from format: ' . $from . ']';
            }
        }

        // Buat objek tanggal
        $originalDate = DateTime::createFromFormat($formatMap[$fromFormat]['format'], $input);

        if (!$originalDate) {
            return '[invalid date: ' . $input . ']';
        }

        // Tambahkan fallback untuk menghindari undefined index
        $formatMap['userDateTextMonth'] = [
            'function' => function () use ($bulanMap, $originalDate) {
                $month = $originalDate->format('m');
                $monthName = isset($bulanMap[$month]) ? $bulanMap[$month] : 'Invalid Month';
                return $originalDate->format('d') . ' ' . $monthName . ' ' . $originalDate->format('Y');
            }
        ];

        if (!array_key_exists($to, $formatMap)) {
            return '[invalid conversion to format: ' . $to . ']';
        }

        if (isset($formatMap[$to]['function'])) {
            return $formatMap[$to]['function']();
        } else {
            return $originalDate->format($formatMap[$to]['format']);
        }
    }

    function format_tanggal_indonesia($tanggal)
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $tanggal_split = explode('-', $tanggal);
        if (count($tanggal_split) !== 3) {
            return $tanggal; // Jika format tidak sesuai, kembalikan nilai asli
        }

        $tahun = $tanggal_split[0];
        $bulan_index = (int)$tanggal_split[1];
        $hari = $tanggal_split[2];

        return $hari . ' ' . $bulan[$bulan_index] . ' ' . $tahun;
    }
}
