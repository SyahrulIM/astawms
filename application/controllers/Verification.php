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
				i.tgl_terima AS tanggal,
				i.jam_terima AS jam,
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
				o.tgl_keluar AS tanggal,
				o.jam_keluar AS jam,
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
		$detail_table = 'detail_' . $type;
		$kode_field = $type . '_code';

		$this->db->set('status_verification', 1)
			->where($kode_field, $code)
			->update($main_table);

		$trx = $this->db->where($kode_field, $code)->get($main_table)->row();
		if (!$trx) {
			show_error(ucfirst($type) . ' tidak ditemukan.');
			return;
		}

		$idgudang = $trx->idgudang;

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
		if ($type == 'INSTOCK') {
			// Fetch the main transaction based on kode_transaksi
			$trx = $this->db->get_where('instock', ['instock_code' => $kode])->row();
	
			// If transaction is found
			if ($trx) {
				// Fetch the details of the transaction from the 'detail_instock' table
				$details = $this->db->get_where('detail_instock', ['instock_code' => $kode])->result();
	
				// If details are found, return them as JSON
				if ($details) {
					echo json_encode(['details' => $details]);
				} else {
					echo json_encode(['error' => 'Transaction details not found']);
				}
			} else {
				echo json_encode(['error' => 'Transaction not found']);
			}
		} else {
			echo json_encode(['error' => 'Invalid transaction type']);
		}
	}	
}
