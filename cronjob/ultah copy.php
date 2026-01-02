<?php

date_default_timezone_set('Asia/Jakarta');

/* =====================
   CONFIG
===================== */
$token = 'ZsZ2Dp71dyKrgz3YAQKg';

/* =====================
   DATA KARYAWAN (DISISAKAN)
===================== */
$employees = [
    ['name' => 'Suriadi Chiannger', 'date' => '28-09', 'phone' => '0816536516'],
    ['name' => 'Priaji Utomo', 'date' => '13-11', 'phone' => '08563557912'],
    ['name' => 'Virijani Rossanna', 'date' => '24-03', 'phone' => '081331090331'],
];

/* =====================
   NORMALISASI NO HP
===================== */
function normalizePhone($phone)
{
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (substr($phone, 0, 1) === '0') {
        $phone = '62' . substr($phone, 1);
    }
    return $phone;
}

/* =====================
   TANGGAL HARI INI
===================== */
$today = new DateTime(date('Y-m-d'));

/* =====================
   LOOP CEK ULTAH
===================== */
foreach ($employees as $emp) {

    [$day, $month] = explode('-', $emp['date']);

    $birthday = DateTime::createFromFormat(
        'Y-m-d',
        date('Y') . "-{$month}-{$day}"
    );

    $diffDays = (int) $today->diff($birthday)->format('%r%a');

    // HANYA H-2, H-1, H-0
    if (!in_array($diffDays, [-2, -1, 0])) {
        continue;
    }

    /* =====================
       TARGET WA (KECUALI YG ULTAH)
    ====================== */
    $excludePhone = normalizePhone($emp['phone']);
    $targets = [];

    foreach ($employees as $t) {
        if (normalizePhone($t['phone']) === $excludePhone) {
            continue;
        }
        $targets[] = normalizePhone($t['phone']);
    }

    if (empty($targets)) {
        continue;
    }

    $targets = implode(',', $targets);

    /* =====================
       MESSAGE
    ====================== */
    $message = "🎉 *ULTAH ALERT!* 🎉\n\n"
        . "Selamat siang rekan-rekan 🙏\n\n"
        . "Dalam rangka menyambut H-" . abs($diffDays) . " ulang tahun karyawan, "
        . "diadakan pengumpulan dana seikhlasnya.\n\n"
        . "🎯 *DETAIL ULTAH*\n"
        . "━━━━━━━━━━━━━━━━━━\n"
        . "👤 *Nama*    : {$emp['name']}\n"
        . "📅 *Tanggal* : {$day}-{$month}\n"
        . "━━━━━━━━━━━━━━━━━━\n\n"
        . "🔒 *Mohon dijaga kerahasiaannya* ya 🤓\n\n"
        . "_Asta Homeware ERP_";

    /* =====================
       SEND WA
    ====================== */
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => [
            'target' => $targets,
            'message' => $message,
            'countryCode' => '62',
        ],
        CURLOPT_HTTPHEADER => [
            'Authorization: ' . $token
        ],
    ]);

    curl_exec($curl);
    curl_close($curl);
}

echo "Cron birthday executed";
