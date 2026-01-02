<?php

date_default_timezone_set('Asia/Jakarta');

/* =====================
   CONFIG
===================== */
$token = 'ZsZ2Dp71dyKrgz3YAQKg';

/* =====================
   DATA KARYAWAN
===================== */
$employees = [
    ['name' => 'M. Mustofa Khabib Bukhori al-Baghdadi', 'date' => '24-01', 'phone' => '085806241787'],
    ['name' => 'Adinda Gita Puspita', 'date' => '19-02', 'phone' => '085735096566'],
    ['name' => 'Virijani Rossanna', 'date' => '24-02', 'phone' => '081331090331'],
    ['name' => 'Catur Ardiansyah Wulandana', 'date' => '23-03', 'phone' => '085731122858'],
    ['name' => 'Alida April Lia', 'date' => '08-04', 'phone' => '085733207227'],
    ['name' => 'Imroatin Fauziah', 'date' => '16-04', 'phone' => '085755692019'],
    ['name' => 'Syahrul Izha Mahendra', 'date' => '18-05', 'phone' => '085156340619'],
    ['name' => 'Widiawati', 'date' => '31-05', 'phone' => '082141428660'],
    ['name' => 'Khodijah Atika', 'date' => '18-06', 'phone' => '085926871752'],
    ['name' => 'Adinda Dewi Zulfia Putri', 'date' => '27-06', 'phone' => '089612686399'],
    ['name' => 'Aditya Yuli Setyawan Pradana', 'date' => '16-07', 'phone' => '0895371819977'],
    ['name' => 'Candra Kurniasih', 'date' => '17-07', 'phone' => '085743103073'],
    ['name' => 'Michella Audry Anjarwati', 'date' => '20-07', 'phone' => '085856777414'],
    ['name' => 'Suriadi Chiannger', 'date' => '28-09', 'phone' => '0816536516'],
    ['name' => 'Alfiyatur Rosida', 'date' => '02-10', 'phone' => '085816236056'],
    ['name' => 'Dwi Wahyu Nursanti', 'date' => '04-11', 'phone' => '896-1646-0526'],
    ['name' => 'Purwono', 'date' => '18-12', 'phone' => '083143115467'],
    ['name' => 'Eka Sandra Khairun Nisa', 'date' => '24-12', 'phone' => '081340238155'],
    ['name' => 'Mirza Adriansyah', 'date' => '30-12', 'phone' => '081215908797'],
];

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

    // hanya H-2, H-1, H-0
    if (!in_array($diffDays, [-2, -1, 0])) {
        continue;
    }

    /* =====================
       TARGET (KECUALI YG ULTAH)
    ====================== */
    $targets = [];

    foreach ($employees as $t) {
        if ($t['phone'] == $emp['phone']) continue;

        $phone = preg_replace('/[^0-9]/', '', $t['phone']);
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }

        $targets[] = $phone;
    }

    if (empty($targets)) continue;

    $targets = implode(',', $targets);

    /* =====================
       MESSAGE
    ====================== */
    $message = "🎉 *ULTAH ALERT!* 🎉\n\n"
        . "Selamat siang rekan-rekan Asta Homeware 🙏\n\n"
        . "Dalam rangka menyambut H-" . abs($diffDays) . " ulang tahun karyawan, "
        . "kita mengadakan pengumpulan dana seikhlasnya.\n\n"
        . "🎯 *DETAIL ULTAH*\n"
        . "━━━━━━━━━━━━━━━━━━\n"
        . "👤 Nama : {$emp['name']}\n"
        . "📅 Tgl  : {$day}-{$month}\n"
        . "━━━━━━━━━━━━━━━━━━\n\n"
        . "🔒 Mohon dijaga kerahasiaannya ya 🤓\n\n"
        . "💳 *INFO TRANSFER*\n"
        . "🏦 Bank : BCA\n"
        . "🔢 No Rek : 6720711981\n"
        . "👩 A.n : Imroatin Fauziah\n\n"
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

echo "Cron birthday success";
