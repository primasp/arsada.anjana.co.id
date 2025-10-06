<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PropertyModel extends CI_Model
{

    // Mendapatkan semua pengguna dari database
    public function get_total_properties()
    {
        return $this->db->count_all_results('properties');
    }

    public function get_all_properties()
    {

        $sql = "SELECT 
                p.property_id,
                o.owner_name,
                p.property_type,
                p.property_name,
                p.address,
                p.city,
                p.total_rooms,
                p.property_photo,
                
                -- Menghitung kamar yang tersedia (kamar yang tidak memiliki transaksi sewa aktif)
                COUNT(CASE 
                        WHEN rt.transaction_id IS NULL THEN 1 ELSE NULL 
                END) AS tersedia,
                
                -- Menghitung kamar yang terisi (kamar yang sedang dalam transaksi sewa aktif)
                COUNT(CASE 
                        WHEN rt.transaction_id IS NOT NULL THEN 1 ELSE NULL 
                END) AS terisi,
                
                -- Status properti (Penuh jika semua kamar terisi)
                CASE 
                    WHEN COUNT(CASE 
                                WHEN rt.transaction_id IS NOT NULL THEN 1 ELSE NULL 
                            END) = p.total_rooms 
                    THEN 'Penuh'
                    ELSE 'Tersedia'
                END AS statusfull
                
            FROM 
                properties p
            LEFT JOIN 
                owners o ON p.owner_id = o.owner_id
            LEFT JOIN 
                rooms r ON p.property_id = r.property_id AND r.is_active='1'
            LEFT JOIN 
                (
                    SELECT 
                        room_id, 
                        transaction_id
                    FROM 
                        rental_transactions
                    --WHERE 
                        --CURRENT_DATE BETWEEN start_date AND end_date  -- Cek transaksi sewa aktif
                ) rt ON r.room_id = rt.room_id

            WHERE 
                p.is_active = '1'

            GROUP BY 
                p.property_id, o.owner_name, p.property_type, p.property_name, p.address, p.city, p.total_rooms

            ORDER BY 
                p.property_id ASC";


        $query = $this->db->query($sql);
        return $query->result_array();  // Kembalikan hasil sebagai array
    }


    // Mengambil kamar yang tersedia dari semua properti
    public function get_available_rooms()
    {
        $this->db->select_sum('available_rooms');
        return $this->db->get('properties')->row()->available_rooms;
    }



    // Method untuk memperbarui properti
    public function update_property($id, $data)
    {
        $this->db->where('property_id', $id);
        $this->db->where('is_active', '1');
        return $this->db->update('properties', $data); // Update data berdasarkan ID
    }


    public function delete_property($property_id)
    {
        // Update is_active menjadi 0 untuk menandai bahwa properti ini dihapus
        $this->db->where('property_id', $property_id);
        return $this->db->update('properties', ['is_active' => '0']);
    }


    public function get_all_owners()
    {
        $query = $this->db->get('owners');
        return $query->result_array();  // Mengembalikan data dalam bentuk array
    }


    // Method untuk menyimpan properti baru
    public function insert_property($data)
    {
        // Siapkan query manual untuk memasukkan data ke tabel 'properties' dengan RETURNING
        $sql = "INSERT INTO properties (owner_id, property_type, address, city, total_rooms, available_rooms, property_name)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                RETURNING property_id";  // RETURNING property_id agar kita bisa mendapatkan property_id yang baru saja diinsert

        // Eksekusi query dengan bind parameter untuk mencegah SQL Injection
        $query = $this->db->query($sql, [
            $data['owner_id'],
            $data['property_type'],
            $data['address'],
            $data['city'],
            $data['total_rooms'],
            $data['available_rooms'],
            $data['property_name']
        ]);

        // Kembalikan property_id yang baru saja diinsert
        return $query->row()->property_id;
    }


    // Fungsi untuk mengambil data properti berdasarkan ID
    public function get_property_by_id($property_id)
    {

        $sql = "SELECT 
                    p.property_id,
                    o.owner_name,p.owner_id,
                    p.property_type,
                    p.property_name,
                    p.address,
                    p.city,
                    p.total_rooms,
                     COUNT(CASE WHEN r.status = '01' THEN 1 ELSE NULL END) AS available_rooms
                FROM 
                    properties p
                LEFT JOIN 
                    owners o ON p.owner_id = o.owner_id
                LEFT JOIN 
                    rooms r ON p.property_id = r.property_id
                WHERE p.property_id = ?  and p.is_active='1'
                GROUP BY 
                    p.property_id, o.owner_name, p.property_type, p.property_name, p.address, p.city, p.total_rooms
                ORDER BY 
                    p.property_name";

        // Jalankan query dengan limit yang ditentukan
        $query = $this->db->query($sql, [$property_id]);
        // return $query->result_array();  // Kembalikan hasil sebagai array


        // Jika ditemukan, kembalikan hasilnya, jika tidak, return false
        if ($query->num_rows() > 0) {
            return $query->row_array(); // Kembalikan data sebagai array
        } else {
            return false; // Jika tidak ditemukan
        }
    }


    // Fungsi untuk mengambil daftar kamar berdasarkan property_id
    public function get_rooms_by_property_id($property_id)
    {
        $this->db->where('property_id', $property_id);
        $this->db->where('is_active', '1');
        $this->db->order_by('room_number', 'ASC'); // Menambahkan order by berdasarkan room_number
        $query = $this->db->get('rooms');

        return $query->result_array();
    }
}
