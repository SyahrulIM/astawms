<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_note extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Eeettss gak boleh nakal, Login dulu ya kak hehe.');
            redirect('auth');  // Assuming 'auth' is your login controller
        }
    }

    public function index()
    {
        $title = 'Realisasi Pengiriman';

        $this->db->where_in('user.idrole', [3, 5]);
        $this->db->where('user.status', 1);
        $this->db->join('role', 'role.idrole = user.idrole');
        $admin = $this->db->get('user')->result();

        $this->db->select('
    delivery_note.iddelivery_note,
    delivery_note.no_manual,
    delivery_note.send_date,
    user_input.full_name as user_input,
    delivery_note.created_date,
    delivery_note_log.progress,
    delivery_note.foto
');
        $this->db->join('user as user_input', 'user_input.iduser = delivery_note.iduser', 'left');

        $this->db->join(
            '(SELECT t1.* FROM delivery_note_log t1
      JOIN (
        SELECT iddelivery_note, MAX(created_date) as max_date
        FROM delivery_note_log
        GROUP BY iddelivery_note
      ) t2 ON t1.iddelivery_note = t2.iddelivery_note AND t1.created_date = t2.max_date
    ) delivery_note_log',
            'delivery_note_log.iddelivery_note = delivery_note.iddelivery_note',
            'left'
        );

        $this->db->where('delivery_note.status', 1);
        $delivery = $this->db->get('delivery_note')->result();

        $data = [
            'title' => $title,
            'admin' => $admin,
            'delivery' => $delivery,
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Delivery_note/v_delivery_note');
    }

    public function createDelivery()
    {
        $no_manual = $this->input->post('inputNo');
        $send_date = date("Y-m-d H:i:s");
        $username = $this->session->userdata('username');
        $iduser = $this->session->userdata('iduser');
        $now = date("Y-m-d H:i:s");

        // Cek duplikasi no_manual
        $cek = $this->db->get_where('delivery_note', ['no_manual' => $no_manual, 'status' => 1])->row();
        if ($cek) {
            $this->session->set_flashdata('error', 'No Surat Jalan sudah terpakai. Gunakan nomor yang berbeda.');
            redirect('delivery_note');
            return;
        }

        $foto = ''; // Default kosong

        // Upload gambar
        if (!empty($_FILES['inputFoto']['name'])) {
            $config['upload_path'] = './assets/image/surat_jalan/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = 'surat_jalan_' . time();
            $config['overwrite'] = true;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('inputFoto')) {
                $uploadData = $this->upload->data();
                $foto = $uploadData['file_name'];
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect('delivery_note');
                return;
            }
        }

        $data = [
            'no_manual' => $no_manual,
            'foto' => $foto,
            'send_date' => $send_date,
            'iduser' => $iduser,
            'created_by' => $username,
            'created_date' => $now,
            'updated_by' => $username,
            'updated_date' => $now,
            'status' => 1
        ];

        $this->db->insert('delivery_note', $data);
        $iddelivery_note = $this->db->insert_id();

        $data_log = [
            'iddelivery_note' => $iddelivery_note,
            'progress' => 1,
            'description' => 'Verifikasi Pengiriman',
            'created_by' => $username,
            'created_date' => $now,
            'status' => 1
        ];

        $this->db->insert('delivery_note_log', $data_log);

        $this->session->set_flashdata('success', 'Realisasi pengiriman berhasil ditambahkan.');
        redirect('delivery_note');
    }

    public function updateDelivery()
    {
        $id = $this->input->get('id');
        $username = $this->session->userdata('username');
        $now = date("Y-m-d H:i:s");

        if (!$id) {
            $this->session->set_flashdata('error', 'ID pengiriman tidak ditemukan.');
            redirect('delivery_note');
            return;
        }

        // Cek apakah delivery_note valid
        $cek = $this->db->get_where('delivery_note', ['iddelivery_note' => $id])->row();
        if (!$cek) {
            $this->session->set_flashdata('error', 'Data pengiriman tidak ditemukan.');
            redirect('delivery_note');
            return;
        }

        // Cek apakah progress 2 sudah ada
        $exists = $this->db->get_where('delivery_note_log', [
            'iddelivery_note' => $id,
            'progress' => 2
        ])->num_rows();

        if ($exists > 0) {
            $this->session->set_flashdata('warning', 'Progress 2 sudah pernah ditambahkan sebelumnya.');
            redirect('delivery_note');
            return;
        }

        // Insert progress 2 ke log
        $log = [
            'iddelivery_note' => $id,
            'progress' => 2,
            'description' => 'Pengiriman Diproses',
            'created_by' => $username,
            'created_date' => $now,
            'status' => 1
        ];
        $this->db->insert('delivery_note_log', $log);

        $this->session->set_flashdata('success', 'Progress berhasil ditambahkan.');
        redirect('delivery_note');
    }
}
