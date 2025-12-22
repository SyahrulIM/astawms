<?php

$servername = "103.163.138.82";
$username = "astahome_it";
$password = "astawms=d17d09";
$dbname = "astahome_wms";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$token = 'ZsZ2Dp71dyKrgz3YAQKg';
$targets = '6281331090331-1528429522@g.us';
// $targets = '6285156340619';

// ===============================================
// 1. DATA ASTA WMS - TANPA FILTER PERIODE
// ===============================================

$sql_pending_verification = "
    SELECT 
        COUNT(*) as total_pending,
        tipe
    FROM (
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
        WHERE i.status_verification = 0
        
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
        WHERE o.status_verification = 0
    ) as combined
    GROUP BY tipe
";

$result_pending = $conn->query($sql_pending_verification);
$total_instock = 0;
$total_outstock = 0;

if ($result_pending) {
    while ($row = $result_pending->fetch_assoc()) {
        if ($row['tipe'] == 'INSTOCK') {
            $total_instock = $row['total_pending'];
        } else {
            $total_outstock = $row['total_pending'];
        }
    }
}

// 2. Hitung pending verification delivery note (kategori 1) - semua data
$sql_verification_delivery_note = "
    SELECT COUNT(DISTINCT dn.iddelivery_note) as total
    FROM delivery_note dn
    LEFT JOIN (
        SELECT t1.* 
        FROM delivery_note_log t1
        JOIN (
            SELECT iddelivery_note, MAX(created_date) as max_date
            FROM delivery_note_log
            GROUP BY iddelivery_note
        ) t2 ON t1.iddelivery_note = t2.iddelivery_note AND t1.created_date = t2.max_date
    ) dnl ON dnl.iddelivery_note = dn.iddelivery_note
    WHERE dn.status = 1
    AND dn.kategori = 1
    AND dnl.progress = 1
";

$result_verif_dn = $conn->query($sql_verification_delivery_note);
$total_verif_dn = $result_verif_dn->fetch_assoc()['total'];

// 3. Hitung pending validation delivery note (kategori 1) - semua data
$sql_validation_delivery_note = "
    SELECT COUNT(DISTINCT dn.iddelivery_note) as total
    FROM delivery_note dn
    LEFT JOIN (
        SELECT t1.* 
        FROM delivery_note_log t1
        JOIN (
            SELECT iddelivery_note, MAX(created_date) as max_date
            FROM delivery_note_log
            GROUP BY iddelivery_note
        ) t2 ON t1.iddelivery_note = t2.iddelivery_note AND t1.created_date = t2.max_date
    ) dnl ON dnl.iddelivery_note = dn.iddelivery_note
    WHERE dn.status = 1
    AND dn.kategori = 1
    AND dnl.progress = 2
";

$result_valid_dn = $conn->query($sql_validation_delivery_note);
$total_valid_dn = $result_valid_dn->fetch_assoc()['total'];

// 4. Hitung pending Final DIR delivery note (kategori 1) - semua data
$sql_final_delivery_note = "
    SELECT COUNT(DISTINCT dn.iddelivery_note) as total
    FROM delivery_note dn
    LEFT JOIN (
        SELECT t1.* 
        FROM delivery_note_log t1
        JOIN (
            SELECT iddelivery_note, MAX(created_date) as max_date
            FROM delivery_note_log
            GROUP BY iddelivery_note
        ) t2 ON t1.iddelivery_note = t2.iddelivery_note AND t1.created_date = t2.max_date
    ) dnl ON dnl.iddelivery_note = dn.iddelivery_note
    WHERE dn.status = 1
    AND dn.kategori = 1
    AND dnl.progress = 3
";

$result_final_dn = $conn->query($sql_final_delivery_note);
$total_final_dn = $result_final_dn->fetch_assoc()['total'];

// 5. Hitung pending verification delivery manual (kategori 2) - semua data
$sql_verification_delivery_manual = "
    SELECT COUNT(DISTINCT dn.iddelivery_note) as total
    FROM delivery_note dn
    LEFT JOIN (
        SELECT t1.* 
        FROM delivery_note_log t1
        JOIN (
            SELECT iddelivery_note, MAX(created_date) as max_date
            FROM delivery_note_log
            GROUP BY iddelivery_note
        ) t2 ON t1.iddelivery_note = t2.iddelivery_note AND t1.created_date = t2.max_date
    ) dnl ON dnl.iddelivery_note = dn.iddelivery_note
    WHERE dn.status = 1
    AND dn.kategori = 2
    AND dnl.progress = 1
";

$result_verif_dm = $conn->query($sql_verification_delivery_manual);
$total_verif_dm = $result_verif_dm->fetch_assoc()['total'];

// 6. Hitung pending validation delivery manual (kategori 2) - semua data
$sql_validation_delivery_manual = "
    SELECT COUNT(DISTINCT dn.iddelivery_note) as total
    FROM delivery_note dn
    LEFT JOIN (
        SELECT t1.* 
        FROM delivery_note_log t1
        JOIN (
            SELECT iddelivery_note, MAX(created_date) as max_date
            FROM delivery_note_log
            GROUP BY iddelivery_note
        ) t2 ON t1.iddelivery_note = t2.iddelivery_note AND t1.created_date = t2.max_date
    ) dnl ON dnl.iddelivery_note = dn.iddelivery_note
    WHERE dn.status = 1
    AND dn.kategori = 2
    AND dnl.progress = 2
";

$result_valid_dm = $conn->query($sql_validation_delivery_manual);
$total_valid_dm = $result_valid_dm->fetch_assoc()['total'];

// 7. Hitung pending Final DIR delivery manual (kategori 2) - semua data
$sql_final_delivery_manual = "
    SELECT COUNT(DISTINCT dn.iddelivery_note) as total
    FROM delivery_note dn
    LEFT JOIN (
        SELECT t1.* 
        FROM delivery_note_log t1
        JOIN (
            SELECT iddelivery_note, MAX(created_date) as max_date
            FROM delivery_note_log
            GROUP BY iddelivery_note
        ) t2 ON t1.iddelivery_note = t2.iddelivery_note AND t1.created_date = t2.max_date
    ) dnl ON dnl.iddelivery_note = dn.iddelivery_note
    WHERE dn.status = 1
    AND dn.kategori = 2
    AND dnl.progress = 3
";

$result_final_dm = $conn->query($sql_final_delivery_manual);
$total_final_dm = $result_final_dm->fetch_assoc()['total'];

// ===============================================
// 2. DATA ASTA ACOL DENGAN PERIODE KHUSUS
// ===============================================

// **PERIODE ACOL: tanggal 1 bulan sekarang sampai kemarin**
$current_month_start = date('Y-m-01');
$current_month_end = date('Y-m-d', strtotime('-1 day')); // Sampai kemarin

$ratio_limit = 30;

// 2.2 Query untuk data Acol dengan periode tertentu - SESUAI CONTROLLER
$sql_acol_detail = "
    SELECT
        asd.no_faktur,
        MAX(asd.order_date) AS shopee_order_date,
        MAX(asd.pay_date) AS shopee_pay_date,
        MAX(asd.total_faktur) AS shopee_total_faktur,
        MAX(asd.discount) AS shopee_discount,
        MAX(asd.payment) AS shopee_payment,
        MAX(asd.refund) AS shopee_refund,
        MAX(asd.note) AS note,
        MAX(asd.is_check) AS is_check,
        MAX(asd.status_dir) AS status_dir,
        MAX(aad.pay_date) AS accurate_pay_date,
        MAX(aad.total_faktur) AS accurate_total_faktur,
        MAX(aad.discount) AS accurate_discount,
        MAX(aad.payment) AS accurate_payment,
        'shopee' as source,
        MAX(s.is_kotime) as is_kotime
    FROM acc_shopee_detail asd
    INNER JOIN acc_shopee s ON s.idacc_shopee = asd.idacc_shopee
    LEFT JOIN (
        SELECT a1.*
        FROM acc_accurate_detail a1
        INNER JOIN (
            SELECT no_faktur, MAX(idacc_accurate_detail) AS max_id
            FROM acc_accurate_detail
            GROUP BY no_faktur
        ) latest ON a1.no_faktur = latest.no_faktur AND a1.idacc_accurate_detail = latest.max_id
    ) aad ON aad.no_faktur = asd.no_faktur
    WHERE asd.order_date BETWEEN '{$current_month_start}' AND '{$current_month_end}'
    AND aad.payment IS NOT NULL
    AND asd.total_faktur > 0
    AND asd.status = 1
    AND s.is_kotime = 0  -- Hanya ASTA, bukan Kotime
    GROUP BY asd.no_faktur

    UNION

    SELECT
        atd.no_faktur,
        MAX(atd.order_date) AS shopee_order_date,
        MAX(atd.pay_date) AS shopee_pay_date,
        MAX(atd.total_faktur) AS shopee_total_faktur,
        MAX(atd.discount) AS shopee_discount,
        MAX(atd.payment) AS shopee_payment,
        MAX(atd.refund) AS shopee_refund,
        MAX(atd.note) AS note,
        MAX(atd.is_check) AS is_check,
        MAX(atd.status_dir) AS status_dir,
        MAX(aad.pay_date) AS accurate_pay_date,
        MAX(aad.total_faktur) AS accurate_total_faktur,
        MAX(aad.discount) AS accurate_discount,
        MAX(aad.payment) AS accurate_payment,
        'tiktok' as source,
        MAX(t.is_kotime) as is_kotime
    FROM acc_tiktok_detail atd
    INNER JOIN acc_tiktok t ON t.idacc_tiktok = atd.idacc_tiktok
    LEFT JOIN (
        SELECT a1.*
        FROM acc_accurate_detail a1
        INNER JOIN (
            SELECT no_faktur, MAX(idacc_accurate_detail) AS max_id
            FROM acc_accurate_detail
            GROUP BY no_faktur
        ) latest ON a1.no_faktur = latest.no_faktur AND a1.idacc_accurate_detail = latest.max_id
    ) aad ON aad.no_faktur = atd.no_faktur
    WHERE atd.order_date BETWEEN '{$current_month_start}' AND '{$current_month_end}'
    AND aad.payment IS NOT NULL
    AND atd.total_faktur > 0
    AND atd.status = 1
    AND t.is_kotime = 0  -- Hanya ASTA, bukan Kotime
    GROUP BY atd.no_faktur

    UNION

    SELECT
        ald.no_faktur,
        MAX(ald.order_date) AS shopee_order_date,
        MAX(ald.pay_date) AS shopee_pay_date,
        MAX(ald.total_faktur) AS shopee_total_faktur,
        MAX(ald.discount) AS shopee_discount,
        MAX(ald.payment) AS shopee_payment,
        MAX(ald.refund) AS shopee_refund,
        MAX(ald.note) AS note,
        MAX(ald.is_check) AS is_check,
        MAX(ald.status_dir) AS status_dir,
        MAX(aad.pay_date) AS accurate_pay_date,
        MAX(aad.total_faktur) AS accurate_total_faktur,
        MAX(aad.discount) AS accurate_discount,
        MAX(aad.payment) AS accurate_payment,
        'lazada' as source,
        MAX(l.is_kotime) as is_kotime
    FROM acc_lazada_detail ald
    INNER JOIN acc_lazada l ON l.idacc_lazada = ald.idacc_lazada
    LEFT JOIN (
        SELECT a1.*
        FROM acc_accurate_detail a1
        INNER JOIN (
            SELECT no_faktur, MAX(idacc_accurate_detail) AS max_id
            FROM acc_accurate_detail
            GROUP BY no_faktur
        ) latest ON a1.no_faktur = latest.no_faktur AND a1.idacc_accurate_detail = latest.max_id
    ) aad ON aad.no_faktur = ald.no_faktur
    WHERE ald.order_date BETWEEN '{$current_month_start}' AND '{$current_month_end}'
    AND aad.payment IS NOT NULL
    AND ald.total_faktur > 0
    AND ald.status = 1
    AND l.is_kotime = 0  -- Hanya ASTA, bukan Kotime
    GROUP BY ald.no_faktur

    ORDER BY no_faktur ASC
";

$result_acol = $conn->query($sql_acol_detail);

// Inisialisasi data Acol
$acol_data = [
    'shopee' => [
        'total_faktur' => 0,
        'belum_note' => 0,
        'belum_check' => 0,
        'butuh_final_dir' => 0,
        'total_invoice' => 0,
        'total_diterima' => 0,
        'ratio_exceed' => 0,
        'total_selisih' => 0,
        'total_faktur_retur' => 0,
        'total_invoice_retur' => 0,
        'total_diterima_retur' => 0
    ],
    'tiktok' => [
        'total_faktur' => 0,
        'belum_note' => 0,
        'belum_check' => 0,
        'butuh_final_dir' => 0,
        'total_invoice' => 0,
        'total_diterima' => 0,
        'ratio_exceed' => 0,
        'total_selisih' => 0,
        'total_faktur_retur' => 0,
        'total_invoice_retur' => 0,
        'total_diterima_retur' => 0
    ],
    'lazada' => [
        'total_faktur' => 0,
        'belum_note' => 0,
        'belum_check' => 0,
        'butuh_final_dir' => 0,
        'total_invoice' => 0,
        'total_diterima' => 0,
        'ratio_exceed' => 0,
        'total_selisih' => 0,
        'total_faktur_retur' => 0,
        'total_invoice_retur' => 0,
        'total_diterima_retur' => 0
    ]
];

$critical_invoices = [];
$all_invoices = [];

if ($result_acol) {
    while ($row = $result_acol->fetch_assoc()) {
        $source = $row['source'];
        $is_retur = ($row['shopee_refund'] ?? 0) < 0;
        $shopee = (float) ($row['shopee_total_faktur'] ?? 0);
        $accurate = (float) ($row['accurate_payment'] ?? 0);

        // Hitung data dasar
        $acol_data[$source]['total_faktur']++;
        $acol_data[$source]['total_invoice'] += $shopee;
        $acol_data[$source]['total_diterima'] += $accurate;
        $acol_data[$source]['total_selisih'] += ($shopee - $accurate);

        // Hitung retur
        if ($is_retur) {
            $acol_data[$source]['total_faktur_retur']++;
            $acol_data[$source]['total_invoice_retur'] += $shopee;
            $acol_data[$source]['total_diterima_retur'] += $accurate;
        }

        // Cek apakah ratio melebihi 30%
        $ratio = ($accurate > 0 && $shopee > 0) ? (($shopee - $accurate) / $shopee) * 100 : 0;
        $is_ratio_exceed = $ratio > $ratio_limit && !$is_retur; // Hanya untuk non-retur

        if ($is_ratio_exceed) {
            $acol_data[$source]['ratio_exceed']++;

            // Kategori 1: Belum Note (ratio >30% DAN note empty)
            $is_note_empty = empty($row['note']);
            if ($is_note_empty) {
                $acol_data[$source]['belum_note']++;
                $category = 'Belum Note';
            }
            // Kategori 2: Belum Check (ratio >30% DAN note NOT empty DAN is_check = 0)
            elseif (!$is_note_empty && $row['is_check'] == 0) {
                $acol_data[$source]['belum_check']++;
                $category = 'Belum Check';
            }
            // Kategori 3: Butuh Final DIR (ratio >30% DAN note NOT empty DAN is_check = 1 DAN status_dir bukan 'Final DIR')
            elseif (
                !$is_note_empty && $row['is_check'] == 1 && ($row['status_dir'] === null || $row['status_dir'] != 'Final DIR')
            ) {
                $acol_data[$source]['butuh_final_dir']++;
                $category = 'Butuh Final DIR';
            } else {
                $category = 'Lainnya';
            }

            // Simpan invoice kritis untuk ditampilkan
            $critical_invoices[] = [
                'no_faktur' => $row['no_faktur'],
                'source' => ucfirst($source),
                'category' => $category,
                'ratio_diference' => $ratio,
                'note_status' => $is_note_empty ? 'Empty' : 'Filled',
                'check_status' => $row['is_check'] == 1 ? 'Checked' : 'Not Checked',
                'dir_status' => $row['status_dir'] ?? 'Empty'
            ];
        }

        $all_invoices[] = $row;
    }
}

// ===============================================
// 3. HITUNG ADDITIONAL REVENUE
// ===============================================

$additional_revenue = 0;

// Shopee ASTA (non-kotime)
$sql_additional_shopee = "
    SELECT SUM(additional_revenue) as total
    FROM acc_shopee_additional 
    WHERE start_date >= '{$current_month_start}' 
    AND end_date <= '{$current_month_end}'
    AND is_kotime = 0
";
$result_shopee_add = $conn->query($sql_additional_shopee);
if ($result_shopee_add) {
    $row = $result_shopee_add->fetch_assoc();
    $additional_revenue += $row['total'] ?? 0;
}

// TikTok ASTA (non-kotime)
$sql_additional_tiktok = "
    SELECT SUM(additional_revenue) as total
    FROM acc_tiktok_additional 
    WHERE start_date >= '{$current_month_start}' 
    AND end_date <= '{$current_month_end}'
    AND is_kotime = 0
";
$result_tiktok_add = $conn->query($sql_additional_tiktok);
if ($result_tiktok_add) {
    $row = $result_tiktok_add->fetch_assoc();
    $additional_revenue += $row['total'] ?? 0;
}

// Lazada ASTA (non-kotime)
$sql_additional_lazada = "
    SELECT SUM(additional_revenue) as total
    FROM acc_lazada_additional 
    WHERE start_date >= '{$current_month_start}' 
    AND end_date <= '{$current_month_end}'
    AND is_kotime = 0
";
$result_lazada_add = $conn->query($sql_additional_lazada);
if ($result_lazada_add) {
    $row = $result_lazada_add->fetch_assoc();
    $additional_revenue += $row['total'] ?? 0;
}

// ===============================================
// 4. FORMAT PESAN WHATSAPP
// ===============================================

$current_date = date('d F Y');
$message = "📊 *ASTA HOMEWARE - DAILY REPORT*\n";
$message .= "*Tanggal: " . $current_date . "*\n\n";

// 4.1 Asta WMS Section - TANPA PERIODE (semua data)
$message .= "══════ Asta WMS ═════\n";
$message .= "*(Semua data tanpa filter periode)*\n\n";

$message .= "📦 *VERIFIKASI INSTOCK/OUTSTOCK:*\n";
$message .= "• Instock Pending: " . $total_instock . "\n";
$message .= "• Outstock Pending: " . $total_outstock . "\n";
$message .= "• Total: " . ($total_instock + $total_outstock) . "\n\n";

$message .= "🚚 *DELIVERY NOTE:*\n";
$message .= "• Verifikasi Pending: " . $total_verif_dn . "\n";
$message .= "• Validasi Pending: " . $total_valid_dn . "\n";
$message .= "• Final DIR Pending: " . $total_final_dn . "\n";
$message .= "• Total: " . ($total_verif_dn + $total_valid_dn + $total_final_dn) . "\n\n";

$message .= "📝 *DELIVERY MANUAL:*\n";
$message .= "• Verifikasi Pending: " . $total_verif_dm . "\n";
$message .= "• Validasi Pending: " . $total_valid_dm . "\n";
$message .= "• Final DIR Pending: " . $total_final_dm . "\n";
$message .= "• Total: " . ($total_verif_dm + $total_valid_dm + $total_final_dm) . "\n\n";

$message .= "📈 *GRAND TOTAL PENDING WMS:*\n";
$grand_total_wms = ($total_instock + $total_outstock) + ($total_verif_dn + $total_valid_dn + $total_final_dn) + ($total_verif_dm + $total_valid_dm + $total_final_dm);
$message .= "• *" . $grand_total_wms . " transaksi* membutuhkan tindakan\n\n";

// 4.2 Asta Acol Section - DENGAN PERIODE KHUSUS
$message .= "══════ Asta Acol ═════\n";
$message .= "*Periode: " . date('d M Y', strtotime($current_month_start)) . " - " . date('d M Y', strtotime($current_month_end)) . "*\n";
$message .= "*Bulan: " . date('F Y', strtotime($current_month_start)) . "*\n";
$message .= "*Hanya faktur ASTA (non-Kotime)*\n";
$message .= "*Ratio Limit: " . $ratio_limit . "%*\n";
$message .= "*Hanya faktur dengan ratio >{$ratio_limit}% yang dihitung (non-retur)*\n\n";

$message .= "📋 *KATEGORI BERTINGKAT:*\n";
$message .= "1. Belum Note: Ratio >{$ratio_limit}% & note kosong\n";
$message .= "2. Belum Check: Ratio >{$ratio_limit}% & note terisi & belum check\n";
$message .= "3. Butuh Final DIR: Ratio >{$ratio_limit}% & note terisi & sudah check & status dir bukan 'Final DIR'\n\n";

$total_faktur_all = 0;
$total_ratio_exceed_all = 0;
$total_belum_note_all = 0;
$total_belum_check_all = 0;
$total_butuh_final_dir_all = 0;
$total_invoice_all = 0;
$total_diterima_all = 0;
$total_faktur_retur_all = 0;
$total_invoice_retur_all = 0;
$total_diterima_retur_all = 0;

foreach (['shopee', 'tiktok', 'lazada'] as $mp) {
    $data = $acol_data[$mp];
    if ($data['total_faktur'] > 0) {
        // Hitung non-retur
        $total_faktur_non_retur = $data['total_faktur'] - $data['total_faktur_retur'];
        $total_invoice_non_retur = $data['total_invoice'] - $data['total_invoice_retur'];
        $total_diterima_non_retur = $data['total_diterima'] - $data['total_diterima_retur'];
        $total_selisih_non_retur = $total_invoice_non_retur - $total_diterima_non_retur;

        $ratio = $total_invoice_non_retur > 0 ? ($total_selisih_non_retur / $total_invoice_non_retur * 100) : 0;

        $message .= "🛒 *" . strtoupper($mp) . "*\n";
        $message .= "• Total Faktur: " . number_format($data['total_faktur']) . "\n";
        $message .= "  - Non-Retur: " . number_format($total_faktur_non_retur) . "\n";
        $message .= "  - Retur: " . number_format($data['total_faktur_retur']) . "\n";

        $message .= "• Ratio >{$ratio_limit}%: " . number_format($data['ratio_exceed']) .
            " (" . number_format(($data['ratio_exceed'] / $total_faktur_non_retur * 100), 1) . "% dari non-retur)\n";
        $message .= "• Belum Note: " . number_format($data['belum_note']) . "\n";
        $message .= "• Belum Check: " . number_format($data['belum_check']) . "\n";
        $message .= "• Butuh Final DIR: " . number_format($data['butuh_final_dir']) . "\n";

        $message .= "• Total Invoice: Rp " . number_format($total_invoice_non_retur) . "\n";
        $message .= "• Total Diterima: Rp " . number_format($total_diterima_non_retur) . "\n";
        $message .= "• Selisih: Rp " . number_format($total_selisih_non_retur) . "\n";
        $message .= "• Ratio: " . number_format($ratio, 2) . "%\n\n";

        $total_faktur_all += $data['total_faktur'];
        $total_ratio_exceed_all += $data['ratio_exceed'];
        $total_belum_note_all += $data['belum_note'];
        $total_belum_check_all += $data['belum_check'];
        $total_butuh_final_dir_all += $data['butuh_final_dir'];
        $total_invoice_all += $total_invoice_non_retur;
        $total_diterima_all += $total_diterima_non_retur;
        $total_faktur_retur_all += $data['total_faktur_retur'];
        $total_invoice_retur_all += $data['total_invoice_retur'];
        $total_diterima_retur_all += $data['total_diterima_retur'];
    }
}

if ($total_faktur_all > 0) {
    $total_faktur_non_retur_all = $total_faktur_all - $total_faktur_retur_all;
    $selisih_all = $total_invoice_all - $total_diterima_all;
    $ratio_all = $total_invoice_all > 0 ? ($selisih_all / $total_invoice_all) * 100 : 0;

    $message .= "📊 *TOTAL SEMUA MARKETPLACE:*\n";
    $message .= "• Total Faktur: " . number_format($total_faktur_all) . "\n";
    $message .= "  - Non-Retur: " . number_format($total_faktur_non_retur_all) . "\n";
    $message .= "  - Retur: " . number_format($total_faktur_retur_all) . "\n";

    $message .= "• Ratio >{$ratio_limit}%: " . number_format($total_ratio_exceed_all) .
        " (" . number_format(($total_ratio_exceed_all / $total_faktur_non_retur_all * 100), 1) . "%)\n";

    if ($total_ratio_exceed_all > 0) {
        $message .= "• Belum Note: " . number_format($total_belum_note_all) .
            " (" . number_format(($total_belum_note_all / $total_ratio_exceed_all * 100), 1) . "% dari ratio >{$ratio_limit}%)\n";
        $message .= "• Belum Check: " . number_format($total_belum_check_all) .
            " (" . number_format(($total_belum_check_all / $total_ratio_exceed_all * 100), 1) . "% dari ratio >{$ratio_limit}%)\n";
        $message .= "• Butuh Final DIR: " . number_format($total_butuh_final_dir_all) .
            " (" . number_format(($total_butuh_final_dir_all / $total_ratio_exceed_all * 100), 1) . "% dari ratio >{$ratio_limit}%)\n";
    } else {
        $message .= "• Belum Note: " . number_format($total_belum_note_all) . "\n";
        $message .= "• Belum Check: " . number_format($total_belum_check_all) . "\n";
        $message .= "• Butuh Final DIR: " . number_format($total_butuh_final_dir_all) . "\n";
    }

    $message .= "• Total Invoice: Rp " . number_format($total_invoice_all) . "\n";
    $message .= "• Total Diterima: Rp " . number_format($total_diterima_all) . "\n";
    $message .= "• Selisih: Rp " . number_format($selisih_all) . "\n";
    $message .= "• Ratio: " . number_format($ratio_all, 2) . "%\n";
    $message .= "• Additional Revenue: Rp " . number_format($additional_revenue) . "\n\n";
} else {
    $message .= "ℹ️ *Tidak ada data faktur untuk periode ini*\n\n";
}

// 4.3 Critical Invoices Section
if (!empty($critical_invoices)) {
    // Kelompokkan berdasarkan kategori
    $grouped_by_category = [];
    foreach ($critical_invoices as $inv) {
        $category = $inv['category'];
        if (!isset($grouped_by_category[$category])) {
            $grouped_by_category[$category] = [];
        }
        $grouped_by_category[$category][] = $inv;
    }

    // Urutkan kategori berdasarkan urutan prioritas
    $category_order = ['Belum Note', 'Belum Check', 'Butuh Final DIR'];

    $message .= "⚠️ *INVOICE YANG PERLU PERHATIAN:*\n";
    $message .= "Total: " . count($critical_invoices) . " invoice (ratio >{$ratio_limit}%)\n\n";

    foreach ($category_order as $category) {
        if (isset($grouped_by_category[$category]) && !empty($grouped_by_category[$category])) {
            $invoices = $grouped_by_category[$category];

            // Urutkan berdasarkan ratio tertinggi
            usort($invoices, function ($a, $b) {
                return $b['ratio_diference'] <=> $a['ratio_diference'];
            });

            $message .= "*" . $category . " (" . count($invoices) . "):*\n";

            $display_count = min(2, count($invoices));
            for ($i = 0; $i < $display_count; $i++) {
                $inv = $invoices[$i];
                $message .= ($i + 1) . ". " . $inv['no_faktur'] .
                    " (Ratio: " . number_format($inv['ratio_diference'], 2) . "%)" .
                    " [" . $inv['source'] . "]\n";
            }

            if (count($invoices) > $display_count) {
                $message .= "...dan " . (count($invoices) - $display_count) . " lainnya\n";
            }
            $message .= "\n";
        }
    }
}

// 4.4 Grand Total
$message .= "📋 *GRAND TOTAL SEMUA SISTEM:*\n";
$grand_total_all = $grand_total_wms + $total_belum_note_all + $total_belum_check_all + $total_butuh_final_dir_all;
$message .= "• WMS Pending: " . $grand_total_wms . "\n";
$message .= "• Acol Belum Note: " . $total_belum_note_all . "\n";
$message .= "• Acol Belum Check: " . $total_belum_check_all . "\n";
$message .= "• Acol Butuh Final DIR: " . $total_butuh_final_dir_all . "\n";
$message .= "• *Total: " . $grand_total_all . " item* membutuhkan perhatian\n";
$message .= "━━━━━━━━━━━━━━━━━━\n";
$message .= "_Asta Homeware ERP | Report Date: " . $current_date . "_";

// ===============================================
// 5. KIRIM VIA FONNTE API
// ===============================================

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.fonnte.com/send',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
        'target' => $targets,
        'message' => $message,
        'countryCode' => '62',
    ),
    CURLOPT_HTTPHEADER => array(
        'Authorization: ' . $token
    ),
));

$response = curl_exec($curl);
if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
}
curl_close($curl);

// ===============================================
// 6. OUTPUT DAN DEBUG
// ===============================================

echo "📊 ASTA DAILY REPORT SYSTEM\n";
echo "===========================\n";
echo "Tanggal Eksekusi: " . date('Y-m-d H:i:s') . "\n";
echo "Token Fonnte: " . (strlen($token) > 10 ? substr($token, 0, 10) . "..." : $token) . "\n";
echo "Target: " . $targets . "\n\n";

echo "📋 PERIODE DATA:\n";
echo "----------------\n";
echo "ASTA WMS: Semua data (tanpa filter periode)\n";
echo "ASTA ACOL: " . date('d M Y', strtotime($current_month_start)) . " - " . date('d M Y', strtotime($current_month_end)) . "\n";
echo "Bulan: " . date('F Y', strtotime($current_month_start)) . "\n";
echo "Hanya ASTA (non-Kotime)\n";
echo "Ratio Limit: " . $ratio_limit . "%\n";
echo "Additional Revenue: Rp " . number_format($additional_revenue) . "\n\n";

if (isset($error_msg)) {
    echo "❌ ERROR cURL: " . $error_msg . "\n";
} else {
    echo "✅ Response API Fonnte:\n";
    echo $response . "\n\n";

    $response_data = json_decode($response, true);
    if ($response_data) {
        echo "Status API: " . (isset($response_data['status']) ? ($response_data['status'] ? "Berhasil" : "Gagal") : "Unknown") . "\n";
        if (isset($response_data['detail'])) {
            echo "Detail: " . $response_data['detail'] . "\n";
        }
    }
    echo "\n";
}

// Tampilkan summary data
echo "📊 DATA SUMMARY:\n";
echo "----------------\n";

echo "ASTA WMS (Semua data):\n";
echo "  - Instock Pending: " . $total_instock . "\n";
echo "  - Outstock Pending: " . $total_outstock . "\n";
echo "  - Verif DN: " . $total_verif_dn . "\n";
echo "  - Valid DN: " . $total_valid_dn . "\n";
echo "  - Final DN: " . $total_final_dn . "\n";
echo "  - Verif DM: " . $total_verif_dm . "\n";
echo "  - Valid DM: " . $total_valid_dm . "\n";
echo "  - Final DM: " . $total_final_dm . "\n";
echo "  - Total WMS: " . $grand_total_wms . "\n\n";

echo "ASTA ACOL (Periode: " . date('d M Y', strtotime($current_month_start)) . " - " . date('d M Y', strtotime($current_month_end)) . "):\n";
foreach (['shopee', 'tiktok', 'lazada'] as $mp) {
    $data = $acol_data[$mp];
    if ($data['total_faktur'] > 0) {
        echo "  " . strtoupper($mp) . ":\n";
        echo "    - Total Faktur: " . number_format($data['total_faktur']) . "\n";
        echo "      • Non-Retur: " . number_format($data['total_faktur'] - $data['total_faktur_retur']) . "\n";
        echo "      • Retur: " . number_format($data['total_faktur_retur']) . "\n";
        echo "    - Ratio >{$ratio_limit}%: " . number_format($data['ratio_exceed']) . "\n";
        echo "    - Belum Note: " . number_format($data['belum_note']) . "\n";
        echo "    - Belum Check: " . number_format($data['belum_check']) . "\n";
        echo "    - Butuh Final DIR: " . number_format($data['butuh_final_dir']) . "\n";
    }
}

echo "\n📈 GRAND TOTAL:\n";
echo "  - Total Faktur: " . number_format($total_faktur_all) . "\n";
echo "    • Non-Retur: " . number_format($total_faktur_all - $total_faktur_retur_all) . "\n";
echo "    • Retur: " . number_format($total_faktur_retur_all) . "\n";
echo "  - Ratio >{$ratio_limit}%: " . number_format($total_ratio_exceed_all) . "\n";
echo "  - Total Invoice: Rp " . number_format($total_invoice_all) . "\n";
echo "  - Total Diterima: Rp " . number_format($total_diterima_all) . "\n";
echo "  - Selisih: Rp " . number_format($total_invoice_all - $total_diterima_all) . "\n";
echo "  - Additional Revenue: Rp " . number_format($additional_revenue) . "\n";
echo "\n📋 ITEM YANG PERLU PERHATIAN:\n";
echo "  - WMS: " . $grand_total_wms . "\n";
echo "  - Acol: " . ($total_belum_note_all + $total_belum_check_all + $total_butuh_final_dir_all) . "\n";
echo "    • Belum Note: " . $total_belum_note_all . "\n";
echo "    • Belum Check: " . $total_belum_check_all . "\n";
echo "    • Butuh Final DIR: " . $total_butuh_final_dir_all . "\n";
echo "  - TOTAL: " . $grand_total_all . "\n\n";

echo "📱 PANJANG PESAN: " . strlen($message) . " karakter\n";

// Tutup koneksi database
$conn->close();
