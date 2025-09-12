<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Delivery_file extends CI_Controller
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
        $title = 'File Surat Jalan Supplier';

        $this->db->where('delivery_file.status', 1);
        $delivery = $this->db->get('delivery_file')->result();

        $data = [
            'title' => $title,
            'delivery' => $delivery
        ];

        $this->load->view('theme/v_head', $data);
        $this->load->view('Delivery_file/v_delivery_file');
    }

    public function createDelivery()
    {
        $inputNameSupplier = $this->input->post('inputNameSupplier');
        $inputDateReceived = $this->input->post('inputDateReceived');
        $inputFoto = $this->input->post('inputFoto');
        $username = $this->session->userdata('username');
        $iduser = $this->session->userdata('iduser');
        $now = date("Y-m-d H:i:s");

        // Cek duplikasi no_manual
        $cek = $this->db->get_where('delivery_file', [
            'date_received' => $inputDateReceived,
            'name_supplier' => $inputNameSupplier
        ])->row();

        if ($cek) {
            $this->session->set_flashdata('error', 'No Surat Jalan sudah terpakai. Gunakan nomor yang berbeda.');
            redirect('delivery_file');
            return;
        }

        $inputFoto = '';

        if (!empty($_FILES['inputFoto']['name'])) {
            $config['upload_path'] = './assets/image/surat_jalan/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = 'file_surat_jalan_' . time();
            $config['overwrite'] = true;

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('inputFoto')) {
                $uploadData = $this->upload->data();
                $inputFoto = $uploadData['file_name'];
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect('delivery_file');
                return;
            }
        }

        $data = [
            'name_supplier' => $inputNameSupplier,
            'foto' => $inputFoto,
            'date_received' => $inputDateReceived,
            'iduser' => $iduser,
            'created_by' => $username,
            'created_date' => $now,
            'updated_by' => $username,
            'updated_date' => $now,
            'status' => 1,
            'kategori' => 2
        ];

        $this->db->insert('delivery_file', $data);

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
            $message = 'File Surat Jalan dengan nama supplier ' . $inputNameSupplier . ' Tanggal diterima ' . $inputDateReceived . ' dibuat oleh ' . $username . ' sudah ditambahkan di File Surat Jalan';

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
        redirect('delivery_file');
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
                        $displayWidth = (int) ($width * $ratio);
                        $displayHeight = (int) ($height * $ratio);

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
                $displayHeight = (int) ($dimension['height'] * $ratio);
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

    public function deleteDelivery()
    {
        $id = $this->input->get('id');

        // Get data before update
        $delivery = $this->db->get_where('delivery_file', ['iddelivery_file' => $id])->row();

        if ($delivery) {
            // Soft delete (set status to 0)
            $this->db->set('status', 0);
            $this->db->where('iddelivery_file', $id);
            $this->db->update('delivery_file');

            // Set flash message with supplier name & date
            $this->session->set_flashdata(
                'success',
                'Delivery from <b>' . $delivery->name_supplier . '</b> on <b>' . $delivery->date_received . '</b> has been successfully deleted.'
            );
        } else {
            $this->session->set_flashdata('error', 'Delivery not found.');
        }

        redirect('delivery_file');
    }
}
