<?php defined('BASEPATH') or exit('No direct script access allowed');


class EventForm_model extends CI_Model
{
    protected $table = 'event_form';


    public function get_default_by_eventxxxx($event_id)
    {
        return $this->db
            ->where(['event_id' => $event_id, 'is_default' => '1', 'aktif' => '1'])
            ->get($this->table)->row();
    }

    public function get_default_by_eventx132($event_id)
    {
        $form = $this->db->get_where($this->table, ['event_id' => $event_id, 'is_default' => '1'])->row();
        // $form_id = $this->create_default($event_id, 'USR000000001');
        if (!$form) {
            $form_id = $this->create_default($event_id, 'USR000000001');
            return $this->db->get_where($this->table, ['form_id' => $form_id])->row();
        }

        return $form;
    }

    public function get_default_by_event($event_id)
    {
        // Cari form default berdasarkan event
        $form = $this->db
            ->get_where($this->table, [
                'event_id'   => $event_id,
                'is_default' => '1',  // gunakan boolean, bukan string
                'aktif' => '1'
            ])
            ->row();

        // Jika belum ada form default, buat otomatis
        if (!$form) {
            // Coba ambil user ID dari session, fallback ke default user jika belum login
            $user_id = $this->session->userdata('user_id_ap') ?? 'USR000000001';

            $form_id = $this->create_default($event_id, $user_id);

            // Ambil ulang form-nya
            return $this->db
                ->get_where($this->table, ['form_id' => $form_id])
                ->row();
        }

        return $form;
    }




    public function create_defaultxx($event_id, $user_id)
    {


        $exists = $this->db->get_where($this->table, [
            'event_id' => $event_id,
            'is_default' => '1'
        ])->num_rows();


        if ($exists > 0) {
            return; // sudah ada form default, tidak perlu tambah
        }







        $data = [
            'event_id' => $event_id,
            'name' => 'Form Default',
            'description' => 'Form bawaan otomatis untuk event ini',
            'status' => 'draft',
            'version' => 1,
            'is_default' => '1',
            'created_by' => $user_id
        ];
        $this->db->insert($this->table, $data);
        // return $this->db->insert_id();
    }


    public function create_defaultxxxxx($event_id, $user_id)
    {
        $data = [
            'event_id'   => $event_id,
            'name'       => 'Form Pendaftaran Peserta',
            'description' => 'Form otomatis untuk pendaftaran event',
            'is_default' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $user_id
        ];
        $this->db->insert($this->table, $data);
        $form_id = $this->db->insert_id();

        // 🔥 Buat section default
        $this->load->model('FormSection_model');
        $section_id = $this->FormSection_model->create([
            'form_id' => $form_id,
            'title' => 'Data Peserta',
            'description' => 'Informasi identitas peserta event',
            'sort_order' => 1,
        ]);

        // 🔥 Tambahkan 3 pertanyaan default
        $this->load->model('FormQuestion_model');
        $questions = [
            ['label' => 'Nama Peserta', 'question_type' => 'short_text', 'placeholder' => 'Masukkan nama lengkap', 'is_required' => true],
            ['label' => 'Email', 'question_type' => 'email', 'placeholder' => 'Masukkan email aktif', 'is_required' => true],
            ['label' => 'No. Telepon', 'question_type' => 'short_text', 'placeholder' => 'Masukkan nomor HP', 'is_required' => false],
        ];
        $sort = 1;
        foreach ($questions as $q) {
            $this->FormQuestion_model->create([
                'form_id'      => $form_id,
                'section_id'   => $section_id,
                'label'        => $q['label'],
                'question_type' => $q['question_type'],
                'placeholder'  => $q['placeholder'],
                'is_required'  => $q['is_required'],
                'sort_order'   => $sort++,
                'is_system'    => true  // flag khusus agar tidak bisa dihapus
            ]);
        }

        return $form_id;
    }

    public function create_default($event_id, $user_id)
    {
        // 🔹 Insert form utama dan ambil form_id langsung dari RETURNING
        $sql = "INSERT INTO {$this->table} 
            (event_id, name, description, is_default, created_at, created_by)
            VALUES (?, ?, ?, ?, ?, ?)
            RETURNING form_id";
        $query = $this->db->query($sql, [
            $event_id,
            'Form Pendaftaran Peserta',
            'Form otomatis untuk pendaftaran event',
            '1',
            date('Y-m-d H:i:s'),
            $user_id
        ]);

        $form_id = $query->row()->form_id; // ambil hasil RETURNING

        // 🔥 Buat section default
        // $this->load->model('FormSection_model');
        // $section_id = $this->FormSection_model->create([
        //     'form_id'     => $form_id,
        //     'title'       => 'Data Peserta',
        //     'description' => 'Informasi identitas peserta event',
        //     'sort_order'  => 1,
        //     'created_at'  => date('Y-m-d H:i:s'),
        //     'created_by'  => $user_id
        // ]);




        // 🔥 Buat section default langsung via RETURNING section_id
        $sql_section = "INSERT INTO event_manager.form_section
        (form_id, title, description, sort_order, created_at, created_by)
        VALUES (?, ?, ?, ?, ?, ?)
        RETURNING section_id";
        $query_section = $this->db->query($sql_section, [
            $form_id,
            'Data Peserta',
            'Informasi identitas peserta event',
            1,
            date('Y-m-d H:i:s'),
            $user_id
        ]);

        $section_id = $query_section->row()->section_id; // ✅ Dijamin terisi








        // 🔥 Tambahkan 3 pertanyaan default
        $this->load->model('FormQuestion_model');
        $questions = [
            [
                'label'         => 'Nama Peserta',
                'question_type' => 'short_text',
                'placeholder'   => 'Masukkan nama lengkap',
                'is_required'   => true
            ],
            [
                'label'         => 'Email',
                'question_type' => 'email',
                'placeholder'   => 'Masukkan email aktif',
                'is_required'   => true
            ],
            [
                'label'         => 'No. Telepon',
                'question_type' => 'short_text',
                'placeholder'   => 'Masukkan nomor HP',
                'is_required'   => false
            ]
        ];

        $sort = 1;
        foreach ($questions as $q) {
            // insert ke form_question pakai RETURNING juga
            $this->db->query("
            INSERT INTO event_manager.form_question
            (form_id, section_id, label, question_type, placeholder, is_required, sort_order, is_system, created_at, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
                $form_id,
                $section_id,
                $q['label'],
                $q['question_type'],
                $q['placeholder'],
                $q['is_required'],
                $sort++,
                true, // is_system
                date('Y-m-d H:i:s'),
                $user_id
            ]);
        }

        return $form_id;
    }
}
