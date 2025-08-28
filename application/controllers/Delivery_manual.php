<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_manual extends CI_Controller
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
        $title = 'Realisasi Pengiriman Manual';

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
        $this->db->where('delivery_note.kategori', 2);
        $this->db->order_by('delivery_note.send_date', 'DESC');
        $delivery = $this->db->get('delivery_note')->result();

        $data = [
            'title' => $title,
            'admin' => $admin,
            'delivery' => $delivery,
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Delivery_manual/v_delivery_manual');
    }

    public function createDelivery()
    {
        $no_manual = $this->input->post('inputNo');
        $send_date = date("Y-m-d H:i:s");
        $username = $this->session->userdata('username');
        $iduser = $this->session->userdata('iduser');
        $now = date("Y-m-d H:i:s");

        // Cek duplikasi no_manual
        $cek = $this->db->get_where('delivery_note', [
            'no_manual' => $no_manual,
            'status' => 1,
            'kategori' => 2
        ])->row();

        if ($cek) {
            $this->session->set_flashdata('error', 'No Surat Jalan sudah terpakai. Gunakan nomor yang berbeda.');
            redirect('delivery_manual');
            return;
        }

        $foto = '';

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
                redirect('delivery_manual');
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
            'kategori' => 2
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

        // WhatsApp API
        $this->db->select('handphone');
        $this->db->from('user');
        $this->db->where('idrole', 1);
        $this->db->where('is_whatsapp', 1);
        $this->db->where('status', 1);
        $this->db->where('handphone IS NOT NULL');
        $query = $this->db->get();
        $results = $query->result();

        $targets = array_column($results, 'handphone');
        $target = count($targets) > 1 ? implode(',', $targets) : (count($targets) === 1 ? $targets[0] : '');

        if ($target !== '') {
            $token = 'EyuhsmTqzeKaDknoxdxt';
            $message = 'Surat Jalan Manual dengan nomor ' . $no_manual . ' dibuat oleh ' . $username . ' sedang dalam pengiriman ke IV, harap ditunggu';

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
            $response = curl_exec($curl);
            curl_close($curl);
            echo $response;
        }

        $this->session->set_flashdata('success', 'Realisasi pengiriman berhasil ditambahkan.');
        redirect('delivery_manual');
    }

    public function updateDelivery()
    {
        $id = $this->input->get('id');
        $username = $this->session->userdata('username');
        $now = date("Y-m-d H:i:s");

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID pengiriman tidak ditemukan.']);
            return;
        }

        $cek = $this->db->get_where('delivery_note', [
            'iddelivery_note' => $id,
            'kategori' => 2
        ])->row();

        if (!$cek) {
            echo json_encode(['status' => 'error', 'message' => 'Data pengiriman tidak ditemukan.']);
            return;
        }

        $exists = $this->db->get_where('delivery_note_log', [
            'iddelivery_note' => $id,
            'progress' => 2
        ])->num_rows();

        if ($exists > 0) {
            echo json_encode(['status' => 'warning', 'message' => 'Progress 2 sudah pernah ditambahkan sebelumnya.']);
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
        $this->db->where('is_whatsapp', 1);
        $this->db->where('status', 1);
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

        echo json_encode(['status' => 'success', 'message' => 'Progress berhasil ditambahkan.']);
    }

    public function validasiDelivery()
    {
        $id = $this->input->get('id');
        $username = $this->session->userdata('username');
        $now = date("Y-m-d H:i:s");

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID pengiriman tidak ditemukan.']);
            return;
        }

        $cek = $this->db->get_where('delivery_note', [
            'iddelivery_note' => $id,
            'kategori' => 2
        ])->row();

        if (!$cek) {
            echo json_encode(['status' => 'error', 'message' => 'Data pengiriman tidak ditemukan.']);
            return;
        }

        $exists = $this->db->get_where('delivery_note_log', [
            'iddelivery_note' => $id,
            'progress' => 3
        ])->num_rows();

        if ($exists > 0) {
            echo json_encode(['status' => 'warning', 'message' => 'Progress 3 sudah pernah ditambahkan sebelumnya.']);
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
        $this->db->where('is_whatsapp', 1);
        $this->db->where('status', 1);
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

        echo json_encode(['status' => 'success', 'message' => 'Validasi pengiriman berhasil.']);
    }

    public function finalDelivery()
    {
        $id = $this->input->get('id');
        $username = $this->session->userdata('username');
        $now = date("Y-m-d H:i:s");

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID pengiriman tidak ditemukan.']);
            return;
        }

        $cek = $this->db->get_where('delivery_note', [
            'iddelivery_note' => $id,
            'kategori' => 2 // Ensure this is for manual deliveries
        ])->row();

        if (!$cek) {
            echo json_encode(['status' => 'error', 'message' => 'Data pengiriman tidak ditemukan.']);
            return;
        }

        $exists = $this->db->get_where('delivery_note_log', [
            'iddelivery_note' => $id,
            'progress' => 4
        ])->num_rows();

        if ($exists > 0) {
            echo json_encode(['status' => 'warning', 'message' => 'Progress 4 sudah pernah ditambahkan sebelumnya.']);
            return;
        }

        $log = [
            'iddelivery_note' => $id,
            'progress' => 4,
            'description' => 'Pengiriman Final',
            'created_by' => $username,
            'created_date' => $now,
            'status' => 1
        ];
        $this->db->insert('delivery_note_log', $log);

        echo json_encode(['status' => 'success', 'message' => 'Finalisasi pengiriman berhasil.']);
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
        redirect('delivery_manual');
    }

    public function getDeliveryRow($id)
    {
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
        $this->db->where('delivery_note.iddelivery_note', $id);
        $result = $this->db->get('delivery_note')->row();

        echo json_encode($result);
    }

    public function exportExcel()
    {
        $filterInputStart = $this->input->post('filterInputStart');
        $filterInputEnd   = $this->input->post('filterInputEnd');

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
        $this->db->where('delivery_note.kategori', 2); // Manual deliveries only

        // Filter berdasarkan input tanggal
        if (!empty($filterInputStart) && !empty($filterInputEnd)) {
            $this->db->where('DATE(delivery_note.created_date) >=', $filterInputStart);
            $this->db->where('DATE(delivery_note.created_date) <=', $filterInputEnd);
        }

        $this->db->order_by('delivery_note.send_date', 'DESC');
        $delivery = $this->db->get('delivery_note')->result();

        // Buat spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Styling
        $styleHeader = [
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleSubHeader = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleTableHeader = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f0f0f0']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleBorder = [
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];

        $row = 1;

        // Header judul
        $sheet->mergeCells("A{$row}:G{$row}")->setCellValue("A{$row}", "ASTA HOMEWARE");
        $sheet->getStyle("A{$row}")->applyFromArray($styleHeader);
        $row++;

        $sheet->mergeCells("A{$row}:G{$row}")->setCellValue("A{$row}", "LAPORAN REALISASI PENGIRIMAN MANUAL");
        $sheet->getStyle("A{$row}")->applyFromArray($styleSubHeader);
        $row++;

        $periodeText = "Periode: " . ($filterInputStart ?? '-') . " s/d " . ($filterInputEnd ?? '-');
        $sheet->mergeCells("A{$row}:G{$row}")->setCellValue("A{$row}", $periodeText);
        $sheet->getStyle("A{$row}")->getFont()->setItalic(true);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $row += 2;

        // Header tabel
        $headers = ['No', 'No Surat Jalan', 'Tanggal Kirim', 'Input By', 'Tanggal Input', 'Progress', 'Foto'];
        $sheet->fromArray($headers, null, "A{$row}");
        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($styleTableHeader);

        // Set initial column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(40); // Width for photo column

        $row++;

        // Track maximum photo width for column sizing
        $maxPhotoWidth = 0;
        $maxPhotoHeight = 0;
        $photoDimensions = []; // Store dimensions for all photos

        // Data
        $no = 1;
        foreach ($delivery as $d) {
            // Convert progress number to text
            $progressText = '';
            switch ($d->progress) {
                case 1:
                    $progressText = 'Dikirim';
                    break;
                case 2:
                    $progressText = 'Terverifikasi(Diterima)';
                    break;
                case 3:
                    $progressText = 'Tervalidasi(Terdata)';
                    break;
                case 4:
                    $progressText = 'Final Direksi';
                    break;
                default:
                    $progressText = 'Unknown';
            }

            // Set data for each row
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $d->no_manual);
            $sheet->setCellValue("C{$row}", $d->send_date);
            $sheet->setCellValue("D{$row}", $d->user_input);
            $sheet->setCellValue("E{$row}", $d->created_date);
            $sheet->setCellValue("F{$row}", $progressText);

            // Insert image if exists
            if (!empty($d->foto)) {
                $imagePath = FCPATH . 'assets/image/surat_jalan/' . $d->foto;

                if (file_exists($imagePath)) {
                    try {
                        // Get original image dimensions
                        list($width, $height) = getimagesize($imagePath);

                        // Store dimensions for later column width calculation
                        $photoDimensions[] = [
                            'width' => $width,
                            'height' => $height,
                            'row' => $row
                        ];

                        // Calculate display size while maintaining aspect ratio
                        $maxDisplayWidth = 400;
                        $maxDisplayHeight = 300;

                        $ratio = min($maxDisplayWidth / $width, $maxDisplayHeight / $height);
                        $displayWidth = (int)($width * $ratio);
                        $displayHeight = (int)($height * $ratio);

                        // Track maximum dimensions for column sizing
                        $maxPhotoWidth = max($maxPhotoWidth, $displayWidth);
                        $maxPhotoHeight = max($maxPhotoHeight, $displayHeight);

                        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawing->setName('Surat Jalan');
                        $drawing->setDescription('Surat Jalan');
                        $drawing->setPath($imagePath);
                        $drawing->setResizeProportional(true); // Maintain aspect ratio
                        $drawing->setWidth($displayWidth);
                        $drawing->setHeight($displayHeight);
                        $drawing->setCoordinates("G{$row}");
                        $drawing->setOffsetX(5);
                        $drawing->setOffsetY(5);
                        $drawing->setWorksheet($sheet);

                        // Set row height to accommodate image
                        $sheet->getRowDimension($row)->setRowHeight($displayHeight + 10);
                    } catch (Exception $e) {
                        $sheet->setCellValue("G{$row}", 'Gagal memuat gambar');
                    }
                } else {
                    $sheet->setCellValue("G{$row}", 'File tidak ditemukan');
                }
            } else {
                $sheet->setCellValue("G{$row}", 'Tidak ada foto');
            }

            // Apply border to the entire row
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($styleBorder);
            $row++;
        }

        // Calculate optimal column width based on all photos
        if (!empty($photoDimensions)) {
            // Find the maximum display width needed
            $maxDisplayWidth = 0;
            foreach ($photoDimensions as $dimension) {
                $maxDisplayWidth = max($maxDisplayWidth, $dimension['width']);
            }

            // Convert to Excel column width (pixels to Excel width units)
            $pixelsToExcelWidth = 0.14; // Approximate conversion factor
            $excelWidth = min(50, max(15, round($maxDisplayWidth * $pixelsToExcelWidth)));

            $sheet->getColumnDimension('G')->setWidth($excelWidth);

            // Also adjust row heights if needed for very tall images
            foreach ($photoDimensions as $dimension) {
                $ratio = min(400 / $dimension['width'], 300 / $dimension['height']);
                $displayHeight = (int)($dimension['height'] * $ratio);
                $sheet->getRowDimension($dimension['row'])->setRowHeight($displayHeight + 10);
            }
        }

        // Set alignment for data cells
        $lastRow = $row - 1;
        $sheet->getStyle("A6:G{$lastRow}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
        $sheet->getStyle("A6:A{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F6:F{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set auto filter
        $sheet->setAutoFilter("A5:G5");

        // Set judul worksheet
        $sheet->setTitle('Realisasi Pengiriman Manual');

        $filename = 'Realisasi_Pengiriman_Manual_' . date('Y-m-d_H-i-s') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }
}
