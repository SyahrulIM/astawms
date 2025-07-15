<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_note extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Eeettss gak boleh nakal, Login dulu ya kak hehe.');
            redirect('auth');
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
        $this->db->where('delivery_note.kategori', 1); // filter kategori 1
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

        $cek = $this->db->get_where('delivery_note', ['no_manual' => $no_manual, 'status' => 1])->row();
        if ($cek) {
            $this->session->set_flashdata('error', 'No Surat Jalan sudah terpakai. Gunakan nomor yang berbeda.');
            redirect('delivery_note');
            return;
        }

        $foto = '';

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
            'status' => 1,
            'kategori' => 1 // default kategori 1
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

        // Start Kirim pesan WhatsApp via Fonnte
        $this->db->select('handphone');
        $this->db->from('user');
        $this->db->where('idrole', 6);
        $this->db->where('handphone IS NOT NULL');
        $query = $this->db->get();
        $results = $query->result();

        $targets = array_column($results, 'handphone');
        $target = count($targets) > 1 ? implode(',', $targets) : (count($targets) === 1 ? $targets[0] : '');

        if ($target !== '') {
            $token = 'EyuhsmTqzeKaDknoxdxt';
            $message = 'Surat Jalan dengan nomor ' . $no_manual . ' dibuat oleh ' . $username . ' sedang dalam pengiriman ke IV, harap ditunggu';

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => array(
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62',
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $token
                ),
            ));

            curl_exec($curl);
            curl_close($curl);
        }
        // End

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

        $cek = $this->db->get_where('delivery_note', ['iddelivery_note' => $id])->row();
        if (!$cek) {
            $this->session->set_flashdata('error', 'Data pengiriman tidak ditemukan.');
            redirect('delivery_note');
            return;
        }

        $exists = $this->db->get_where('delivery_note_log', [
            'iddelivery_note' => $id,
            'progress' => 2
        ])->num_rows();

        if ($exists > 0) {
            $this->session->set_flashdata('warning', 'Progress 2 sudah pernah ditambahkan sebelumnya.');
            redirect('delivery_note');
            return;
        }

        $log = [
            'iddelivery_note' => $id,
            'progress' => 2,
            'description' => 'Pengiriman Diproses',
            'created_by' => $username,
            'created_date' => $now,
            'status' => 1
        ];
        $this->db->insert('delivery_note_log', $log);

        // Start Kirim pesan WhatsApp via Fonnte
        $this->db->select('handphone');
        $this->db->from('user');
        $this->db->where('idrole', 5);
        $this->db->where('handphone IS NOT NULL');
        $query = $this->db->get();
        $results = $query->result();

        $targets = array_column($results, 'handphone');
        $target = count($targets) > 1 ? implode(',', $targets) : (count($targets) === 1 ? $targets[0] : '');

        if ($target !== '') {
            $token = 'EyuhsmTqzeKaDknoxdxt';
            $message = 'Surat Jalan dengan nomor ' . $id . ' dibuat oleh ' . $username . ' sudah diverifikasi dan sekarang membutuhkan validasi dari bagian accounting di WMS. Mohon segera diproses, terima kasih.';

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => array(
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62',
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $token
                ),
            ));

            curl_exec($curl);
            curl_close($curl);
        }
        // End

        $this->session->set_flashdata('success', 'Progress berhasil ditambahkan.');
        redirect('delivery_note');
    }

    public function validasiDelivery()
    {
        $id = $this->input->get('id');
        $username = $this->session->userdata('username');
        $now = date("Y-m-d H:i:s");

        if (!$id) {
            $this->session->set_flashdata('error', 'ID pengiriman tidak ditemukan.');
            redirect('delivery_note');
            return;
        }

        $cek = $this->db->get_where('delivery_note', ['iddelivery_note' => $id])->row();
        if (!$cek) {
            $this->session->set_flashdata('error', 'Data pengiriman tidak ditemukan.');
            redirect('delivery_note');
            return;
        }

        $exists = $this->db->get_where('delivery_note_log', [
            'iddelivery_note' => $id,
            'progress' => 3
        ])->num_rows();

        if ($exists > 0) {
            $this->session->set_flashdata('warning', 'Progress 3 sudah pernah ditambahkan sebelumnya.');
            redirect('delivery_note');
            return;
        }

        $log = [
            'iddelivery_note' => $id,
            'progress' => 3,
            'description' => 'Pengiriman Divalidasi',
            'created_by' => $username,
            'created_date' => $now,
            'status' => 1
        ];
        $this->db->insert('delivery_note_log', $log);

        // Start Kirim pesan WhatsApp via Fonnte
        $this->db->select('handphone');
        $this->db->from('user');
        $this->db->where('idrole', 1);
        $this->db->where('handphone IS NOT NULL');
        $query = $this->db->get();
        $results = $query->result();

        $targets = array_column($results, 'handphone');
        $target = count($targets) > 1 ? implode(',', $targets) : (count($targets) === 1 ? $targets[0] : '');

        if ($target !== '') {
            $token = 'EyuhsmTqzeKaDknoxdxt';
            $message = 'Surat Jalan dengan nomor ' . $id . ' dibuat oleh ' . $username . ' sudah divalidasi dan sekarang membutuhkan Final Dir dari superadmin di WMS. Mohon segera diproses, terima kasih.';

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => array(
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62',
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $token
                ),
            ));

            curl_exec($curl);
            curl_close($curl);
        }
        // End

        $this->session->set_flashdata('success', 'Validasi pengiriman berhasil.');
        redirect('delivery_note');
    }

    public function finalDelivery()
    {
        $id = $this->input->get('id');
        $username = $this->session->userdata('username');
        $now = date("Y-m-d H:i:s");

        if (!$id) {
            $this->session->set_flashdata('error', 'ID pengiriman tidak ditemukan.');
            redirect('delivery_note');
            return;
        }

        $cek = $this->db->get_where('delivery_note', ['iddelivery_note' => $id])->row();
        if (!$cek) {
            $this->session->set_flashdata('error', 'Data pengiriman tidak ditemukan.');
            redirect('delivery_note');
            return;
        }

        $exists = $this->db->get_where('delivery_note_log', [
            'iddelivery_note' => $id,
            'progress' => 4
        ])->num_rows();

        if ($exists > 0) {
            $this->session->set_flashdata('warning', 'Progress 4 sudah pernah ditambahkan sebelumnya.');
            redirect('delivery_note');
            return;
        }

        $log = [
            'iddelivery_note' => $id,
            'progress' => 4,
            'description' => 'Pengiriman Selesai',
            'created_by' => $username,
            'created_date' => $now,
            'status' => 1
        ];
        $this->db->insert('delivery_note_log', $log);

        $this->session->set_flashdata('success', 'Pengiriman berhasil diselesaikan.');
        redirect('delivery_note');
    }

    public function revisionDelivery()
    {
        $this->load->library('upload');

        $id = $this->input->post('id');
        $no_manual = $this->input->post('no_manual');
        $username = $this->session->userdata('username');
        $now = date("Y-m-d H:i:s");

        if (empty($id)) {
            $this->session->set_flashdata('error', 'ID pengiriman wajib diisi.');
            redirect('delivery_note');
            return;
        }

        // Get current delivery data
        $current_data = $this->db->get_where('delivery_note', ['iddelivery_note' => $id])->row();
        if (!$current_data) {
            $this->session->set_flashdata('error', 'Data pengiriman tidak ditemukan.');
            redirect('delivery_note');
            return;
        }

        // Get last log entry for description
        $this->db->where('iddelivery_note', $id);
        $this->db->order_by('created_date', 'DESC');
        $last_log = $this->db->get('delivery_note_log')->row();
        $last_description = $last_log ? $last_log->description : '';
        $last_progress = $last_log ? $last_log->progress : 1;

        // Prepare update data
        $delivery_data = [
            'updated_by' => $username,
            'updated_date' => $now
        ];

        // Handle no_manual update
        if (!empty($no_manual)) {
            $delivery_data['no_manual'] = $no_manual;
        } else {
            $delivery_data['no_manual'] = $current_data->no_manual;
        }

        // Handle file upload
        if (!empty($_FILES['foto']['name'])) {
            $config['upload_path'] = './assets/image/surat_jalan/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;
            $config['file_name'] = 'SJ_' . time();

            $this->upload->initialize($config);

            if (!$this->upload->do_upload('foto')) {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect('delivery_note');
                return;
            }

            $upload_data = $this->upload->data();
            $delivery_data['foto'] = $upload_data['file_name'];
        } else {
            $delivery_data['foto'] = $current_data->foto;
        }

        // Update delivery note
        $this->db->where('iddelivery_note', $id);
        $this->db->update('delivery_note', $delivery_data);

        // Create revision log with previous description
        $log_data = [
            'iddelivery_note' => $id,
            'progress' => $last_progress,
            'description' => $last_description, // Maintain previous description
            'status' => '1',
            'status_revision' => '1',
            'created_by' => $username,
            'created_date' => $now
        ];

        $this->db->insert('delivery_note_log', $log_data);

        $this->session->set_flashdata('success', 'Revisi pengiriman berhasil disimpan.');
        redirect('delivery_note');
    }
}
