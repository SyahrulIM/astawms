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
		// Query terpisah untuk menghindari masalah collation
		$instock = $this->db
			->select("'INSTOCK' AS tipe, i.instock_code AS kode_transaksi, i.no_manual, i.tgl_terima AS tanggal, 
					i.jam_terima AS jam, i.distribution_date, i.kategori, i.user, g.nama_gudang, i.status_verification")
			->from('instock i')
			->join('gudang g', 'g.idgudang = i.idgudang', 'left')
			->get()
			->result();

		$outstock = $this->db
			->select("'OUTSTOCK' AS tipe, o.outstock_code AS kode_transaksi, o.no_manual, o.tgl_keluar AS tanggal, 
					o.jam_keluar AS jam, o.distribution_date, o.kategori, o.user, g.nama_gudang, o.status_verification")
			->from('outstock o')
			->join('gudang g', 'g.idgudang = o.idgudang', 'left')
			->get()
			->result();

		$packing_list = $this->db
			->select("'Packing List' AS tipe, a.number_po AS kode_transaksi, a.no_manual, a.order_date AS tanggal, 
					a.order_time AS jam, a.distribution_date, a.kategori, a.user, g.nama_gudang, a.status_verification")
			->from('analisys_po a')
			->join('gudang g', 'g.idgudang = a.idgudang', 'left')
			->get()
			->result();

		// Gabungkan semua hasil
		$all_transactions = array_merge($instock, $outstock, $packing_list);

		// Urutkan berdasarkan tanggal dan jam (terbaru ke terlama)
		usort($all_transactions, function($a, $b) {
			$dateA = strtotime($a->tanggal . ' ' . $a->jam);
			$dateB = strtotime($b->tanggal . ' ' . $b->jam);
			return $dateB - $dateA; // Descending
		});

		$data['transactions'] = $all_transactions;
		$data['warehouse'] = $this->db->get('gudang')->result();
		$data['title'] = 'Verification';

		$this->load->view('theme/v_head', $data);
		$this->load->view('Verification/v_verification', $data);
	}

	public function confirm_stock($type, $code)
	{
		// Decode URL parameter
		$type = urldecode($type);
		$original_type = $type;
		$type = strtolower($type);
		
		// Normalisasi tipe
		if ($type == 'packing list') {
			$type = 'packing_list';
		}
		
		error_log("Confirm Stock - Original Type: $original_type, Normalized: $type, Code: $code");
		
		// Validasi tipe
		if (!in_array($type, ['instock', 'outstock', 'packing_list'])) {
			$this->session->set_flashdata('error', 'Tipe transaksi tidak valid: ' . $original_type);
			redirect('verification');
			return;
		}

		if ($type == 'instock' || $type == 'outstock') {
			// KODE UNTUK INSTOCK DAN OUTSTOCK
			$main_table = $type;
			$kode_field = $type . '_code';

			$trx = $this->db->where($kode_field, $code)->get($main_table)->row();
			if (!$trx) {
				$this->session->set_flashdata('error', ucfirst($type) . ' tidak ditemukan.');
				redirect('verification');
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

			// Mulai transaction untuk menjaga konsistensi
			$this->db->trans_start();

			try {
				// Update status verifikasi
				$this->db->set('status_verification', 1)
					->where($kode_field, $code)
					->update($main_table);

				$idgudang = $trx->idgudang;
				$detail_table = 'detail_' . $type;
				$details = $this->db->where($kode_field, $code)->get($detail_table)->result();

				foreach ($details as $detail) {
					$sku = $detail->sku;
					$jumlah = (int)$detail->jumlah;

					// Skip jika jumlah 0
					if ($jumlah <= 0) continue;

					$product = $this->db->where('sku', $sku)->get('product')->row();
					if (!$product) continue;

					$idproduct = $product->idproduct;

					$stock = $this->db->where('idproduct', $idproduct)
						->where('idgudang', $idgudang)
						->get('product_stock')
						->row();

					if (!$stock) {
						$initial_stock = $type === 'instock' ? $jumlah : -$jumlah;
						$this->db->insert('product_stock', [
							'idproduct' => $idproduct,
							'idgudang' => $idgudang,
							'stok' => $initial_stock
						]);
					} else {
						if ($type === 'instock') {
							$this->db->set('stok', "stok + {$jumlah}", false);
						} else {
							$this->db->set('stok', "stok - {$jumlah}", false);
						}

						$this->db->where('idproduct', $idproduct)
							->where('idgudang', $idgudang)
							->update('product_stock');
					}
				}

				$this->db->trans_complete();
				
				if ($this->db->trans_status() === FALSE) {
					throw new Exception('Gagal memperbarui database.');
				}

				$this->session->set_flashdata('success', 'Stok berhasil diverifikasi dan diperbarui.');
				
			} catch (Exception $e) {
				$this->db->trans_rollback();
				$this->session->set_flashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
			}
			
		} elseif ($type == 'packing_list') {
			// KODE UNTUK PACKING LIST
			$main_table = 'analisys_po';
			$kode_field = 'number_po';

			$trx = $this->db->where($kode_field, $code)->get($main_table)->row();
			if (!$trx) {
				$this->session->set_flashdata('error', 'Packing List tidak ditemukan.');
				redirect('verification');
				return;
			}

			// Ambil data dari POST
			$nomor_accurate = $this->input->post('nomor_accurate');
			$idgudang = $this->input->post('idgudang');
			$tanggal_diterima = $this->input->post('tanggal_diterima');

			// Validasi input dari modal
			if (!$nomor_accurate || !$idgudang || !$tanggal_diterima) {
				$this->session->set_flashdata('error', 'Harap isi semua field: Nomor Accurate, Gudang, dan Tanggal Diterima.');
				redirect('verification');
				return;
			}

			// Cek status verifikasi
			if ($trx->status_verification != 0) {
				$this->session->set_flashdata('error', 'Packing List sudah diverifikasi sebelumnya.');
				redirect('verification');
				return;
			}

			// Mulai transaction
			$this->db->trans_start();

			try {
				// Update analisys_po dengan data baru
				$update_data = [
					'status_verification' => 1,
					'no_manual' => $nomor_accurate,
					'idgudang' => $idgudang,
					'distribution_date' => $tanggal_diterima,
					'updated_by' => $this->session->userdata('username'),
					'updated_date' => date('Y-m-d H:i:s')
				];

				$this->db->where($kode_field, $code)->update($main_table, $update_data);

				// Ambil detail analisys_po
				$details = $this->db
					->select('d.*, p.sku')
					->from('detail_analisys_po d')
					->join('product p', 'd.idproduct = p.idproduct', 'left')
					->where('d.idanalisys_po', $trx->idanalisys_po)
					->where('(d.qty_order > 0 OR d.qty_receive > 0)', null, false)
					->get()
					->result();

				// Update stok berdasarkan qty_receive (jika ada) atau qty_order
				foreach ($details as $detail) {
					if (!$detail->idproduct) continue;
					
					$jumlah = (int) ($detail->qty_receive > 0 ? $detail->qty_receive : $detail->qty_order);
					
					if ($jumlah <= 0) continue;

					$stock = $this->db->where('idproduct', $detail->idproduct)
						->where('idgudang', $idgudang)
						->get('product_stock')
						->row();

					if (!$stock) {
						$this->db->insert('product_stock', [
							'idproduct' => $detail->idproduct,
							'idgudang' => $idgudang,
							'stok' => $jumlah
						]);
					} else {
						$this->db->set('stok', "stok + {$jumlah}", false)
							->where('idproduct', $detail->idproduct)
							->where('idgudang', $idgudang)
							->update('product_stock');
					}
				}

				$this->db->trans_complete();
				
				if ($this->db->trans_status() === FALSE) {
					throw new Exception('Gagal memperbarui database.');
				}

				$this->session->set_flashdata('success', 'Packing List berhasil diverifikasi dan stok diperbarui.');
				
			} catch (Exception $e) {
				$this->db->trans_rollback();
				$this->session->set_flashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
			}
		}

		redirect('verification');
	}

public function get_details($type, $kode)
{
    $type = strtolower($type);
    
    // Normalisasi tipe
    if ($type == 'packing list') {
        $type = 'packing_list';
    }
    
    // Validasi tipe
    $valid_types = ['instock', 'outstock', 'packing_list'];
    if (!in_array($type, $valid_types)) {
        echo json_encode(['success' => false, 'error' => 'Tipe tidak valid: ' . $type]);
        return;
    }

    try {
        if ($type == 'instock' || $type == 'outstock') {
            // KODE UNTUK INSTOCK DAN OUTSTOCK
            $main_table = $type;
            $kode_field = $type . '_code';
            $detail_table = 'detail_' . $type;

            // Cek apakah transaksi utama ada
            $main_data = $this->db->where($kode_field, $kode)->get($main_table)->row();
            if (!$main_data) {
                echo json_encode(['success' => false, 'error' => 'Transaksi tidak ditemukan']);
                return;
            }

            // Ambil detail dengan JOIN product
            $details = $this->db
                ->select("$detail_table.*, p.sku, p.nama_produk")
                ->from($detail_table)
                ->join('product p', "$detail_table.sku = p.sku", 'left')
                ->where("$detail_table.$kode_field", $kode)
                ->where("$detail_table.jumlah >", 0) // Hanya ambil yang jumlah > 0
                ->get()
                ->result();

            // Format data
            $formatted_details = [];
            foreach ($details as $detail) {
                $formatted_details[] = [
                    'sku' => $detail->sku ?: 'N/A',
                    'nama_produk' => $detail->nama_produk ?: 'N/A',
                    'jumlah' => (int)$detail->jumlah,
                    'qty_order' => (int)$detail->jumlah, // Untuk instock/outstock, jumlah = qty_order
                    'qty_receive' => (int)$detail->jumlah // Untuk instock/outstock, jumlah = qty_receive
                ];
            }

            if (!empty($formatted_details)) {
                echo json_encode(['success' => true, 'details' => $formatted_details]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Tidak ada data detail dengan quantity > 0']);
            }
            
        } elseif ($type == 'packing_list') {
            // KODE UNTUK PACKING LIST
            $main_data = $this->db->where('number_po', $kode)->get('analisys_po')->row();

            if (!$main_data) {
                echo json_encode(['success' => false, 'error' => 'Data tidak ditemukan untuk kode: ' . $kode]);
                return;
            }

            // Debug: Cek apakah ada data
            error_log("Found analisys_po ID: " . $main_data->idanalisys_po);

            // Ambil detail dari detail_analisys_po
            $details = $this->db
                ->select("d.*, p.sku, p.nama_produk, p.idproduct")
                ->from('detail_analisys_po d')
                ->join('product p', 'd.idproduct = p.idproduct', 'left')
                ->where('d.idanalisys_po', $main_data->idanalisys_po)
                ->where('(d.qty_order > 0 OR d.qty_receive > 0)', null, false)
                ->get()
                ->result();

            // Debug: Cek jumlah detail
            error_log("Found " . count($details) . " details");

            // Format data
            $formatted_details = [];
            foreach ($details as $detail) {
                // Tentukan jumlah mana yang akan ditampilkan
                $jumlah = 0;
                if ($detail->qty_order > 0) {
                    $jumlah = $detail->qty_order;
                } elseif ($detail->qty_receive > 0) {
                    $jumlah = $detail->qty_receive;
                }

                // Hanya tambahkan jika jumlah > 0
                if ($jumlah > 0) {
                    $formatted_details[] = [
                        'sku' => $detail->sku ?: 'N/A',
                        'nama_produk' => $detail->nama_produk ?: ($detail->product_name_en ?: 'N/A'),
                        'idproduct' => $detail->idproduct,
                        'jumlah' => $jumlah,
                        'qty_order' => (int)$detail->qty_order,
                        'qty_receive' => (int)$detail->qty_receive,
                        'price' => (int)$detail->price
                    ];
                }
            }

            if (!empty($formatted_details)) {
                echo json_encode(['success' => true, 'details' => $formatted_details]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Tidak ada data detail dengan quantity > 0']);
            }
        }
        
    } catch (Exception $e) {
        error_log("Error in get_details: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}

    public function reject($type, $code)
    {
        // Decode URL parameter
        $type = urldecode($type);
        $original_type = $type;
        $type = strtolower($type);
        
        // Normalisasi tipe
        if ($type == 'packing list') {
            $type = 'packing_list';
        }
        
        error_log("Reject - Original Type: $original_type, Normalized: $type, Code: $code");
        
        // Validasi tipe
        if (!in_array($type, ['instock', 'outstock', 'packing_list'])) {
            $this->session->set_flashdata('error', 'Tipe transaksi tidak valid: ' . $original_type);
            redirect('verification');
            return;
        }

        if ($type == 'instock' || $type == 'outstock') {
            // KODE ASLI UNTUK INSTOCK DAN OUTSTOCK
            $main_table = $type;
            $kode_field = $type . '_code';

            $trx = $this->db->where($kode_field, $code)->get($main_table)->row();
            if (!$trx) {
                $this->session->set_flashdata('error', 'Transaksi tidak ditemukan.');
                redirect('verification');
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
            
        } elseif ($type == 'packing_list') {
            // KODE UNTUK PACKING LIST
            $main_table = 'analisys_po';
            $kode_field = 'number_po';

            $trx = $this->db->where($kode_field, $code)->get($main_table)->row();
            if (!$trx) {
                $this->session->set_flashdata('error', 'Packing List tidak ditemukan.');
                redirect('verification');
                return;
            }

            // Cek status verifikasi
            if ($trx->status_verification != 0) {
                $this->session->set_flashdata('error', 'Packing List sudah diproses sebelumnya.');
                redirect('verification');
                return;
            }

            $this->db->set('status_verification', 2)
                ->set('updated_by', $this->session->userdata('username'))
                ->set('updated_date', date('Y-m-d H:i:s'))
                ->where($kode_field, $code)
                ->update($main_table);

            $this->session->set_flashdata('error', 'Packing List berhasil ditolak.');
        }

        redirect('verification');
    }

    public function exportExcel()
    {
        // Kode exportExcel tetap sama seperti aslinya
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