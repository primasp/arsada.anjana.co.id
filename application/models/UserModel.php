<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserModel extends CI_Model
{

    // Mendapatkan semua pengguna dari database
    public function getAllUsers()
    {
        $query = $this->db->get('users');
        return $query->result_array();
    }

    public function getUserByUserId($username)
    {
        return $this->db->get_where('users', ['username' => $username])->row();
    }

    public function get_user_by_email($email)
    {
        $this->db->select('user_id, email');
        $this->db->from('users');
        $this->db->where('email', $email);
        $this->db->where('aktif', '1'); // Tanpa kutipan pada angka
        $this->db->where('lokasi_id', '001'); // Jika LOKASI_ID adalah VARCHAR, tetap gunakan kutipan

        return $this->db->get()->row();
    }

    public function set_reset_code($user_id, $reset_code)
    {
        $this->db->where('user_id', $user_id);
        $this->db->update('users', ['reset_code' => $reset_code]);
    }

    public function get_user_by_reset_code($reset_code)
    {
        return $this->db->get_where('users', ['reset_code' => $reset_code])->row();
    }

    public function update_password($user_id, $password)
    {
        $this->db->where('user_id', $user_id);
        $this->db->update('users', ['password' => $password, 'reset_code' => NULL]);
    }
}
