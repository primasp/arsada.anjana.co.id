<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AdminModel extends CI_Model
{
  public function getAllOwners()
  {
    $this->db->where('is_active', '1');
    $query = $this->db->get('owners'); // 'owners' is the table name

    return $query->result_array();
  }

  public function getPropertiesByOwner($owner_id)
  {
    // $this->db->distinct(); // Memastikan hasil yang diambil adalah distinct (unik)
    $this->db->select(['property_id', 'property_name', 'property_type', 'address']); // Use an array for columns
    $this->db->where('owner_id', $owner_id);
    $this->db->where('is_active', '1');
    $query = $this->db->get('properties');

    return $query->result_array();
  }


  public function getFilteredRooms($owner_id = null, $property_id = null, $room_status = null)
  {
    $this->db->select('a.room_id, b.property_type, b.owner_id, a.property_id, b.property_name, c.owner_name, 
                           a.room_number, a.room_type, a.size, a.facilities, a.status, a.daily_price, 
                           a.weekly_price, a.monthly_price, a.image_path');
    $this->db->from('rooms a');
    $this->db->join('properties b', 'a.property_id = b.property_id', 'left');
    $this->db->join('owners c', 'b.owner_id = c.owner_id', 'left');

    // Tambahkan kondisi untuk owner_id jika ada
    if ($owner_id) {
      $this->db->where('b.owner_id', $owner_id);
    }

    // Tambahkan kondisi untuk property_id jika ada
    if ($property_id) {
      $this->db->where('b.property_id', $property_id);
    }

    // Tambahkan kondisi untuk room_status jika ada
    if ($room_status) {
      $this->db->where('a.status', $room_status);
    }

    // Tambahkan kondisi untuk properti dan kamar yang aktif
    $this->db->where('a.is_active', '1');
    $this->db->where('b.is_active', '1');

    // Urutkan berdasarkan nomor kamar
    $this->db->order_by('a.room_id', 'ASC');

    // Eksekusi query
    $query = $this->db->get();

    // Kembalikan hasil sebagai array
    return $query->result_array();
  }

  public function update_room_status($room_id, $status)
  {
    $this->db->where('room_id', $room_id);
    return $this->db->update('rooms', ['status' => $status]); // 'rooms' adalah nama tabel di database
  }

  public function update_price($room_id, $column, $price)
  {
    $this->db->where('room_id', $room_id);
    return $this->db->update('rooms', [$column => $price]);
  }
}
