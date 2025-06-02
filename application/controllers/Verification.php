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
}
