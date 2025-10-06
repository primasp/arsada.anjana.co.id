<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TransaksiModel extends CI_Model
{

  public function get_all_Transaksi()
  {
    $sql = "
        SELECT 
            a.transaction_id, 
            a.tenant_id,
            d.property_name,
            a.auto_renew, 
            a.room_id, 
            b.room_number, 
            b.daily_price,
            b.weekly_price,
            b.monthly_price,

            a.quantity, 
            a.total_sewa,

            -- Dokumen
            a.ktp_upload, 
            a.proof_of_payment,          -- bukti pelunasan
            a.dp_proof_of_payment,       -- bukti DP

            -- Periode & waktu
            a.rent_period,
            a.start_date,
            a.end_date,
            a.payment_method,
            a.created_at,

            -- Pembayaran
            a.payment_mode,              -- 'FULL' | 'DP'
            a.payment_status,            -- 'PENDING' | 'DP_PAID' | 'PAID' | 'CANCELLED'
            a.dp_amount,
            a.dp_percent,
            a.dp_due_date,
            a.dp_paid_at,
            a.remaining_amount,

            -- Legacy
            a.verif_bayar,

            -- Penyewa
            c.full_name, 
            c.phone_number,
            c.nik_ktp,

            -- ===== Kolom turunan untuk tombol/aksi =====
            -- Approve DP bila: mode DP + status PENDING + dp_amount > 0
            CASE
              WHEN UPPER(COALESCE(a.payment_mode,'FULL')) = 'DP'
               AND UPPER(COALESCE(a.payment_status,'PENDING')) = 'PENDING'
               AND COALESCE(a.dp_amount,0) > 0
              THEN TRUE ELSE FALSE
            END AS can_approve_dp,

            -- Approve Pelunasan bila:
            --   (mode FULL dan belum PAID) ATAU
            --   (mode DP, status DP_PAID, dan masih ada sisa bayar)
            CASE
              WHEN (UPPER(COALESCE(a.payment_mode,'FULL')) <> 'DP'
                    AND UPPER(COALESCE(a.payment_status,'PENDING')) <> 'PAID')
                OR (UPPER(COALESCE(a.payment_mode,'FULL')) = 'DP'
                    AND UPPER(COALESCE(a.payment_status,'PENDING')) = 'DP_PAID'
                    AND COALESCE(
                          a.remaining_amount,
                          GREATEST(COALESCE(a.total_sewa,0) - COALESCE(a.dp_amount,0), 0)
                        ) > 0)
              THEN TRUE ELSE FALSE
            END AS can_approve_full

        FROM rental_transactions a 
        LEFT JOIN rooms b 
               ON a.room_id = b.room_id 
              AND b.status IN ('02','03') 
              AND b.is_active = '1'
        LEFT JOIN tenants c 
               ON a.tenant_id = c.tenant_id 
              AND c.is_active = '1'
        LEFT JOIN properties d 
               ON a.property_id = d.property_id 

        WHERE a.aktif = '1'
          AND COALESCE(a.payment_status, 'PENDING') IN ('PENDING','DP_PAID')

        ORDER BY d.property_name ASC, b.room_number ASC
    ";

    $query = $this->db->query($sql);
    return $query->result();
  }



  public function get_all_Transaksix()
  {

    // Hanya transaksi yg masih butuh tindakan (belum lunas)
    // payment_status: PENDING  -> butuh approve (DP atau full)
    //                  DP_PAID -> DP sudah di-approve, pelunasan menunggu

    $query = $this->db->query("SELECT 
            a.transaction_id, 
            a.tenant_id,
            d.property_name,
            a.auto_renew, 
            a.room_id, 
            b.room_number, 
            b.daily_price,
            b.weekly_price,
            b.monthly_price,
            a.quantity, 
            a.total_sewa,
            a.ktp_upload, 
            a.proof_of_payment,          
            a.dp_proof_of_payment,       
            a.rent_period,
            a.start_date,
            a.end_date,
            a.payment_method,
            a.created_at,
            -- DP / pembayaran
            a.payment_mode,              -- 'FULL' | 'DP'
            a.payment_status,            -- 'PENDING' | 'DP_PAID' | 'PAID' | 'CANCELLED'
            a.dp_amount,
            a.dp_percent,
            a.dp_due_date,
            a.dp_paid_at,
            a.remaining_amount,
            -- Legacy verif (tetap dibawa kalau masih dipakai di tempat lain)
            a.verif_bayar,
            c.full_name, 
            c.phone_number,
            c.nik_ktp 
        FROM rental_transactions a 
        LEFT JOIN rooms b 
            ON a.room_id = b.room_id 
           AND b.status IN ('02','03') 
           AND b.is_active = '1'
        LEFT JOIN tenants c 
            ON a.tenant_id = c.tenant_id 
           AND c.is_active = '1'
        LEFT JOIN properties d 
            ON a.property_id = d.property_id 
        -- WHERE a.verif_bayar = 'pending' and a.aktif='1'
        -- ORDER BY d.property_name ASC, b.room_number ASC
        WHERE a.aktif = '1'
          AND COALESCE(a.payment_status, 'PENDING') IN ('PENDING','DP_PAID')
          -- kalau Anda masih ingin menyaring yg belum di-approve secara legacy:
          -- OR (a.payment_status IS NULL AND COALESCE(a.verif_bayar,'pending') <> 'approve')

        ORDER BY d.property_name ASC, b.room_number ASC
    ");


    return $query->result();
  }
  public function getAllProperies()
  {
    $this->db->where('is_active', '1');
    // $this->db->where('done_status <>', '02'); // tambahkan kondisi done_status <> '02'
    $query = $this->db->get('properties'); // 'owners' is the table name

    return $query->result_array();
  }



  public function filter_transaksi($property = null, $status_sewa = null, $bayar_status = null)
  {

    // Subquery untuk menghitung status_sewa
    $subquery = "
     SELECT 
         rt.transaction_id,
         rt.tenant_id,
         rt.property_id,
         rt.room_id,
         rt.rent_period,
         rt.start_date,
         rt.end_date,
         rt.payment_method,
         rt.verif_bayar,
         rt.quantity,
         rt.proof_of_payment,
         rt.ktp_upload,
         rt.total_sewa,
         rt.done_status,
         rt.aktif,
         rt.auto_renew, 
         CASE
             WHEN rt.done_status = '02' THEN 'Di Stop'
             WHEN rt.start_date > CURRENT_DATE THEN 'Upcoming'
             WHEN rt.end_date = CURRENT_DATE THEN 'Berakhir Hari Ini'
             WHEN CURRENT_DATE BETWEEN rt.start_date AND rt.end_date THEN 
                 CASE
                     WHEN rt.end_date <= CURRENT_DATE + INTERVAL '2 days' THEN 'Ending Soon (<2 Days)'
                     ELSE 'Ongoing'
                 END
             WHEN rt.end_date < CURRENT_DATE THEN 'Lease Ended'
             ELSE 'Unknown Status'
         END AS status_sewa
     FROM 
         stay_manager_v2.rental_transactions rt
     WHERE (rt.aktif = '1' OR rt.done_status = '02')
 ";

    // Main query with joins
    $this->db->select([
      'filtered_transactions.transaction_id',
      'p.property_name',
      'p.property_id',
      'r.room_number',
      't.full_name',
      't.nik_ktp',
      'filtered_transactions.rent_period',
      'filtered_transactions.start_date',
      'filtered_transactions.end_date',
      'filtered_transactions.verif_bayar',
      'filtered_transactions.total_sewa',
      'filtered_transactions.done_status',
      'filtered_transactions.status_sewa',
      'filtered_transactions.quantity',
      'filtered_transactions.auto_renew',
    ]);
    $this->db->from("($subquery) AS filtered_transactions");
    $this->db->join('stay_manager_v2.properties p', 'filtered_transactions.property_id = p.property_id');
    $this->db->join('stay_manager_v2.rooms r', 'filtered_transactions.room_id = r.room_id');
    $this->db->join('stay_manager_v2.tenants t', 'filtered_transactions.tenant_id = t.tenant_id');

    // Apply filters dynamically
    if (!empty($property)) {
      $this->db->where('filtered_transactions.property_id', $property);
    }
    // if (!empty($status_sewa)) {
    //   $this->db->where('filtered_transactions.status_sewa', $status_sewa);
    // }


    if (!empty($status_sewa)) {
      if ($status_sewa === 'Auto Renew') {
        $this->db->where('filtered_transactions.auto_renew', true);
      } elseif ($status_sewa === 'Di Stop') {
        $this->db->where('filtered_transactions.done_status', '02');
      } else {
        $this->db->where('filtered_transactions.status_sewa', $status_sewa);
      }
    } else {
      // kalau status_sewa null → kecualikan done_status '02'
      $this->db->where('filtered_transactions.done_status <>', '02');
    }




    // if (!empty($transaksi_status)) {
    //   $this->db->where('filtered_transactions.done_status', $transaksi_status);
    // }
    if (!empty($bayar_status)) {
      $this->db->where('filtered_transactions.verif_bayar', $bayar_status);
    }





    // Order by end_date ascending
    $this->db->order_by('p.property_name', 'ASC');
    $this->db->order_by('r.room_number', 'ASC');
    $this->db->order_by('filtered_transactions.end_date', 'ASC');

    // Execute query and return results
    $query = $this->db->get();
    return $query->result();
  }
}
