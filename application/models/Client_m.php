<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Client_m extends CI_Model
{

    public function run_auto_renew()
    {
        // $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        // $tomorrow = '2025-08-23';
        // return var_dump($today);
        // die;

        // Ambil semua transaksi yang masa sewanya habis hari ini & ingin diperpanjang otomatis
        // $expiring = $this->db->get_where('rental_transactions', [
        //     'end_date'    => $tomorrow,
        //     'auto_renew'  => true,
        //     'done_status' => '00',  // Masih aktif
        // ])->result();




        // Ambil trx yang end_date <= besok, auto_renew aktif dan masih ongoing
        $this->db->from('rental_transactions');
        $this->db->where('end_date <=', $tomorrow);   // <= besok (termasuk yang sudah lewat)
        // Jika yang Anda mau hanya besok saja, ganti dengan:
        // $this->db->where('end_date', $tomorrow);
        $this->db->where('auto_renew', true);            // gunakan 1/TRUE sesuai tipe kolom
        $this->db->where('done_status', '00');
        $this->db->order_by('end_date', 'ASC');

        $expiring = $this->db->get()->result();




        if (empty($expiring)) {
            log_message('info', '[AUTO_RENEW] Tidak ada transaksi yang perlu diperpanjang pada ' . $tomorrow);
            return;
        }

        foreach ($expiring as $trx) {
            // Hitung tanggal baru
            //   $new_start_date = date('Y-m-d', strtotime($trx->end_date . ' +1 day'));
            // $new_start_date = date('Y-m-d', strtotime($trx->end_date . ' +1 day'));
            $new_start_date = $trx->end_date;
            $qty = (int)($trx->quantity ?? 1);
            if ($qty < 1) {
                $qty = 1;
            }

            if ($trx->rent_period == 'bulanan') {

                // $new_end_date = date('Y-m-d', strtotime($new_start_date . ' +1 month -1 day'));
                // $new_end_date = date('Y-m-d', strtotime("$new_start_date +{$qty} months -1 day"));
                $new_end_date = date('Y-m-d', strtotime("$new_start_date +{$qty} months"));
                // return var_dump($new_end_date);
                // die;
            } elseif ($trx->rent_period == 'mingguan') {
                // $new_end_date = date('Y-m-d', strtotime($new_start_date . ' +7 days -1 day'));
                // $new_end_date = date('Y-m-d', strtotime("$new_start_date +{$qty} weeks -1 day"));

                // Mingguan: qty x 7 hari - 1 hari
                // $days = ($qty * 7) - 1;
                $days = $qty * 7;
                // $new_end_date = date('Y-m-d', strtotime("$new_start_date +{$days} days"));
                $new_end_date = date('Y-m-d', strtotime("$new_start_date +{$days} days"));
            } else { // harian
                // $new_end_date = $new_start_date;
                $days = $qty;
                // $new_end_date = date('Y-m-d', strtotime("$new_start_date +{$qty} days -1 day"));
                $new_end_date = date('Y-m-d', strtotime("$new_start_date +{$days} days"));
            }

            //  $new_transaction_id = $this->generate_transaction_id();

            $new_transaction = [
                // 'transaction_id'    => $new_transaction_id,
                'tenant_id'         => $trx->tenant_id,
                'property_id'       => $trx->property_id,
                'room_id'           => $trx->room_id,
                'rent_period'       => $trx->rent_period,
                'start_date'        => $new_start_date,
                'end_date'          => $new_end_date,
                'payment_method'    => $trx->payment_method,
                'created_at'        => date('Y-m-d H:i:s'),
                'verif_bayar'       => 'pending',
                // 'quantity'          => 1,
                // 'quantity'          => $trx->quantity,
                'quantity'            => $qty,
                'proof_of_payment'  => null, // Menunggu pembayaran baru
                'ktp_upload'        => $trx->ktp_upload,
                'total_sewa'        => $trx->total_sewa,
                'done_status'       => '00',
                'aktif'             => '1',
                'auto_renew'        => true,
                'marriage_proof'    => $trx->marriage_proof,
                'is_bringing_partner' => $trx->is_bringing_partner,
                'number_of_people'    => $trx->number_of_people
            ];

            // $this->db->insert('rental_transactions', $new_transaction);
            $insert = $this->db->insert('rental_transactions', $new_transaction);


            // Tandai transaksi sebelumnya sebagai selesai
            // $this->db->where('transaction_id', $trx->transaction_id)
            //     ->update('rental_transactions', ['done_status' => '01', 'verif_bayar' => 'approve']);

            if ($insert) {
                log_message('info', '[AUTO_RENEW] Sukses insert transaksi baru untuk tenant_id: ' . $trx->tenant_id);

                // Tandai transaksi lama selesai
                $this->db->where('transaction_id', $trx->transaction_id)
                    ->update('rental_transactions', [
                        'done_status'  => '01',
                        // 'verif_bayar'  => 'approve'
                    ]);

                log_message('info', '[AUTO_RENEW] Transaksi lama ditutup: ' . $trx->transaction_id);
            } else {
                log_message('error', '[AUTO_RENEW] Gagal insert: ' . json_encode($this->db->error()));
            }
        }
    }

    public function get_available_properties_x($start_date, $end_date)
    {
        // echo $start_date;

        $sql = "SELECT DISTINCT properties.property_id, properties.property_name, properties.address, COUNT(rooms.room_id) as available_rooms, properties.property_photo
        FROM properties
        JOIN rooms ON properties.property_id = rooms.property_id
        WHERE rooms.room_id NOT IN (
            SELECT room_id 
            FROM rental_transactions 
            WHERE (? BETWEEN start_date AND end_date)
            OR (? BETWEEN start_date AND end_date)
            OR (start_date BETWEEN ? AND ?)
            OR (end_date BETWEEN ? AND ?) 
        ) AND   properties.is_active='1' AND rooms.is_active='1'
        GROUP BY properties.property_id, properties.property_name, properties.address, properties.property_photo
    ";

        // Gunakan binding parameter untuk variabel tanggal
        $query = $this->db->query($sql, array($start_date, $end_date, $start_date, $end_date, $start_date, $end_date));

        // Jika ada properti yang tersedia, kembalikan datanya
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;  // Jika tidak ada properti yang tersedia
        }
    }






    public function get_available_properties($start_date, $end_date)
    {
        //     $sql = "SELECT
        //         p.property_id,
        //         p.property_name,
        //         p.address,
        //         p.property_photo,
        //         SUM(
        //             CASE WHEN EXISTS (
        //                 SELECT 1
        //                 FROM rental_transactions a
        //                 WHERE a.room_id = r.room_id
        //                   AND a.aktif = '1'
        //                   AND COALESCE(a.payment_status,'PENDING') IN ('DP_PAID','PAID')
        //                   AND (
        //                         (?::date BETWEEN a.start_date AND a.end_date)
        //                      OR (?::date BETWEEN a.start_date AND a.end_date)
        //                      OR (a.start_date BETWEEN ?::date AND ?::date)
        //                      OR (a.end_date   BETWEEN ?::date AND ?::date)
        //                   )
        //             )
        //             THEN 0 ELSE 1
        //             END
        //         ) AS available_rooms
        //     FROM properties p
        //     JOIN rooms r
        //       ON p.property_id = r.property_id
        //      AND r.is_active = '1'
        //     WHERE p.is_active = '1'
        //     GROUP BY p.property_id, p.property_name, p.address, p.property_photo
        //     HAVING SUM(
        //             CASE WHEN EXISTS (
        //                 SELECT 1
        //                 FROM rental_transactions a
        //                 WHERE a.room_id = r.room_id
        //                   AND a.aktif = '1'
        //                   AND COALESCE(a.payment_status,'PENDING') IN ('DP_PAID','PAID')
        //                   AND (
        //                         (?::date BETWEEN a.start_date AND a.end_date)
        //                      OR (?::date BETWEEN a.start_date AND a.end_date)
        //                      OR (a.start_date BETWEEN ?::date AND ?::date)
        //                      OR (a.end_date   BETWEEN ?::date AND ?::date)
        //                      OR auto_renew =true
        //                   )
        //             )
        //             THEN 0 ELSE 1
        //             END
        //         ) > 0
        //     ORDER BY p.property_name ASC
        // ";

        // public function get_available_properties($start_date, $end_date)
        // {
        // Kamar dianggap TIDAK tersedia jika ADA transaksi APPROVED (DP_PAID/PAID) yang:
        //  - auto_renew = TRUE (kunci tanpa lihat tanggal), ATAU
        //  - overlap half-open: [a.start_date, a.end_date) && [start_date, end_date)
        //    -> overlap iff (a.start_date < :req_end) AND (a.end_date > :req_start)

        $sql = "
                    SELECT
                        p.property_id,
                        p.property_name,
                        p.address,
                        p.property_photo,
                        SUM(
                            CASE WHEN EXISTS (
                                SELECT 1
                                FROM rental_transactions a
                                WHERE a.room_id = r.room_id
                                AND a.aktif = '1'
                                AND UPPER(COALESCE(a.payment_status,'PENDING')) IN ('DP_PAID','PAID')
                                AND (
                                        a.auto_renew = TRUE
                                    OR (a.start_date < ?::date AND a.end_date > ?::date)
                                )
                            )
                            THEN 0 ELSE 1
                            END
                        ) AS available_rooms
                    FROM properties p
                    JOIN rooms r
                    ON p.property_id = r.property_id
                    AND r.is_active = '1'
                    WHERE p.is_active = '1'
                    GROUP BY p.property_id, p.property_name, p.address, p.property_photo
                    HAVING SUM(
                            CASE WHEN EXISTS (
                                SELECT 1
                                FROM rental_transactions a
                                WHERE a.room_id = r.room_id
                                AND a.aktif = '1'
                                AND UPPER(COALESCE(a.payment_status,'PENDING')) IN ('DP_PAID','PAID')
                                AND (
                                        a.auto_renew = TRUE
                                    OR (a.start_date < ?::date AND a.end_date > ?::date)
                                )
                            )
                            THEN 0 ELSE 1
                            END
                        ) > 0
                    ORDER BY p.property_name ASC
                ";

        // Kita pakai 12 placeholder (6 untuk SELECT, 6 untuk HAVING), urutannya sama
        // $bind = [
        //     $start_date, $end_date, $start_date, $end_date, $start_date, $end_date,
        //     $start_date, $end_date, $start_date, $end_date, $start_date, $end_date,
        // ];
        $bind = [$end_date, $start_date, $end_date, $start_date];

        $query = $this->db->query($sql, $bind);
        return ($query->num_rows() > 0) ? $query->result() : false;
    }








    public function get_available_rooms_X($property_id, $start_date, $end_date)
    {
        $this->db->select('rooms.*');
        $this->db->from('rooms');
        $this->db->where('rooms.property_id', $property_id);
        $this->db->where('rooms.is_active', '1'); // Pastikan hanya kamar yang aktif

        $this->db->where("rooms.room_id NOT IN ( 
        SELECT room_id 
        FROM rental_transactions 
        WHERE 
            (
                auto_renew = TRUE
                AND aktif = '1'
                AND done_status = '00'
            )
            OR
            (
                start_date <= '$end_date'
                AND end_date >= '$start_date'
                AND aktif = '1'
                AND done_status = '00'
            )
    )");

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result(); // Mengembalikan daftar kamar yang tersedia
        } else {
            return false; // Tidak ada kamar yang tersedia
        }
    }


    public function get_available_rooms($property_id, $start_date, $end_date)
    {
        // Kamar tersedia jika TIDAK ada transaksi APPROVED yang:
        // - auto_renew = TRUE  (blokir tanpa lihat tanggal), ATAU
        // - tanggalnya overlap dengan rentang permintaan
        //
        // APPROVED = payment_status IN ('DP_PAID','PAID')
        //
        // Catatan: transaksi PENDING tidak mengunci kamar.

        //     $sql = "
        //     SELECT r.*
        //     FROM rooms r
        //     WHERE r.property_id = ?
        //       AND r.is_active = '1'
        //       AND NOT EXISTS (
        //             SELECT 1
        //             FROM rental_transactions a
        //             WHERE a.room_id = r.room_id
        //               AND a.aktif = '1'
        //               AND UPPER(COALESCE(a.payment_status,'PENDING')) IN ('DP_PAID','PAID')
        //               AND (
        //                     a.auto_renew = TRUE
        //                     OR (
        //                          a.start_date <= ?::date   -- overlap check
        //                      AND a.end_date   >= ?::date
        //                     )
        //               )
        //       )
        //     ORDER BY r.room_number ASC
        // ";

        // Kamar tersedia bila TIDAK ada transaksi APPROVED (DP_PAID/PAID) yang:
        // - auto_renew = TRUE (mengunci tanpa lihat tanggal), ATAU
        // - overlap half-open: [a.start_date, a.end_date) && [start_date, end_date)
        //    ↳ overlap iff (a.start_date < req_end) AND (a.end_date > req_start)

        $sql = "SELECT r.*
        FROM rooms r
        WHERE r.property_id = ?
          AND r.is_active = '1'
          AND NOT EXISTS (
                SELECT 1
                FROM rental_transactions a
                WHERE a.room_id = r.room_id
                  AND a.aktif = '1'
                  AND UPPER(COALESCE(a.payment_status,'PENDING')) IN ('DP_PAID','PAID')
                  AND (
                        a.auto_renew = TRUE
                        OR (
                             a.start_date < ?::date   -- half-open overlap check
                         AND a.end_date   > ?::date
                        )
                  )
          )
        ORDER BY r.room_number ASC
    ";



        $query = $this->db->query($sql, [$property_id, $end_date, $start_date]);
        return ($query->num_rows() > 0) ? $query->result() : false;
    }


    public function get_room_by_id($room_id)
    {
        $this->db->where('room_id', $room_id);
        return $this->db->get('rooms')->row();
    }

    public function save_transaction($data)
    {

        // return var_dump($data);
        // die;
        $this->db->insert('rental_transactions', $data);
    }

    public function get_total_sewa_tr($room_id, $quantity, $rent_period)
    {
        $this->db->select('daily_price, weekly_price, monthly_price');
        $this->db->from('rooms');
        $this->db->where('room_id', $room_id);
        $room = $this->db->get()->row();

        if (!$room) {
            return 0; // Jika room_id tidak ditemukan, kembalikan 0
        }
        $total_sewa = 0;

        // return var_dump($room->monthly_price);
        // die;
        switch (strtolower($rent_period)) {
            case 'harian':
                $total_sewa = $room->daily_price * $quantity;
                break;
            case 'mingguan':
                $total_sewa = $room->weekly_price * $quantity;
                break;
            case 'bulanan':
                $total_sewa = $room->monthly_price * $quantity;
                break;
            default:
                throw new Exception("Rent period tidak valid: $rent_period");
        }
        return $total_sewa;
    }


    public function update_room_status($room_id, $status)
    {
        $this->db->where('room_id', $room_id);
        $this->db->update('rooms', ['status' => $status]);
    }

    public function get_or_create_tenant($data)
    {
        // Cek apakah penyewa sudah ada berdasarkan nik_ktp
        if (!empty($data['nik_ktp'])) {

            $this->db->where('nik_ktp', $data['nik_ktp']);
            $tenant = $this->db->get('tenants')->row();
            if ($tenant) {
                return $tenant->tenant_id;
            }
        }
        // return var_dump($data);
        // die;

        // Jika penyewa belum ada, buat baru
        $this->db->insert('tenants', $data);




        // Setelah insert, ambil tenant yang baru dibuat berdasarkan nik_ktp
        $this->db->where('full_name', $data['full_name']);
        $new_tenant = $this->db->get('tenants')->row();

        // Kembalikan tenant_id dari penyewa yang baru saja dibuat
        return $new_tenant ? $new_tenant->tenant_id : null;
    }
}
