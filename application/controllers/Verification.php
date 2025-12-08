<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Verification extends CI_Controller
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
		$query = "
        SELECT 
            CONVERT('INSTOCK' USING utf8mb4) AS tipe,
            CONVERT(i.instock_code USING utf8mb4) AS kode_transaksi,
            CONVERT(i.no_manual USING utf8mb4) AS no_manual,
            i.tgl_terima AS tanggal,
            i.jam_terima AS jam,
            i.distribution_date AS distribution_date,
            CONVERT(i.kategori USING utf8mb4) AS kategori,
            CONVERT(i.user USING utf8mb4) AS user,
            CONVERT(g.nama_gudang USING utf8mb4) AS nama_gudang,
            i.status_verification
        FROM instock i
        LEFT JOIN gudang g ON g.idgudang = i.idgudang

        UNION ALL

        SELECT 
            CONVERT('OUTSTOCK' USING utf8mb4) AS tipe,
            CONVERT(o.outstock_code USING utf8mb4) AS kode_transaksi,
            CONVERT(o.no_manual USING utf8mb4) AS no_manual,
            o.tgl_keluar AS tanggal,
            o.jam_keluar AS jam,
            o.distribution_date AS distribution_date,
            CONVERT(o.kategori USING utf8mb4) AS kategori,
            CONVERT(o.user USING utf8mb4) AS user,
            CONVERT(g.nama_gudang USING utf8mb4) AS nama_gudang,
            o.status_verification
        FROM outstock o
        LEFT JOIN gudang g ON g.idgudang = o.idgudang

        UNION ALL

        SELECT
            CONVERT('Packing List' USING utf8mb4) AS tipe,
            CONVERT(a.number_po USING utf8mb4) AS kode_transaksi,
            CONVERT(a.no_manual USING utf8mb4) AS no_manual,
            a.order_date AS tanggal,
            a.order_time AS jam,
            a.distribution_date AS distribution_date,
            CONVERT(a.kategori USING utf8mb4) AS kategori,
            CONVERT(a.user USING utf8mb4) AS user,
            CONVERT(g.nama_gudang USING utf8mb4) AS nama_gudang,
            a.status_verification
        FROM analisys_po a
        LEFT JOIN gudang g ON g.idgudang = a.idgudang

        ORDER BY tanggal DESC, jam DESC
    ";

		$data['transactions'] = $this->db->query($query)->result();
		$data['warehouse'] = $this->db->get('gudang')->result();
		$data['title'] = 'Verification';

		$this->load->view('theme/v_head', $data);
		$this->load->view('Verification/v_verification', $data);
	}

	public function confirm_stock($type, $code)
	{
		$type = strtolower($type);
		if ($type !== 'instock' && $type !== 'outstock') {
			show_error('Tipe stok tidak valid.');
		}

		$main_table = $type;
		$kode_field = $type . '_code';

		$trx = $this->db->where($kode_field, $code)->get($main_table)->row();
		if (!$trx) {
			show_error(ucfirst($type) . ' tidak ditemukan.');
			return;
		}

		// Ambil tanggal transaksi
		$tanggal_field = $type === 'instock' ? 'tgl_terima' : 'tgl_keluar';
		$jam_field = $type === 'instock' ? 'jam_terima' : 'jam_keluar';

		$tanggal = $trx->$tanggal_field;
		$jam = $trx->$jam_field;

		// Cek apakah ada transaksi sebelumnya yang belum diverifikasi
		$query_unverified = "
			SELECT * FROM (
				SELECT tgl_terima AS tanggal, jam_terima AS jam FROM instock WHERE status_verification = 0
				UNION ALL
				SELECT tgl_keluar AS tanggal, jam_keluar AS jam FROM outstock WHERE status_verification = 0
			) AS all_unverified
			WHERE (tanggal < '$tanggal') OR (tanggal = '$tanggal' AND jam < '$jam')
			LIMIT 1
		";

		$older_unverified = $this->db->query($query_unverified)->row();
		if ($older_unverified) {
			$this->session->set_flashdata('error', 'Terdapat transaksi sebelumnya yang belum diverifikasi. Harap verifikasi berdasarkan urutan waktu.');
			redirect('verification');
			return;
		}

		// Lanjutkan verifikasi jika tidak ada yang lebih lama
		if ($trx->status_verification != 0) {
			$this->session->set_flashdata('error', 'Transaksi sudah diverifikasi sebelumnya.');
			redirect('verification');
			return;
		}

		$this->db->set('status_verification', 1)
			->where($kode_field, $code)
			->update($main_table);

		$idgudang = $trx->idgudang;
		$detail_table = 'detail_' . $type;
		$details = $this->db->where($kode_field, $code)->get($detail_table)->result();

		foreach ($details as $detail) {
			$sku = $detail->sku;
			$jumlah = (int) $detail->jumlah;

			$product = $this->db->where('sku', $sku)->get('product')->row();
			if (!$product) continue;

			$idproduct = $product->idproduct;

			$stock = $this->db->where('idproduct', $idproduct)->where('idgudang', $idgudang)->get('product_stock')->row();
			if (!$stock) {
				$this->db->insert('product_stock', [
					'idproduct' => $idproduct,
					'idgudang' => $idgudang,
					'stok' => 0
				]);
			}

			if ($type === 'instock') {
				$this->db->set('stok', "stok + {$jumlah}", false);
			} else {
				$this->db->set('stok', "stok - {$jumlah}", false);
			}

			$this->db->where('idproduct', $idproduct)
				->where('idgudang', $idgudang)
				->update('product_stock');
		}

		$this->session->set_flashdata('success', 'Stok berhasil diverifikasi dan diperbarui.');
		redirect('verification');
	}

	public function get_details($type, $kode)
	{
		$type = strtolower($type);

		// Daftar tipe valid
		$valid_types = ['instock', 'outstock', 'packing'];
		if (!in_array($type, $valid_types)) {
			echo json_encode(['error' => 'Invalid type']);
			return;
		}

		if ($type == 'instock') {
			$kode_field = 'instock_code';
			$detail_table = 'detail_instock';
		} elseif ($type == 'outstock') {
			$kode_field = 'outstock_code';
			$detail_table = 'detail_outstock';
		} elseif ($type == 'packing') {
			// untuk analisys_po
			$kode_field = 'number_po';
			$detail_table = 'detail_analisys_po';
		}

		// Query Penarik Data
		$details = $this->db
			->select("$detail_table.*, p.nama_produk")
			->from($detail_table)
			->join('product p', "$detail_table.sku = p.sku", 'left')
			->where("$detail_table.$kode_field", $kode)
			->get()
			->result();

		if ($details) {
			echo json_encode(['details' => $details]);
		} else {
			echo json_encode(['error' => 'Data tidak ditemukan']);
		}
	}


	public function reject($type, $code)
	{
		$type = strtolower($type);
		if ($type !== 'instock' && $type !== 'outstock') {
			show_error('Tipe stok tidak valid.');
		}

		$main_table = $type;
		$kode_field = $type . '_code';

		$trx = $this->db->where($kode_field, $code)->get($main_table)->row();
		if (!$trx) {
			show_error('Transaksi tidak ditemukan.');
			return;
		}

		// Ambil tanggal dan jam transaksi
		$tanggal_field = $type === 'instock' ? 'tgl_terima' : 'tgl_keluar';
		$jam_field = $type === 'instock' ? 'jam_terima' : 'jam_keluar';

		$tanggal = $trx->$tanggal_field;
		$jam = $trx->$jam_field;

		// Cek apakah ada transaksi sebelumnya yang belum diverifikasi
		$query_unverified = "
			SELECT * FROM (
				SELECT tgl_terima AS tanggal, jam_terima AS jam FROM instock WHERE status_verification = 0
				UNION ALL
				SELECT tgl_keluar AS tanggal, jam_keluar AS jam FROM outstock WHERE status_verification = 0
			) AS all_unverified
			WHERE (tanggal < '$tanggal') OR (tanggal = '$tanggal' AND jam < '$jam')
			LIMIT 1
		";

		$older_unverified = $this->db->query($query_unverified)->row();
		if ($older_unverified) {
			$this->session->set_flashdata('error', 'Terdapat transaksi sebelumnya yang belum diverifikasi atau ditolak. Harap proses berdasarkan urutan waktu.');
			redirect('verification');
			return;
		}

		// Cegah reject ulang
		if ($trx->status_verification != 0) {
			$this->session->set_flashdata('error', 'Transaksi sudah diproses sebelumnya.');
			redirect('verification');
			return;
		}

		$this->db->set('status_verification', 2)
			->where($kode_field, $code)
			->update($main_table);

		$this->session->set_flashdata('error', 'Transaksi berhasil ditolak.');
		redirect('verification');
	}

	public function exportExcel()
	{
		$start_date = $this->input->post('filterInputStart');
		$end_date = $this->input->post('filterInputEnd');

		// Default filter tanggal jika kosong (7 hari terakhir)
		if (!$start_date && !$end_date) {
			$start_date = date('Y-m-d', strtotime('-7 days'));
			$end_date = date('Y-m-d');
		}

		$whereIn = "";
		$whereOut = "";

		if ($start_date && $end_date) {
			$whereIn = "WHERE i.tgl_terima BETWEEN '$start_date' AND '$end_date'";
			$whereOut = "WHERE o.tgl_keluar BETWEEN '$start_date' AND '$end_date'";
		} elseif ($start_date) {
			$whereIn = "WHERE i.tgl_terima >= '$start_date'";
			$whereOut = "WHERE o.tgl_keluar >= '$start_date'";
		} elseif ($end_date) {
			$whereIn = "WHERE i.tgl_terima <= '$end_date'";
			$whereOut = "WHERE o.tgl_keluar <= '$end_date'";
		}

		$query = "
        SELECT 'INSTOCK' AS tipe, i.instock_code AS kode_transaksi, i.no_manual, i.tgl_terima AS tanggal,
               i.jam_terima AS jam, i.distribution_date, i.kategori, i.user, g.nama_gudang, i.status_verification
        FROM instock i
        LEFT JOIN gudang g ON g.idgudang = i.idgudang
        $whereIn

        UNION ALL

        SELECT 'OUTSTOCK' AS tipe, o.outstock_code AS kode_transaksi, o.no_manual, o.tgl_keluar AS tanggal,
               o.jam_keluar AS jam, o.distribution_date, o.kategori, o.user, g.nama_gudang, o.status_verification
        FROM outstock o
        LEFT JOIN gudang g ON g.idgudang = o.idgudang
        $whereOut

        ORDER BY tanggal DESC, jam DESC
    ";

		$transactions = $this->db->query($query)->result();

		// Ambil semua kode transaksi berdasarkan tipe untuk ambil detail sekaligus
		$instockCodes = [];
		$outstockCodes = [];

		foreach ($transactions as $trx) {
			if ($trx->tipe === 'INSTOCK') {
				$instockCodes[] = $trx->kode_transaksi;
			} else {
				$outstockCodes[] = $trx->kode_transaksi;
			}
		}

		// Ambil detail_instock sekaligus
		$detailInstock = [];
		if (!empty($instockCodes)) {
			$this->db->where_in('instock_code', $instockCodes);
			$detailInstock = $this->db->get('detail_instock')->result();
		}

		// Ambil detail_outstock sekaligus
		$detailOutstock = [];
		if (!empty($outstockCodes)) {
			$this->db->where_in('outstock_code', $outstockCodes);
			$detailOutstock = $this->db->get('detail_outstock')->result();
		}

		// Group detail berdasarkan kode transaksi
		$groupedDetailInstock = [];
		foreach ($detailInstock as $d) {
			$groupedDetailInstock[$d->instock_code][] = $d;
		}

		$groupedDetailOutstock = [];
		foreach ($detailOutstock as $d) {
			$groupedDetailOutstock[$d->outstock_code][] = $d;
		}

		// Pasang detail ke masing-masing transaksi
		foreach ($transactions as &$trx) {
			if ($trx->tipe === 'INSTOCK') {
				$trx->details = $groupedDetailInstock[$trx->kode_transaksi] ?? [];
			} else {
				$trx->details = $groupedDetailOutstock[$trx->kode_transaksi] ?? [];
			}
		}

		// Buat spreadsheet
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// Styling
		$styleHeader = [
			'font' => ['bold' => true],
			'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
		];
		$styleTableHeader = [
			'font' => ['bold' => true],
			'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f0f0f0']],
			'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
			'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
		];
		$styleDetailHeader = [
			'font' => ['bold' => true],
			'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'd9edf7']],
			'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
		];
		$styleBorder = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]];

		$row = 1;

		// Header judul
		$sheet->mergeCells("A{$row}:I{$row}")->setCellValue("A{$row}", "Asta Homeware");
		$sheet->getStyle("A{$row}")->applyFromArray($styleHeader);
		$row++;

		$sheet->mergeCells("A{$row}:I{$row}")->setCellValue("A{$row}", "Data Verifikasi Transaksi");
		$sheet->getStyle("A{$row}")->applyFromArray($styleHeader);
		$row++;

		$sheet->mergeCells("A{$row}:I{$row}")->setCellValue("A{$row}", "Periode: " . ($start_date ?? '-') . " s/d " . ($end_date ?? '-'));
		$row += 2;

		$no = 1;
		foreach ($transactions as $trx) {
			// Header tabel
			$sheet->fromArray([
				'No', 'Tipe', 'Kode Transaksi', 'Nomer', 'Tanggal Input', 'Tanggal Distribusi', 'User', 'Gudang', 'Status'
			], null, "A{$row}");
			$sheet->getStyle("A{$row}:I{$row}")->applyFromArray($styleTableHeader);
			$row++;

			// Data transaksi
			$sheet->fromArray([
				$no,
				$trx->tipe,
				$trx->kode_transaksi,
				$trx->no_manual,
				$trx->tanggal,
				$trx->distribution_date,
				$trx->user,
				$trx->nama_gudang,
				$trx->status_verification == 1 ? 'Accept' : ($trx->status_verification == 2 ? 'Reject' : 'Pending')
			], null, "A{$row}");
			$sheet->getStyle("A{$row}:I{$row}")->applyFromArray($styleBorder);
			$row++;

			// Detail produk
			if (!empty($trx->details)) {
				$sheet->mergeCells("A{$row}:I{$row}")->setCellValue("A{$row}", "Detail Transaksi");
				$sheet->getStyle("A{$row}:I{$row}")->applyFromArray($styleDetailHeader);
				$row++;

				$sheet->setCellValue("A{$row}", 'SKU');
				$sheet->mergeCells("A{$row}:B{$row}");

				$sheet->setCellValue("C{$row}", 'Nama Produk');
				$sheet->mergeCells("C{$row}:E{$row}");

				$sheet->setCellValue("F{$row}", 'Jumlah');
				$sheet->mergeCells("F{$row}:G{$row}");

				$sheet->setCellValue("H{$row}", 'Keterangan');
				$sheet->mergeCells("H{$row}:I{$row}");

				$sheet->getStyle("A{$row}:I{$row}")->applyFromArray($styleDetailHeader);
				$row++;

				foreach ($trx->details as $detail) {
					$sheet->setCellValue("A{$row}", $detail->sku);
					$sheet->mergeCells("A{$row}:B{$row}");

					$sheet->setCellValue("C{$row}", $detail->nama_produk);
					$sheet->mergeCells("C{$row}:E{$row}");

					$sheet->setCellValue("F{$row}", $detail->jumlah);
					$sheet->mergeCells("F{$row}:G{$row}");

					$sheet->setCellValue("H{$row}", $detail->keterangan);
					$sheet->mergeCells("H{$row}:I{$row}");

					$sheet->getStyle("A{$row}:I{$row}")->applyFromArray($styleBorder);
					$row++;
				}
			}

			$no++;
		}

		// Set lebar kolom manual (hindari setAutoSize agar lebih cepat)
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(10);
		$sheet->getColumnDimension('C')->setWidth(25);
		$sheet->getColumnDimension('D')->setWidth(15);
		$sheet->getColumnDimension('E')->setWidth(15);
		$sheet->getColumnDimension('F')->setWidth(15);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(20);
		$sheet->getColumnDimension('I')->setWidth(15);

		$filename = 'Verifikasi_Transaksi_' . date('Y-m-d_H-i-s') . '.xlsx';
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"{$filename}\"");
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		exit;
	}
}
