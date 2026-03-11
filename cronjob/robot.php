<?php

// Helper function di luar class
function getStatusText($status)
{
    switch ($status) {
        case 'safe':
            return '✅ Safe (updated today/yesterday)';
        case 'empty':
            return '📭 No Transaction (no data)';
        case 'not_updated_today':
            return '⚠️ Not Updated Today';
        default:
            return '❓ Unknown';
    }
}

$servername = "103.163.138.102";
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
// PERIODE ACOL: tanggal 1 bulan sekarang sampai kemarin
// ===============================================
$current_month_start = date('Y-m-01');
$current_month_end = date('Y-m-d', strtotime('-1 day')); // Sampai kemarin
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$ratio_limit = 30;

// ===============================================
// CHECK MARKETPLACE STATUS (TERMASUK BLIBLI - ONLY ASTA)
// ===============================================
$marketplace_status = [
    'shopee' => ['asta' => '', 'kotime' => ''],
    'tiktok' => ['asta' => '', 'kotime' => ''],
    'lazada' => ['asta' => '', 'kotime' => ''],
    'blibli' => ['asta' => '', 'kotime' => '']
];

// Check Lazada ASTA data
$sql_lazada_asta_status = "
    SELECT 
        CASE 
            WHEN COUNT(*) = 0 THEN 'empty'
            WHEN MAX(ald.updated_date) >= '{$today}' THEN 'safe'
            WHEN MAX(ald.updated_date) >= '{$yesterday}' THEN 'safe'
            ELSE 'not_updated_today'
        END as status,
        COUNT(*) as total_records,
        MAX(ald.updated_date) as last_update
    FROM acc_lazada_detail ald
    INNER JOIN acc_lazada al ON al.idacc_lazada = ald.idacc_lazada
    WHERE ald.order_date BETWEEN '{$current_month_start}' AND '{$current_month_end}'
    AND ald.status = 1
    AND (al.is_kotime = 0 OR al.is_kotime IS NULL)
";
$result_lazada_asta = $conn->query($sql_lazada_asta_status);
if ($result_lazada_asta) {
    $row = $result_lazada_asta->fetch_assoc();
    $marketplace_status['lazada']['asta'] = $row['status'];
}

// Check Lazada Kotime data
$sql_lazada_kotime_status = "
    SELECT 
        CASE 
            WHEN COUNT(*) = 0 THEN 'empty'
            WHEN MAX(ald.updated_date) >= '{$today}' THEN 'safe'
            WHEN MAX(ald.updated_date) >= '{$yesterday}' THEN 'safe'
            ELSE 'not_updated_today'
        END as status,
        COUNT(*) as total_records,
        MAX(ald.updated_date) as last_update
    FROM acc_lazada_detail ald
    INNER JOIN acc_lazada al ON al.idacc_lazada = ald.idacc_lazada
    WHERE ald.order_date BETWEEN '{$current_month_start}' AND '{$current_month_end}'
    AND ald.status = 1
    AND al.is_kotime = 1
";
$result_lazada_kotime = $conn->query($sql_lazada_kotime_status);
if ($result_lazada_kotime) {
    $row = $result_lazada_kotime->fetch_assoc();
    $marketplace_status['lazada']['kotime'] = $row['status'];
}

// Check Shopee ASTA data
$sql_shopee_asta_status = "
    SELECT 
        CASE 
            WHEN COUNT(*) = 0 THEN 'empty'
            WHEN MAX(asd.updated_date) >= '{$today}' THEN 'safe'
            WHEN MAX(asd.updated_date) >= '{$yesterday}' THEN 'safe'
            ELSE 'not_updated_today'
        END as status,
        COUNT(*) as total_records,
        MAX(asd.updated_date) as last_update
    FROM acc_shopee_detail asd
    INNER JOIN acc_shopee s ON s.idacc_shopee = asd.idacc_shopee
    WHERE asd.order_date BETWEEN '{$current_month_start}' AND '{$current_month_end}'
    AND asd.status = 1
    AND (s.is_kotime = 0 OR s.is_kotime IS NULL)
";
$result_shopee_asta = $conn->query($sql_shopee_asta_status);
if ($result_shopee_asta) {
    $row = $result_shopee_asta->fetch_assoc();
    $marketplace_status['shopee']['asta'] = $row['status'];
}

// Check Shopee Kotime data
$sql_shopee_kotime_status = "
    SELECT 
        CASE 
            WHEN COUNT(*) = 0 THEN 'empty'
            WHEN MAX(asd.updated_date) >= '{$today}' THEN 'safe'
            WHEN MAX(asd.updated_date) >= '{$yesterday}' THEN 'safe'
            ELSE 'not_updated_today'
        END as status,
        COUNT(*) as total_records,
        MAX(asd.updated_date) as last_update
    FROM acc_shopee_detail asd
    INNER JOIN acc_shopee s ON s.idacc_shopee = asd.idacc_shopee
    WHERE asd.order_date BETWEEN '{$current_month_start}' AND '{$current_month_end}'
    AND asd.status = 1
    AND s.is_kotime = 1
";
$result_shopee_kotime = $conn->query($sql_shopee_kotime_status);
if ($result_shopee_kotime) {
    $row = $result_shopee_kotime->fetch_assoc();
    $marketplace_status['shopee']['kotime'] = $row['status'];
}

// Check TikTok ASTA data
$sql_tiktok_asta_status = "
    SELECT 
        CASE 
            WHEN COUNT(*) = 0 THEN 'empty'
            WHEN MAX(atd.updated_date) >= '{$today}' THEN 'safe'
            WHEN MAX(atd.updated_date) >= '{$yesterday}' THEN 'safe'
            ELSE 'not_updated_today'
        END as status,
        COUNT(*) as total_records,
        MAX(atd.updated_date) as last_update
    FROM acc_tiktok_detail atd
    INNER JOIN acc_tiktok t ON t.idacc_tiktok = atd.idacc_tiktok
    WHERE atd.order_date BETWEEN '{$current_month_start}' AND '{$current_month_end}'
    AND atd.status = 1
    AND (t.is_kotime = 0 OR t.is_kotime IS NULL)
";
$result_tiktok_asta = $conn->query($sql_tiktok_asta_status);
if ($result_tiktok_asta) {
    $row = $result_tiktok_asta->fetch_assoc();
    $marketplace_status['tiktok']['asta'] = $row['status'];
}

// Check TikTok Kotime data
$sql_tiktok_kotime_status = "
    SELECT 
        CASE 
            WHEN COUNT(*) = 0 THEN 'empty'
            WHEN MAX(atd.updated_date) >= '{$today}' THEN 'safe'
            WHEN MAX(atd.updated_date) >= '{$yesterday}' THEN 'safe'
            ELSE 'not_updated_today'
        END as status,
        COUNT(*) as total_records,
        MAX(atd.updated_date) as last_update
    FROM acc_tiktok_detail atd
    INNER JOIN acc_tiktok t ON t.idacc_tiktok = atd.idacc_tiktok
    WHERE atd.order_date BETWEEN '{$current_month_start}' AND '{$current_month_end}'
    AND atd.status = 1
    AND t.is_kotime = 1
";
$result_tiktok_kotime = $conn->query($sql_tiktok_kotime_status);
if ($result_tiktok_kotime) {
    $row = $result_tiktok_kotime->fetch_assoc();
    $marketplace_status['tiktok']['kotime'] = $row['status'];
}

// Check Blibli ASTA data (ONLY ASTA)
$sql_blibli_asta_status = "
    SELECT 
        CASE 
            WHEN COUNT(*) = 0 THEN 'empty'
            WHEN MAX(abd.updated_date) >= '{$today}' THEN 'safe'
            WHEN MAX(abd.updated_date) >= '{$yesterday}' THEN 'safe'
            ELSE 'not_updated_today'
        END as status,
        COUNT(*) as total_records,
        MAX(abd.updated_date) as last_update
    FROM acc_blibli_detail abd
    INNER JOIN acc_blibli b ON b.idacc_blibli = abd.idacc_blibli
    WHERE abd.order_date BETWEEN '{$current_month_start}' AND '{$current_month_end}'
    AND abd.status = 1
    AND (b.is_kotime = 0 OR b.is_kotime IS NULL)  -- ONLY ASTA
";
$result_blibli_asta = $conn->query($sql_blibli_asta_status);
if ($result_blibli_asta) {
    $row = $result_blibli_asta->fetch_assoc();
    $marketplace_status['blibli']['asta'] = $row['status'];
}
// Blibli Kotime - tidak ada data
$marketplace_status['blibli']['kotime'] = 'empty';

// ===============================================
// QUERY DATA ACOL (UNION ALL MARKETPLACE)
// ===============================================
$sql_acol_detail = "
    SELECT
        asd.no_faktur,
        MAX(asd.order_date) AS order_date,
        MAX(asd.pay_date) AS pay_date,
        MAX(asd.total_faktur) AS total_faktur,
        MAX(asd.discount) AS discount,
        MAX(asd.payment) AS payment,
        MAX(asd.refund) AS refund,
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
    GROUP BY asd.no_faktur

    UNION

    SELECT
        atd.no_faktur,
        MAX(atd.order_date) AS order_date,
        MAX(atd.pay_date) AS pay_date,
        MAX(atd.total_faktur) AS total_faktur,
        MAX(atd.discount) AS discount,
        MAX(atd.payment) AS payment,
        MAX(atd.refund) AS refund,
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
    GROUP BY atd.no_faktur

    UNION

    SELECT
        ald.no_faktur,
        MAX(ald.order_date) AS order_date,
        MAX(ald.pay_date) AS pay_date,
        MAX(ald.total_faktur) AS total_faktur,
        MAX(ald.discount) AS discount,
        MAX(ald.payment) AS payment,
        MAX(ald.refund) AS refund,
        MAX(ald.note) AS note,
        MAX(ald.is_check) AS is_check,
        MAX(ald.status_dir) AS status_dir,
        MAX(aad.pay_date) AS accurate_pay_date,
        MAX(aad.total_faktur) AS accurate_total_faktur,
        MAX(aad.discount) AS accurate_discount,
        MAX(aad.payment) AS accurate_payment,
        'lazada' as source,
        MAX(al.is_kotime) as is_kotime
    FROM acc_lazada_detail ald
    INNER JOIN acc_lazada al ON al.idacc_lazada = ald.idacc_lazada
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
    GROUP BY ald.no_faktur

    UNION

    SELECT
        abd.no_faktur,
        MAX(abd.order_date) AS order_date,
        MAX(abd.pay_date) AS pay_date,
        MAX(abd.total_faktur) AS total_faktur,
        MAX(abd.discount) AS discount,
        MAX(abd.payment) AS payment,
        MAX(abd.refund) AS refund,
        MAX(abd.note) AS note,
        MAX(abd.is_check) AS is_check,
        MAX(abd.status_dir) AS status_dir,
        MAX(aad.pay_date) AS accurate_pay_date,
        MAX(aad.total_faktur) AS accurate_total_faktur,
        MAX(aad.discount) AS accurate_discount,
        MAX(aad.payment) AS accurate_payment,
        'blibli' as source,
        0 as is_kotime  -- force ASTA only
    FROM acc_blibli_detail abd
    INNER JOIN acc_blibli b ON b.idacc_blibli = abd.idacc_blibli
    LEFT JOIN (
        SELECT a1.*
        FROM acc_accurate_detail a1
        INNER JOIN (
            SELECT no_faktur, MAX(idacc_accurate_detail) AS max_id
            FROM acc_accurate_detail
            GROUP BY no_faktur
        ) latest ON a1.no_faktur = latest.no_faktur AND a1.idacc_accurate_detail = latest.max_id
    ) aad ON aad.no_faktur = abd.no_faktur
    WHERE abd.order_date BETWEEN '{$current_month_start}' AND '{$current_month_end}'
    AND aad.payment IS NOT NULL
    AND abd.total_faktur > 0
    AND abd.status = 1
    AND (b.is_kotime = 0 OR b.is_kotime IS NULL)  -- ONLY ASTA
    GROUP BY abd.no_faktur

    ORDER BY no_faktur ASC
";

$result_acol = $conn->query($sql_acol_detail);

// Inisialisasi data Acol untuk ASTA dan Kotime
$acol_data = [
    'asta_shopee' => [
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
    'asta_tiktok' => [
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
    'asta_lazada' => [
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
    'asta_blibli' => [
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
    'kotime_shopee' => [
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
    'kotime_tiktok' => [
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
    'kotime_lazada' => [
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

if ($result_acol) {
    while ($row = $result_acol->fetch_assoc()) {
        $source = $row['source'];
        $is_kotime = ($row['is_kotime'] == 1) ? 1 : 0;

        // Skip blibli kotime (tidak mungkin, tapi jaga-jaga)
        if ($source == 'blibli' && $is_kotime == 1) {
            continue;
        }

        $brand_prefix = $is_kotime ? 'kotime_' : 'asta_';
        $data_key = $brand_prefix . $source;

        if (!isset($acol_data[$data_key])) {
            continue;
        }

        $is_retur = ($row['refund'] ?? 0) < 0;
        $shopee = (float) ($row['total_faktur'] ?? 0);
        $accurate = (float) ($row['accurate_payment'] ?? 0);

        // Akumulasi data dasar (termasuk retur)
        $acol_data[$data_key]['total_faktur']++;
        $acol_data[$data_key]['total_invoice'] += $shopee;
        $acol_data[$data_key]['total_diterima'] += $accurate;
        $acol_data[$data_key]['total_selisih'] += ($shopee - $accurate);

        if ($is_retur) {
            $acol_data[$data_key]['total_faktur_retur']++;
            $acol_data[$data_key]['total_invoice_retur'] += $shopee;
            $acol_data[$data_key]['total_diterima_retur'] += $accurate;
        }

        $ratio = ($accurate > 0 && $shopee > 0) ? (($shopee - $accurate) / $shopee) * 100 : 0;
        $is_ratio_exceed = $ratio > $ratio_limit && !$is_retur; // retur dikecualikan dari perhitungan ratio exceed

        if ($is_ratio_exceed) {
            $acol_data[$data_key]['ratio_exceed']++;

            $is_note_empty = empty($row['note']);
            if ($is_note_empty) {
                $acol_data[$data_key]['belum_note']++;
                $category = 'Belum Note';
            } elseif (!$is_note_empty && $row['is_check'] == 0) {
                $acol_data[$data_key]['belum_check']++;
                $category = 'Belum Check';
            } elseif (!$is_note_empty && $row['is_check'] == 1 && ($row['status_dir'] === null || $row['status_dir'] != 'Final DIR')) {
                $acol_data[$data_key]['butuh_final_dir']++;
                $category = 'Butuh Final DIR';
            } else {
                $category = 'Lainnya';
            }

            $critical_invoices[] = [
                'no_faktur' => $row['no_faktur'],
                'source' => ucfirst($source),
                'brand' => $is_kotime ? 'Kotime' : 'ASTA',
                'category' => $category,
                'ratio_diference' => $ratio,
                'note_status' => $is_note_empty ? 'Empty' : 'Filled',
                'check_status' => $row['is_check'] == 1 ? 'Checked' : 'Not Checked',
                'dir_status' => $row['status_dir'] ?? 'Empty'
            ];
        }
    }
}

// ===============================================
// HITUNG ADDITIONAL REVENUE
// ===============================================
$additional_revenue_asta = 0;
$additional_revenue_kotime = 0;

// Shopee ASTA
$sql_additional_shopee_asta = "
    SELECT SUM(asa.additional_revenue) as total
    FROM acc_shopee_additional asa
    INNER JOIN acc_shopee s ON s.idacc_shopee = asa.idacc_shopee
    WHERE asa.start_date >= '{$current_month_start}' 
    AND asa.end_date <= '{$current_month_end}'
    AND (s.is_kotime = 0 OR s.is_kotime IS NULL)
    AND asa.status = 1
";
$result_shopee_add_asta = $conn->query($sql_additional_shopee_asta);
if ($result_shopee_add_asta) {
    $row = $result_shopee_add_asta->fetch_assoc();
    $additional_revenue_asta += $row['total'] ?? 0;
}

// TikTok ASTA
$sql_additional_tiktok_asta = "
    SELECT SUM(ata.additional_revenue) as total
    FROM acc_tiktok_additional ata
    INNER JOIN acc_tiktok t ON t.idacc_tiktok = ata.idacc_tiktok
    WHERE ata.start_date >= '{$current_month_start}' 
    AND ata.end_date <= '{$current_month_end}'
    AND (t.is_kotime = 0 OR t.is_kotime IS NULL)
    AND ata.status = 1
";
$result_tiktok_add_asta = $conn->query($sql_additional_tiktok_asta);
if ($result_tiktok_add_asta) {
    $row = $result_tiktok_add_asta->fetch_assoc();
    $additional_revenue_asta += $row['total'] ?? 0;
}

// Lazada ASTA
$sql_additional_lazada_asta = "
    SELECT SUM(ala.additional_revenue) as total
    FROM acc_lazada_additional ala
    INNER JOIN acc_lazada al ON al.idacc_lazada = ala.idacc_lazada
    WHERE ala.start_date >= '{$current_month_start}' 
    AND ala.end_date <= '{$current_month_end}'
    AND (al.is_kotime = 0 OR al.is_kotime IS NULL)
    AND ala.status = 1
";
$result_lazada_add_asta = $conn->query($sql_additional_lazada_asta);
if ($result_lazada_add_asta) {
    $row = $result_lazada_add_asta->fetch_assoc();
    $additional_revenue_asta += $row['total'] ?? 0;
}

// Blibli ASTA
$sql_additional_blibli_asta = "
    SELECT SUM(aba.additional_revenue) as total
    FROM acc_blibli_additional aba
    INNER JOIN acc_blibli b ON b.idacc_blibli = aba.idacc_blibli
    WHERE aba.start_date >= '{$current_month_start}' 
    AND aba.end_date <= '{$current_month_end}'
    AND (b.is_kotime = 0 OR b.is_kotime IS NULL)
    AND aba.status = 1
";
$result_blibli_add_asta = $conn->query($sql_additional_blibli_asta);
if ($result_blibli_add_asta) {
    $row = $result_blibli_add_asta->fetch_assoc();
    $additional_revenue_asta += $row['total'] ?? 0;
}

// Shopee Kotime
$sql_additional_shopee_kotime = "
    SELECT SUM(asa.additional_revenue) as total
    FROM acc_shopee_additional asa
    INNER JOIN acc_shopee s ON s.idacc_shopee = asa.idacc_shopee
    WHERE asa.start_date >= '{$current_month_start}' 
    AND asa.end_date <= '{$current_month_end}'
    AND s.is_kotime = 1
    AND asa.status = 1
";
$result_shopee_add_kotime = $conn->query($sql_additional_shopee_kotime);
if ($result_shopee_add_kotime) {
    $row = $result_shopee_add_kotime->fetch_assoc();
    $additional_revenue_kotime += $row['total'] ?? 0;
}

// TikTok Kotime
$sql_additional_tiktok_kotime = "
    SELECT SUM(ata.additional_revenue) as total
    FROM acc_tiktok_additional ata
    INNER JOIN acc_tiktok t ON t.idacc_tiktok = ata.idacc_tiktok
    WHERE ata.start_date >= '{$current_month_start}' 
    AND ata.end_date <= '{$current_month_end}'
    AND t.is_kotime = 1
    AND ata.status = 1
";
$result_tiktok_add_kotime = $conn->query($sql_additional_tiktok_kotime);
if ($result_tiktok_add_kotime) {
    $row = $result_tiktok_add_kotime->fetch_assoc();
    $additional_revenue_kotime += $row['total'] ?? 0;
}

// Lazada Kotime
$sql_additional_lazada_kotime = "
    SELECT SUM(ala.additional_revenue) as total
    FROM acc_lazada_additional ala
    INNER JOIN acc_lazada al ON al.idacc_lazada = ala.idacc_lazada
    WHERE ala.start_date >= '{$current_month_start}' 
    AND ala.end_date <= '{$current_month_end}'
    AND al.is_kotime = 1
    AND ala.status = 1
";
$result_lazada_add_kotime = $conn->query($sql_additional_lazada_kotime);
if ($result_lazada_add_kotime) {
    $row = $result_lazada_add_kotime->fetch_assoc();
    $additional_revenue_kotime += $row['total'] ?? 0;
}

$additional_revenue_total = $additional_revenue_asta + $additional_revenue_kotime;

// ===============================================
// FORMAT PESAN WHATSAPP (TANPA WMS, TANPA RETUR)
// ===============================================
$current_date = date('d F Y');
$message = "📊 *ASTA HOMEWARE - DAILY REPORT*\n";
$message .= "*Tanggal: " . $current_date . "*\n\n";

// Marketplace Status
$message .= "══════ STATUS MARKETPLACE ═════\n";
$message .= "*Periode: " . date('d M Y', strtotime($current_month_start)) . " - " . date('d M Y', strtotime($current_month_end)) . "*\n\n";

foreach (['shopee', 'tiktok', 'lazada', 'blibli'] as $mp) {
    $message .= "🛒 *" . strtoupper($mp) . "*\n";
    $asta_status = $marketplace_status[$mp]['asta'];
    $asta_status_text = getStatusText($asta_status);
    $message .= "  🟦 ASTA: " . $asta_status_text . "\n";
    if ($mp == 'blibli') {
        $message .= "  🟧 Kotime: 📭 No Transaction (no data)\n";
    } else {
        $kotime_status = $marketplace_status[$mp]['kotime'];
        $kotime_status_text = getStatusText($kotime_status);
        $message .= "  🟧 Kotime: " . $kotime_status_text . "\n";
    }
    $message .= "\n";
}

// Asta Acol
$message .= "══════ Asta Acol ═════\n";
$message .= "*Periode: " . date('d M Y', strtotime($current_month_start)) . " - " . date('d M Y', strtotime($current_month_end)) . "*\n";
$message .= "*Bulan: " . date('F Y', strtotime($current_month_start)) . "*\n";
$message .= "*Termasuk ASTA (Shopee, TikTok, Lazada, Blibli) dan Kotime (Shopee, TikTok, Lazada)*\n";
$message .= "*Ratio Limit: " . $ratio_limit . "%*\n";
$message .= "*Hanya faktur dengan ratio >{$ratio_limit}% yang dihitung (non-retur)*\n\n";

$message .= "📋 *KATEGORI BERTINGKAT:*\n";
$message .= "1. Belum Note: Ratio >{$ratio_limit}% & note kosong\n";
$message .= "2. Belum Check: Ratio >{$ratio_limit}% & note terisi & belum check\n";
$message .= "3. Butuh Final DIR: Ratio >{$ratio_limit}% & note terisi & sudah check & status dir bukan 'Final DIR'\n\n";

// Hitung total untuk ASTA dan Kotime (tanpa menampilkan retur)
$total_faktur_asta = 0;
$total_ratio_exceed_asta = 0;
$total_belum_note_asta = 0;
$total_belum_check_asta = 0;
$total_butuh_final_dir_asta = 0;
$total_invoice_asta = 0;
$total_diterima_asta = 0;

$total_faktur_kotime = 0;
$total_ratio_exceed_kotime = 0;
$total_belum_note_kotime = 0;
$total_belum_check_kotime = 0;
$total_butuh_final_dir_kotime = 0;
$total_invoice_kotime = 0;
$total_diterima_kotime = 0;

// Tampilkan per marketplace ASTA
$message .= "🟦 *ASTA HOMEWARE*\n";
foreach (['shopee', 'tiktok', 'lazada', 'blibli'] as $mp) {
    $data_key = 'asta_' . $mp;
    $data = $acol_data[$data_key];
    if ($data['total_faktur'] > 0) {
        // Hitung non-retur untuk keperluan persentase (tidak ditampilkan)
        $total_faktur_non_retur = $data['total_faktur'] - $data['total_faktur_retur'];
        $total_invoice_non_retur = $data['total_invoice'] - $data['total_invoice_retur'];
        $total_diterima_non_retur = $data['total_diterima'] - $data['total_diterima_retur'];
        $total_selisih_non_retur = $total_invoice_non_retur - $total_diterima_non_retur;
        $ratio = $total_invoice_non_retur > 0 ? ($total_selisih_non_retur / $total_invoice_non_retur * 100) : 0;

        $message .= "🛒 *" . strtoupper($mp) . "*\n";
        $message .= "• Total Faktur: " . number_format($data['total_faktur']) . "\n";
        $message .= "• Ratio >{$ratio_limit}%: " . number_format($data['ratio_exceed']) .
            " (" . number_format(($data['ratio_exceed'] / max($total_faktur_non_retur, 1) * 100), 1) . "% dari non-retur)\n";
        $message .= "• Belum Note: " . number_format($data['belum_note']) . "\n";
        $message .= "• Belum Check: " . number_format($data['belum_check']) . "\n";
        $message .= "• Butuh Final DIR: " . number_format($data['butuh_final_dir']) . "\n";
        $message .= "• Total Invoice: Rp " . number_format($total_invoice_non_retur) . "\n";
        $message .= "• Total Diterima: Rp " . number_format($total_diterima_non_retur) . "\n";
        $message .= "• Selisih: Rp " . number_format($total_selisih_non_retur) . "\n";
        $message .= "• Ratio: " . number_format($ratio, 2) . "%\n\n";

        // Akumulasi total ASTA (menggunakan non-retur untuk invoice/diterima)
        $total_faktur_asta += $data['total_faktur'];
        $total_ratio_exceed_asta += $data['ratio_exceed'];
        $total_belum_note_asta += $data['belum_note'];
        $total_belum_check_asta += $data['belum_check'];
        $total_butuh_final_dir_asta += $data['butuh_final_dir'];
        $total_invoice_asta += $total_invoice_non_retur;
        $total_diterima_asta += $total_diterima_non_retur;
    } else {
        $message .= "🛒 *" . strtoupper($mp) . "*\n";
        $message .= "• Tidak ada data faktur\n\n";
    }
}

// Tampilkan per marketplace Kotime
$message .= "🟧 *KOTIME*\n";
foreach (['shopee', 'tiktok', 'lazada'] as $mp) {
    $data_key = 'kotime_' . $mp;
    $data = $acol_data[$data_key];
    if ($data['total_faktur'] > 0) {
        $total_faktur_non_retur = $data['total_faktur'] - $data['total_faktur_retur'];
        $total_invoice_non_retur = $data['total_invoice'] - $data['total_invoice_retur'];
        $total_diterima_non_retur = $data['total_diterima'] - $data['total_diterima_retur'];
        $total_selisih_non_retur = $total_invoice_non_retur - $total_diterima_non_retur;
        $ratio = $total_invoice_non_retur > 0 ? ($total_selisih_non_retur / $total_invoice_non_retur) * 100 : 0;

        $message .= "🛒 *" . strtoupper($mp) . "*\n";
        $message .= "• Total Faktur: " . number_format($data['total_faktur']) . "\n";
        $message .= "• Ratio >{$ratio_limit}%: " . number_format($data['ratio_exceed']) .
            " (" . number_format(($data['ratio_exceed'] / max($total_faktur_non_retur, 1) * 100), 1) . "% dari non-retur)\n";
        $message .= "• Belum Note: " . number_format($data['belum_note']) . "\n";
        $message .= "• Belum Check: " . number_format($data['belum_check']) . "\n";
        $message .= "• Butuh Final DIR: " . number_format($data['butuh_final_dir']) . "\n";
        $message .= "• Total Invoice: Rp " . number_format($total_invoice_non_retur) . "\n";
        $message .= "• Total Diterima: Rp " . number_format($total_diterima_non_retur) . "\n";
        $message .= "• Selisih: Rp " . number_format($total_selisih_non_retur) . "\n";
        $message .= "• Ratio: " . number_format($ratio, 2) . "%\n\n";

        $total_faktur_kotime += $data['total_faktur'];
        $total_ratio_exceed_kotime += $data['ratio_exceed'];
        $total_belum_note_kotime += $data['belum_note'];
        $total_belum_check_kotime += $data['belum_check'];
        $total_butuh_final_dir_kotime += $data['butuh_final_dir'];
        $total_invoice_kotime += $total_invoice_non_retur;
        $total_diterima_kotime += $total_diterima_non_retur;
    } else {
        $message .= "🛒 *" . strtoupper($mp) . "*\n";
        $message .= "• Tidak ada data faktur\n\n";
    }
}

// Hitung grand total semua brand (tanpa menyebut retur)
$total_faktur_all = $total_faktur_asta + $total_faktur_kotime;
$total_ratio_exceed_all = $total_ratio_exceed_asta + $total_ratio_exceed_kotime;
$total_belum_note_all = $total_belum_note_asta + $total_belum_note_kotime;
$total_belum_check_all = $total_belum_check_asta + $total_belum_check_kotime;
$total_butuh_final_dir_all = $total_butuh_final_dir_asta + $total_butuh_final_dir_kotime;
$total_invoice_all = $total_invoice_asta + $total_invoice_kotime;
$total_diterima_all = $total_diterima_asta + $total_diterima_kotime;

if ($total_faktur_all > 0) {
    $selisih_all = $total_invoice_all - $total_diterima_all;
    $ratio_all = $total_invoice_all > 0 ? ($selisih_all / $total_invoice_all) * 100 : 0;

    $selisih_asta = $total_invoice_asta - $total_diterima_asta;
    $ratio_asta = $total_invoice_asta > 0 ? ($selisih_asta / $total_invoice_asta) * 100 : 0;

    $selisih_kotime = $total_invoice_kotime - $total_diterima_kotime;
    $ratio_kotime = $total_invoice_kotime > 0 ? ($selisih_kotime / $total_invoice_kotime) * 100 : 0;

    $message .= "📊 *SUMMARY PER BRAND*\n";

    // ASTA Summary (tanpa retur)
    $message .= "🟦 *ASTA HOMEWARE:*\n";
    $message .= "• Total Faktur: " . number_format($total_faktur_asta) . "\n";
    $non_retur_asta = $total_faktur_asta - array_sum(array_column(array_intersect_key($acol_data, array_flip(['asta_shopee','asta_tiktok','asta_lazada','asta_blibli'])), 'total_faktur_retur'));
    if ($non_retur_asta > 0) {
        $message .= "• Ratio >{$ratio_limit}%: " . number_format($total_ratio_exceed_asta) .
            " (" . number_format(($total_ratio_exceed_asta / $non_retur_asta * 100), 1) . "% dari non-retur)\n";
    } else {
        $message .= "• Ratio >{$ratio_limit}%: " . number_format($total_ratio_exceed_asta) . "\n";
    }
    $message .= "• Belum Note: " . number_format($total_belum_note_asta) . "\n";
    $message .= "• Belum Check: " . number_format($total_belum_check_asta) . "\n";
    $message .= "• Butuh Final DIR: " . number_format($total_butuh_final_dir_asta) . "\n";
    $message .= "• Total Invoice: Rp " . number_format($total_invoice_asta) . "\n";
    $message .= "• Total Diterima: Rp " . number_format($total_diterima_asta) . "\n";
    $message .= "• Selisih: Rp " . number_format($selisih_asta) . "\n";
    $message .= "• Ratio: " . number_format($ratio_asta, 2) . "%\n";
    $message .= "• Additional Revenue: Rp " . number_format($additional_revenue_asta) . "\n\n";

    // Kotime Summary
    $message .= "🟧 *KOTIME:*\n";
    $message .= "• Total Faktur: " . number_format($total_faktur_kotime) . "\n";
    $non_retur_kotime = $total_faktur_kotime - array_sum(array_column(array_intersect_key($acol_data, array_flip(['kotime_shopee','kotime_tiktok','kotime_lazada'])), 'total_faktur_retur'));
    if ($non_retur_kotime > 0) {
        $message .= "• Ratio >{$ratio_limit}%: " . number_format($total_ratio_exceed_kotime) .
            " (" . number_format(($total_ratio_exceed_kotime / $non_retur_kotime * 100), 1) . "% dari non-retur)\n";
    } else {
        $message .= "• Ratio >{$ratio_limit}%: " . number_format($total_ratio_exceed_kotime) . "\n";
    }
    $message .= "• Belum Note: " . number_format($total_belum_note_kotime) . "\n";
    $message .= "• Belum Check: " . number_format($total_belum_check_kotime) . "\n";
    $message .= "• Butuh Final DIR: " . number_format($total_butuh_final_dir_kotime) . "\n";
    $message .= "• Total Invoice: Rp " . number_format($total_invoice_kotime) . "\n";
    $message .= "• Total Diterima: Rp " . number_format($total_diterima_kotime) . "\n";
    $message .= "• Selisih: Rp " . number_format($selisih_kotime) . "\n";
    $message .= "• Ratio: " . number_format($ratio_kotime, 2) . "%\n";
    $message .= "• Additional Revenue: Rp " . number_format($additional_revenue_kotime) . "\n\n";

    // Grand Total
    $message .= "📈 *GRAND TOTAL (ASTA + KOTIME):*\n";
    $message .= "• Total Faktur: " . number_format($total_faktur_all) . "\n";
    $non_retur_all = $total_faktur_all - array_sum(array_column($acol_data, 'total_faktur_retur'));
    if ($non_retur_all > 0) {
        $message .= "• Ratio >{$ratio_limit}%: " . number_format($total_ratio_exceed_all) .
            " (" . number_format(($total_ratio_exceed_all / $non_retur_all * 100), 1) . "% dari non-retur)\n";
    } else {
        $message .= "• Ratio >{$ratio_limit}%: " . number_format($total_ratio_exceed_all) . "\n";
    }

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
    $message .= "• Additional Revenue Total: Rp " . number_format($additional_revenue_total) . "\n\n";
} else {
    $message .= "ℹ️ *Tidak ada data faktur untuk periode ini*\n\n";
}

// Critical Invoices
if (!empty($critical_invoices)) {
    $grouped_by_brand = [
        'ASTA' => [],
        'Kotime' => []
    ];

    foreach ($critical_invoices as $inv) {
        $brand = $inv['brand'];
        $category = $inv['category'];
        if (!isset($grouped_by_brand[$brand][$category])) {
            $grouped_by_brand[$brand][$category] = [];
        }
        $grouped_by_brand[$brand][$category][] = $inv;
    }

    $category_order = ['Belum Note', 'Belum Check', 'Butuh Final DIR'];

    $message .= "⚠️ *INVOICE YANG PERLU PERHATIAN:*\n";
    $message .= "Total: " . count($critical_invoices) . " invoice (ratio >{$ratio_limit}%)\n\n";

    foreach (['ASTA', 'Kotime'] as $brand) {
        if (!empty($grouped_by_brand[$brand])) {
            $brand_total = array_sum(array_map('count', $grouped_by_brand[$brand]));
            if ($brand_total > 0) {
                $message .= ($brand == 'ASTA' ? "🟦" : "🟧") . " *" . $brand . " (" . $brand_total . "):*\n";

                foreach ($category_order as $category) {
                    if (isset($grouped_by_brand[$brand][$category]) && !empty($grouped_by_brand[$brand][$category])) {
                        $invoices = $grouped_by_brand[$brand][$category];
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
        }
    }
}

// Total item yang perlu perhatian (hanya Acol)
$grand_total_acol = $total_belum_note_all + $total_belum_check_all + $total_butuh_final_dir_all;
$message .= "📋 *TOTAL ITEM YANG MEMBUTUHKAN PERHATIAN (ACOL):*\n";
$message .= "• Belum Note: " . $total_belum_note_all . "\n";
$message .= "• Belum Check: " . $total_belum_check_all . "\n";
$message .= "• Butuh Final DIR: " . $total_butuh_final_dir_all . "\n";
$message .= "• *Total: " . $grand_total_acol . " item*\n";
$message .= "━━━━━━━━━━━━━━━━━━\n";
$message .= "_Asta Homeware ERP | Report Date: " . $current_date . "_";

// ===============================================
// KIRIM VIA FONNTE API
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
// OUTPUT DEBUG (HANYA ACOL, TANPA RETUR)
// ===============================================
echo "📊 ASTA DAILY REPORT SYSTEM (ACOL ONLY, TANPA RETUR)\n";
echo "==================================================\n";
echo "Tanggal Eksekusi: " . date('Y-m-d H:i:s') . "\n";
echo "Token Fonnte: " . (strlen($token) > 10 ? substr($token, 0, 10) . "..." : $token) . "\n";
echo "Target: " . $targets . "\n\n";

echo "📋 PERIODE DATA:\n";
echo "----------------\n";
echo "ASTA ACOL: " . date('d M Y', strtotime($current_month_start)) . " - " . date('d M Y', strtotime($current_month_end)) . "\n";
echo "Bulan: " . date('F Y', strtotime($current_month_start)) . "\n";
echo "Termasuk: ASTA Homeware (Shopee, TikTok, Lazada, Blibli) dan Kotime (Shopee, TikTok, Lazada)\n";
echo "Ratio Limit: " . $ratio_limit . "%\n";
echo "Additional Revenue Total: Rp " . number_format($additional_revenue_total) . "\n";
echo "  - ASTA: Rp " . number_format($additional_revenue_asta) . "\n";
echo "  - Kotime: Rp " . number_format($additional_revenue_kotime) . "\n\n";

echo "📊 MARKETPLACE STATUS:\n";
echo "---------------------\n";
foreach (['shopee', 'tiktok', 'lazada', 'blibli'] as $mp) {
    echo strtoupper($mp) . ":\n";
    echo "  ASTA: " . getStatusText($marketplace_status[$mp]['asta']) . "\n";
    if ($mp == 'blibli') {
        echo "  Kotime: 📭 No Transaction (no data)\n";
    } else {
        echo "  Kotime: " . getStatusText($marketplace_status[$mp]['kotime']) . "\n";
    }
}
echo "\n";

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

echo "📊 DATA SUMMARY (ACOL):\n";
echo "------------------------\n";
echo "  🟦 ASTA HOMEWARE:\n";
foreach (['shopee', 'tiktok', 'lazada', 'blibli'] as $mp) {
    $data = $acol_data['asta_' . $mp];
    if ($data['total_faktur'] > 0) {
        echo "    " . strtoupper($mp) . ":\n";
        echo "      - Total Faktur: " . number_format($data['total_faktur']) . "\n";
        echo "      - Ratio >{$ratio_limit}%: " . number_format($data['ratio_exceed']) . "\n";
        echo "      - Belum Note: " . number_format($data['belum_note']) . "\n";
        echo "      - Belum Check: " . number_format($data['belum_check']) . "\n";
        echo "      - Butuh Final DIR: " . number_format($data['butuh_final_dir']) . "\n";
    } else {
        echo "    " . strtoupper($mp) . ": Tidak ada data\n";
    }
}

echo "  🟧 KOTIME:\n";
foreach (['shopee', 'tiktok', 'lazada'] as $mp) {
    $data = $acol_data['kotime_' . $mp];
    if ($data['total_faktur'] > 0) {
        echo "    " . strtoupper($mp) . ":\n";
        echo "      - Total Faktur: " . number_format($data['total_faktur']) . "\n";
        echo "      - Ratio >{$ratio_limit}%: " . number_format($data['ratio_exceed']) . "\n";
        echo "      - Belum Note: " . number_format($data['belum_note']) . "\n";
        echo "      - Belum Check: " . number_format($data['belum_check']) . "\n";
        echo "      - Butuh Final DIR: " . number_format($data['butuh_final_dir']) . "\n";
    } else {
        echo "    " . strtoupper($mp) . ": Tidak ada data\n";
    }
}

echo "\n📈 GRAND TOTAL ACOL:\n";
echo "  🟦 ASTA:\n";
echo "    - Total Faktur: " . number_format($total_faktur_asta) . "\n";
echo "    - Ratio >{$ratio_limit}%: " . number_format($total_ratio_exceed_asta) . "\n";
echo "    - Total Invoice: Rp " . number_format($total_invoice_asta) . "\n";
echo "    - Total Diterima: Rp " . number_format($total_diterima_asta) . "\n";
echo "    - Selisih: Rp " . number_format($total_invoice_asta - $total_diterima_asta) . "\n";
echo "    - Additional Revenue: Rp " . number_format($additional_revenue_asta) . "\n";

echo "  🟧 KOTIME:\n";
echo "    - Total Faktur: " . number_format($total_faktur_kotime) . "\n";
echo "    - Ratio >{$ratio_limit}%: " . number_format($total_ratio_exceed_kotime) . "\n";
echo "    - Total Invoice: Rp " . number_format($total_invoice_kotime) . "\n";
echo "    - Total Diterima: Rp " . number_format($total_diterima_kotime) . "\n";
echo "    - Selisih: Rp " . number_format($total_invoice_kotime - $total_diterima_kotime) . "\n";
echo "    - Additional Revenue: Rp " . number_format($additional_revenue_kotime) . "\n";

echo "  📊 TOTAL (ASTA + KOTIME):\n";
echo "    - Total Faktur: " . number_format($total_faktur_all) . "\n";
echo "    - Total Invoice: Rp " . number_format($total_invoice_all) . "\n";
echo "    - Total Diterima: Rp " . number_format($total_diterima_all) . "\n";
echo "    - Selisih: Rp " . number_format($total_invoice_all - $total_diterima_all) . "\n";
echo "    - Additional Revenue Total: Rp " . number_format($additional_revenue_total) . "\n";

echo "\n📋 ITEM YANG PERLU PERHATIAN (ACOL):\n";
echo "  - Belum Note: " . $total_belum_note_all . "\n";
echo "  - Belum Check: " . $total_belum_check_all . "\n";
echo "  - Butuh Final DIR: " . $total_butuh_final_dir_all . "\n";
echo "  - TOTAL: " . $grand_total_acol . "\n\n";

echo "📱 PANJANG PESAN: " . strlen($message) . " karakter\n";

$conn->close();
