<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Verification extends CI_Controller
{
	public function index()
	{
		$query = "
			SELECT 
				'INSTOCK' AS tipe,
				i.instock_code AS kode_transaksi,
				i.no_manual AS no_manual,
				i.tgl_terima AS tanggal,
				i.jam_terima AS jam,
				i.distribution_date as distribution_date,
				i.kategori,
				i.user,
				g.nama_gudang,
				i.status_verification
			FROM instock i
			LEFT JOIN gudang g ON g.idgudang = i.idgudang
	
			UNION ALL
	
			SELECT 
				'OUTSTOCK' AS tipe,
				o.outstock_code AS kode_transaksi,
				o.no_manual AS no_manual,
				o.tgl_keluar AS tanggal,
				o.jam_keluar AS jam,
				o.distribution_date as distribution_date,
				o.kategori,
				o.user,
				g.nama_gudang,
				o.status_verification
			FROM outstock o
			LEFT JOIN gudang g ON g.idgudang = o.idgudang
	
			ORDER BY tanggal DESC, jam DESC
		";

		$data['transactions'] = $this->db->query($query)->result();
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
			$jumlah = (int)$detail->jumlah;

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
		if (!in_array($type, ['instock', 'outstock'])) {
			echo json_encode(['error' => 'Invalid type']);
			return;
		}

		$kode_field = $type . '_code';
		$detail_table = 'detail_' . $type;

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
		$this->load->helper('download');

		$start_date = $this->input->post('filterInputStart');
		$end_date = $this->input->post('filterInputEnd');

		$whereIn = "";
		$whereOut = "";

		// Filter berdasarkan tanggal
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

		// Gabungkan transaksi instock dan outstock
		$query = "
	SELECT 
		'INSTOCK' AS tipe,
		i.instock_code AS kode_transaksi,
		i.no_manual AS no_manual,
		i.tgl_terima AS tanggal,
		i.jam_terima AS jam,
		i.distribution_date AS distribution_date,
		i.kategori,
		i.user,
		g.nama_gudang,
		i.status_verification
	FROM instock i
	LEFT JOIN gudang g ON g.idgudang = i.idgudang
	$whereIn

	UNION ALL

	SELECT 
		'OUTSTOCK' AS tipe,
		o.outstock_code AS kode_transaksi,
		o.no_manual AS no_manual,
		o.tgl_keluar AS tanggal,
		o.jam_keluar AS jam,
		o.distribution_date AS distribution_date,
		o.kategori,
		o.user,
		g.nama_gudang,
		o.status_verification
	FROM outstock o
	LEFT JOIN gudang g ON g.idgudang = o.idgudang
	$whereOut

	ORDER BY tanggal DESC, jam DESC
	";

		$transactions = $this->db->query($query)->result();

		// Ambil detail untuk masing-masing transaksi
		foreach ($transactions as &$trx) {
			if ($trx->tipe === 'INSTOCK') {
				$details = $this->db->get_where('detail_instock', [
					'instock_code' => $trx->kode_transaksi
				])->result();
			} else {
				$details = $this->db->get_where('detail_outstock', [
					'outstock_code' => $trx->kode_transaksi
				])->result();
			}
			$trx->details = $details;
		}

		// Generate HTML Excel
		$filename = 'Verifikasi Transaksi_' . date('Y-m-d_H-i-s') . '.xls';
		$content = "<table>";
		$content .= "<tr><td colspan='9' style='font-weight:bold; text-align:center;'>Asta Homeware</td></tr>";
		$content .= "<tr><td colspan='9' style='font-weight:bold; text-align:center;'>Data Verifikasi Transaksi</td></tr>";
		$content .= "<tr><td colspan='9'>Periode: " . ($start_date ?? '-') . " s/d " . ($end_date ?? '-') . "</td></tr>";
		$content .= "<tr><td colspan='9'>&nbsp;</td></tr>";
		$content .= "</table>";

		$content .= "<table border='1'>";
		$no = 1;
		foreach ($transactions as $trx) {
			$content .= "<thead>
	<tr style='background:#f0f0f0; font-weight:bold;'>
		<th>No</th>
		<th>Tipe</th>
		<th>Kode Transaksi</th>
		<th>Nomer</th>
		<th>Tanggal Input</th>
		<th>Tanggal Distribusi</th>
		<th>User</th>
		<th>Gudang</th>
		<th>Status</th>
	</tr>
	</thead><tbody>";

			$content .= "<tr>
		<td>{$no}</td>
		<td>{$trx->tipe}</td>
		<td>{$trx->kode_transaksi}</td>
		<td>{$trx->no_manual}</td>
		<td>{$trx->tanggal}</td>
		<td>{$trx->distribution_date}</td>
		<td>{$trx->user}</td>
		<td>{$trx->nama_gudang}</td>
		<td>" . ($trx->status_verification == 1 ? 'Accept' : ($trx->status_verification == 2 ? 'Reject' : 'Pending')) . "</td>
	</tr>";

			// Detail transaksi per baris
			if (!empty($trx->details)) {
				$content .= "<tr>
			<td colspan='9'>
				<table border='1' width='100%'>
					<tr style='background:#d9edf7; font-weight:bold;'>
						<th colspan='2'>SKU</th>
						<th colspan='4'>Nama Produk</th>
						<th colspan='2'>Jumlah</th>
						<th>Keterangan</th>
					</tr>";
				foreach ($trx->details as $detail) {
					$content .= "<tr>
				<td colspan='2'>{$detail->sku}</td>
				<td colspan='4'>{$detail->nama_produk}</td>
				<td colspan='2'>{$detail->jumlah}</td>
				<td>{$detail->keterangan}</td>
			</tr>";
				}
				$content .= "</table>
			</td>
		</tr>";
			}

			$no++;
		}

		$content .= "</tbody></table>";

		// Output Excel
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename={$filename}");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $content;
		exit;
	}
}
