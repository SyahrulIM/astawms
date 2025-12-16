<?php

$servername = "103.163.138.82";
$username = "astahome_it";
$password = "astawms=d17d09";
$dbname = "astahome_wms";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$token = 'ZsZ2Dp71dyKrgz3YAQKg'; // Ganti dengan token Fonnte Anda
$targets = '6281331090331-1528429522@g.us'; // Format: nomor telepon saja

// ===============================================
// 1. DATA ASTA WMS
// ===============================================

// 1.1 Hitung total pending verification instock & outstock
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

// ... (kode delivery note dan delivery manual tetap sama) ...

// 2. Hitung pending verification delivery note (kategori 1)
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

// 3. Hitung pending validation delivery note (kategori 1)
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

// 4. Hitung pending final direksi delivery note (kategori 1)
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

// 5. Hitung pending verification delivery manual (kategori 2)
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

// 6. Hitung pending validation delivery manual (kategori 2)
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

// 7. Hitung pending final direksi delivery manual (kategori 2)
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
// 2. DATA ASTA ACOL dengan kondisi bertingkat
// ===============================================

// Set parameter untuk Asta Acol
$current_month_start = date('Y-m-01');
$current_month_end = date('Y-m-t');
$ratio_limit = 30;

// 2.1 Query untuk mendapatkan semua data dengan ratio calculation
$sql_acol_detail = "
    SELECT
        asd.no_faktur,
        'shopee' as source,
        asd.order_date as shopee_order_date,
        aad.pay_date as accurate_pay_date,
        asd.total_faktur as shopee_total_faktur,
        asd.payment as shopee_payment,
        asd.refund as shopee_refund,
        asd.note,
        asd.is_check,
        asd.status_dir,
        aad.payment as accurate_payment,
        ROUND(((asd.total_faktur - COALESCE(aad.payment, 0)) / asd.total_faktur * 100), 2) as ratio_diference,
        CASE 
            WHEN ROUND(((asd.total_faktur - COALESCE(aad.payment, 0)) / asd.total_faktur * 100), 2) > {$ratio_limit}
            THEN 1
            ELSE 0
        END as is_ratio_exceed
    FROM acc_shopee_detail asd
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
    
    UNION ALL
    
    SELECT
        atd.no_faktur,
        'tiktok' as source,
        atd.order_date as shopee_order_date,
        aad.pay_date as accurate_pay_date,
        atd.total_faktur as shopee_total_faktur,
        atd.payment as shopee_payment,
        atd.refund as shopee_refund,
        atd.note,
        atd.is_check,
        atd.status_dir,
        aad.payment as accurate_payment,
        ROUND(((atd.total_faktur - COALESCE(aad.payment, 0)) / atd.total_faktur * 100), 2) as ratio_diference,
        CASE 
            WHEN ROUND(((atd.total_faktur - COALESCE(aad.payment, 0)) / atd.total_faktur * 100), 2) > {$ratio_limit}
            THEN 1
            ELSE 0
        END as is_ratio_exceed
    FROM acc_tiktok_detail atd
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
    
    UNION ALL
    
    SELECT
        ald.no_faktur,
        'lazada' as source,
        ald.order_date as shopee_order_date,
        aad.pay_date as accurate_pay_date,
        ald.total_faktur as shopee_total_faktur,
        ald.payment as shopee_payment,
        ald.refund as shopee_refund,
        ald.note,
        ald.is_check,
        ald.status_dir,
        aad.payment as accurate_payment,
        ROUND(((ald.total_faktur - COALESCE(aad.payment, 0)) / ald.total_faktur * 100), 2) as ratio_diference,
        CASE 
            WHEN ROUND(((ald.total_faktur - COALESCE(aad.payment, 0)) / ald.total_faktur * 100), 2) > {$ratio_limit}
            THEN 1
            ELSE 0
        END as is_ratio_exceed
    FROM acc_lazada_detail ald
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
";

$result_acol = $conn->query($sql_acol_detail);

// Inisialisasi data
$acol_data = [
    'shopee' => [
        'total_faktur' => 0,
        'belum_note' => 0,
        'belum_check' => 0,
        'butuh_allowed' => 0,
        'total_invoice' => 0,
        'total_diterima' => 0,
        'ratio_exceed' => 0,
        'total_selisih' => 0
    ],
    'tiktok' => [
        'total_faktur' => 0,
        'belum_note' => 0,
        'belum_check' => 0,
        'butuh_allowed' => 0,
        'total_invoice' => 0,
        'total_diterima' => 0,
        'ratio_exceed' => 0,
        'total_selisih' => 0
    ],
    'lazada' => [
        'total_faktur' => 0,
        'belum_note' => 0,
        'belum_check' => 0,
        'butuh_allowed' => 0,
        'total_invoice' => 0,
        'total_diterima' => 0,
        'ratio_exceed' => 0,
        'total_selisih' => 0
    ]
];

$critical_invoices = [];
$all_invoices = [];

if ($result_acol) {
    while ($row = $result_acol->fetch_assoc()) {
        $source = $row['source'];

        // Hitung data dasar
        $acol_data[$source]['total_faktur']++;
        $acol_data[$source]['total_invoice'] += (float) $row['shopee_total_faktur'];
        $acol_data[$source]['total_diterima'] += (float) $row['accurate_payment'];
        $acol_data[$source]['total_selisih'] += (float) ($row['shopee_total_faktur'] - $row['accurate_payment']);

        // Cek apakah ratio melebihi 30%
        $is_ratio_exceed = $row['is_ratio_exceed'] == 1;

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
            // Kategori 3: Butuh Allowed (ratio >30% DAN note NOT empty DAN is_check = 1 DAN status_dir empty/bukan 'Allowed')
            elseif (
                !$is_note_empty && $row['is_check'] == 1 && ($row['status_dir'] === null || $row['status_dir'] != 'Allowed')
            ) {
                $acol_data[$source]['butuh_allowed']++;
                $category = 'Butuh Allowed';
            } else {
                $category = 'Lainnya';
            }

            // Simpan invoice kritis untuk ditampilkan
            $critical_invoices[] = [
                'no_faktur' => $row['no_faktur'],
                'source' => ucfirst($source),
                'category' => $category,
                'ratio_diference' => $row['ratio_diference'],
                'note_status' => $is_note_empty ? 'Empty' : 'Filled',
                'check_status' => $row['is_check'] == 1 ? 'Checked' : 'Not Checked',
                'dir_status' => $row['status_dir'] ?? 'Empty'
            ];
        }

        $all_invoices[] = $row;
    }
}

// ===============================================
// 3. FORMAT PESAN WHATSAPP
// ===============================================

$current_date = date('d F Y');
$message = "📊 *ASTA HOMEWARE - DAILY REPORT*\n";
$message .= "*Tanggal: " . $current_date . "*\n\n";

// 3.1 Asta WMS Section
$message .= "══════ Asta WMS ═════\n";
$message .= "📦 *VERIFIKASI INSTOCK/OUTSTOCK:*\n";
$message .= "• Instock Pending: " . $total_instock . "\n";
$message .= "• Outstock Pending: " . $total_outstock . "\n";
$message .= "• Total: " . ($total_instock + $total_outstock) . "\n\n";

$message .= "🚚 *DELIVERY NOTE:*\n";
$message .= "• Verifikasi Pending: " . $total_verif_dn . "\n";
$message .= "• Validasi Pending: " . $total_valid_dn . "\n";
$message .= "• Final Dir Pending: " . $total_final_dn . "\n";
$message .= "• Total: " . ($total_verif_dn + $total_valid_dn + $total_final_dn) . "\n\n";

$message .= "📝 *DELIVERY MANUAL:*\n";
$message .= "• Verifikasi Pending: " . $total_verif_dm . "\n";
$message .= "• Validasi Pending: " . $total_valid_dm . "\n";
$message .= "• Final Dir Pending: " . $total_final_dm . "\n";
$message .= "• Total: " . ($total_verif_dm + $total_valid_dm + $total_final_dm) . "\n\n";

$message .= "📈 *GRAND TOTAL PENDING WMS:*\n";
$grand_total_wms = ($total_instock + $total_outstock) + ($total_verif_dn + $total_valid_dn + $total_final_dn) + ($total_verif_dm + $total_valid_dm + $total_final_dm);
$message .= "• *" . $grand_total_wms . " transaksi* membutuhkan tindakan\n\n";

// 3.2 Asta Acol Section dengan kategori bertingkat
$message .= "══════ Asta Acol ═════\n";
$message .= "*Periode: " . date('d M Y', strtotime($current_month_start)) . " - " . date('d M Y', strtotime($current_month_end)) . "*\n";
$message .= "*Ratio Limit: " . $ratio_limit . "%*\n";
$message .= "*Hanya faktur dengan ratio >{$ratio_limit}% yang dihitung*\n\n";

$message .= "📋 *KATEGORI BERTINGKAT:*\n";
$message .= "1. Belum Note: Ratio >{$ratio_limit}% & note kosong\n";
$message .= "2. Belum Check: Ratio >{$ratio_limit}% & note terisi & belum check\n";
$message .= "3. Butuh Allowed: Ratio >{$ratio_limit}% & note terisi & sudah check & status dir bukan 'Allowed'\n\n";

$total_faktur_all = 0;
$total_ratio_exceed_all = 0;
$total_belum_note_all = 0;
$total_belum_check_all = 0;
$total_butuh_allowed_all = 0;
$total_invoice_all = 0;
$total_diterima_all = 0;

foreach (['shopee', 'tiktok', 'lazada'] as $mp) {
    $data = $acol_data[$mp];
    if ($data['total_faktur'] > 0) {
        $message .= "🛒 *" . strtoupper($mp) . "*\n";
        $message .= "• Total Faktur: " . number_format($data['total_faktur']) . "\n";
        $message .= "• Ratio >{$ratio_limit}%: " . number_format($data['ratio_exceed']) . "\n";
        $message .= "• Belum Note: " . number_format($data['belum_note']) . "\n";
        $message .= "• Belum Check: " . number_format($data['belum_check']) . "\n";
        $message .= "• Butuh Allowed: " . number_format($data['butuh_allowed']) . "\n";

        $ratio = $data['total_invoice'] > 0 ? (($data['total_invoice'] - $data['total_diterima']) / $data['total_invoice'] * 100) : 0;

        $message .= "• Total Invoice: Rp " . number_format($data['total_invoice']) . "\n";
        $message .= "• Total Diterima: Rp " . number_format($data['total_diterima']) . "\n";
        $message .= "• Selisih: Rp " . number_format($data['total_selisih']) . "\n";
        $message .= "• Ratio: " . number_format($ratio, 2) . "%\n\n";

        $total_faktur_all += $data['total_faktur'];
        $total_ratio_exceed_all += $data['ratio_exceed'];
        $total_belum_note_all += $data['belum_note'];
        $total_belum_check_all += $data['belum_check'];
        $total_butuh_allowed_all += $data['butuh_allowed'];
        $total_invoice_all += $data['total_invoice'];
        $total_diterima_all += $data['total_diterima'];
    }
}

if ($total_faktur_all > 0) {
    $message .= "📊 *TOTAL SEMUA MARKETPLACE:*\n";
    $message .= "• Total Faktur: " . number_format($total_faktur_all) . "\n";
    $message .= "• Ratio >{$ratio_limit}%: " . number_format($total_ratio_exceed_all) . "\n";
    $message .= "• Belum Note: " . number_format($total_belum_note_all) . "\n";
    $message .= "• Belum Check: " . number_format($total_belum_check_all) . "\n";
    $message .= "• Butuh Allowed: " . number_format($total_butuh_allowed_all) . "\n";

    $selisih_all = $total_invoice_all - $total_diterima_all;
    $ratio_all = $total_invoice_all > 0 ? ($selisih_all / $total_invoice_all) * 100 : 0;

    $message .= "• Total Invoice: Rp " . number_format($total_invoice_all) . "\n";
    $message .= "• Total Diterima: Rp " . number_format($total_diterima_all) . "\n";
    $message .= "• Selisih: Rp " . number_format($selisih_all) . "\n";
    $message .= "• Ratio: " . number_format($ratio_all, 2) . "%\n\n";
} else {
    $message .= "ℹ️ *Tidak ada data faktur untuk periode ini*\n\n";
}

// 3.3 Critical Invoices Section - dikelompokkan per kategori
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
    $category_order = ['Belum Note', 'Belum Check', 'Butuh Allowed'];

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

            $display_count = min(5, count($invoices));
            for ($i = 0; $i < $display_count; $i++) {
                $inv = $invoices[$i];
                $message .= ($i + 1) . ". " . $inv['no_faktur'] .
                    " (Ratio: " . $inv['ratio_diference'] . "%)" .
                    " [" . $inv['source'] . "]\n";
            }

            if (count($invoices) > $display_count) {
                $message .= "...dan " . (count($invoices) - $display_count) . " lainnya\n";
            }
            $message .= "\n";
        }
    }
}

// 3.4 Progress Tracking
$total_critical = $total_belum_note_all + $total_belum_check_all + $total_butuh_allowed_all;
if ($total_ratio_exceed_all > 0) {
    $message .= "📈 *PROGRESS TRACKING:*\n";
    $message .= "Total Ratio >{$ratio_limit}%: " . number_format($total_ratio_exceed_all) . "\n";
    $message .= "Belum Note: " . number_format($total_belum_note_all) .
        " (" . number_format(($total_belum_note_all / $total_ratio_exceed_all * 100), 1) . "%)\n";
    $message .= "Belum Check: " . number_format($total_belum_check_all) .
        " (" . number_format(($total_belum_check_all / $total_ratio_exceed_all * 100), 1) . "%)\n";
    $message .= "Butuh Allowed: " . number_format($total_butuh_allowed_all) .
        " (" . number_format(($total_butuh_allowed_all / $total_ratio_exceed_all * 100), 1) . "%)\n\n";
}

// 3.5 Grand Total
$message .= "📋 *GRAND TOTAL SEMUA SISTEM:*\n";
$grand_total_all = $grand_total_wms + $total_belum_note_all + $total_belum_check_all + $total_butuh_allowed_all;
$message .= "• *" . $grand_total_all . " item* membutuhkan perhatian\n";
$message .= "━━━━━━━━━━━━━━━━━━\n";
$message .= "_Asta Homeware ERP_";

// ===============================================
// 4. KIRIM VIA FONNTE API
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
// 5. OUTPUT DAN DEBUG
// ===============================================

echo "📊 ASTA DAILY REPORT SYSTEM\n";
echo "===========================\n";
echo "Tanggal Eksekusi: " . date('Y-m-d H:i:s') . "\n";
echo "Token Fonnte: " . (strlen($token) > 10 ? substr($token, 0, 10) . "..." : $token) . "\n";
echo "Target: " . $targets . "\n";
echo "Periode Acol: " . $current_month_start . " - " . $current_month_end . "\n";
echo "Ratio Limit: " . $ratio_limit . "%\n\n";

echo "📋 KATEGORI BERTINGKAT:\n";
echo "1. Belum Note: Ratio >{$ratio_limit}% & note kosong\n";
echo "2. Belum Check: Ratio >{$ratio_limit}% & note terisi & is_check = 0\n";
echo "3. Butuh Allowed: Ratio >{$ratio_limit}% & note terisi & is_check = 1 & status_dir bukan 'Allowed'\n\n";

if (isset($error_msg)) {
    echo "❌ ERROR cURL: " . $error_msg . "\n";
} else {
    echo "✅ Response API Fonnte:\n";
    echo $response . "\n\n";

    // Parse response JSON
    $response_data = json_decode($response, true);
    if ($response_data) {
        echo "Status API: " . (isset($response_data['status']) ? ($response_data['status'] ? "Berhasil" : "Gagal") : "Unknown") . "\n";
        if (isset($response_data['detail'])) {
            echo "Detail: " . $response_data['detail'] . "\n";
        }
        if (isset($response_data['message'])) {
            echo "Message: " . $response_data['message'] . "\n";
        }
    }
    echo "\n";
}

// Tampilkan summary data
echo "📊 DATA SUMMARY ASTA ACOL:\n";
echo "--------------------------\n";

foreach (['shopee', 'tiktok', 'lazada'] as $mp) {
    $data = $acol_data[$mp];
    if ($data['total_faktur'] > 0) {
        echo strtoupper($mp) . ":\n";
        echo "  - Total Faktur: " . number_format($data['total_faktur']) . "\n";
        echo "  - Ratio >{$ratio_limit}%: " . number_format($data['ratio_exceed']) .
            " (" . number_format(($data['ratio_exceed'] / $data['total_faktur'] * 100), 1) . "%)\n";
        echo "  - Belum Note: " . number_format($data['belum_note']) . "\n";
        echo "  - Belum Check: " . number_format($data['belum_check']) . "\n";
        echo "  - Butuh Allowed: " . number_format($data['butuh_allowed']) . "\n";
        echo "  - Total Invoice: Rp " . number_format($data['total_invoice']) . "\n";
        echo "  - Total Diterima: Rp " . number_format($data['total_diterima']) . "\n";
        echo "  - Selisih: Rp " . number_format($data['total_selisih']) . "\n";

        $ratio = $data['total_invoice'] > 0 ? (($data['total_invoice'] - $data['total_diterima']) / $data['total_invoice'] * 100) : 0;
        echo "  - Ratio: " . number_format($ratio, 2) . "%\n\n";
    }
}

echo "TOTAL ACOL:\n";
echo "- Total Faktur: " . $total_faktur_all . "\n";
echo "- Ratio >{$ratio_limit}%: " . $total_ratio_exceed_all .
    " (" . number_format(($total_ratio_exceed_all / $total_faktur_all * 100), 1) . "%)\n";
echo "- Belum Note: " . $total_belum_note_all .
    " (" . number_format(($total_belum_note_all / $total_ratio_exceed_all * 100), 1) . "% dari ratio >{$ratio_limit}%)\n";
echo "- Belum Check: " . $total_belum_check_all .
    " (" . number_format(($total_belum_check_all / $total_ratio_exceed_all * 100), 1) . "% dari ratio >{$ratio_limit}%)\n";
echo "- Butuh Allowed: " . $total_butuh_allowed_all .
    " (" . number_format(($total_butuh_allowed_all / $total_ratio_exceed_all * 100), 1) . "% dari ratio >{$ratio_limit}%)\n";
echo "- Total Invoice: Rp " . number_format($total_invoice_all) . "\n";
echo "- Total Diterima: Rp " . number_format($total_diterima_all) . "\n";
echo "- Selisih: Rp " . number_format($selisih_all) . "\n";
echo "- Ratio: " . number_format($ratio_all, 2) . "%\n\n";

echo "Invoice Kritis (Ratio >{$ratio_limit}%): " . count($critical_invoices) . "\n";
if (!empty($critical_invoices)) {
    echo "Contoh per kategori:\n";

    // Tampilkan 2 contoh per kategori
    $categories_display = ['Belum Note', 'Belum Check', 'Butuh Allowed'];
    foreach ($categories_display as $category) {
        $category_invoices = array_filter($critical_invoices, function ($inv) use ($category) {
            return $inv['category'] === $category;
        });

        if (count($category_invoices) > 0) {
            echo "  " . $category . " (" . count($category_invoices) . "):\n";
            $count = 0;
            foreach ($category_invoices as $inv) {
                if ($count >= 2) break;
                echo "    - " . $inv['no_faktur'] . " (Ratio: " . $inv['ratio_diference'] . "%) [" . $inv['source'] . "]\n";
                $count++;
            }
            if (count($category_invoices) > 2) {
                echo "    ...dan " . (count($category_invoices) - 2) . " lainnya\n";
            }
        }
    }
}
echo "\n";

echo "📈 GRAND TOTAL ALL: " . $grand_total_all . "\n";
echo "  - WMS: " . $grand_total_wms . "\n";
echo "  - Acol Belum Note: " . $total_belum_note_all . "\n";
echo "  - Acol Belum Check: " . $total_belum_check_all . "\n";
echo "  - Acol Butuh Allowed: " . $total_butuh_allowed_all . "\n\n";

echo "📱 PANJANG PESAN: " . strlen($message) . " karakter\n";

// Tampilkan preview pesan (opsional)
echo "\n📱 PREVIEW PESAN (15 baris pertama):\n";
echo "===================================\n";
$lines = explode("\n", $message);
for ($i = 0; $i < min(15, count($lines)); $i++) {
    echo $lines[$i] . "\n";
}
if (count($lines) > 15) {
    echo "...\n";
    echo "(Total: " . count($lines) . " baris)\n";
}

// Tutup koneksi database
$conn->close();
