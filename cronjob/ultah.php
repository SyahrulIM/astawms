<?php

date_default_timezone_set('Asia/Jakarta');

/* =====================
   CONFIG
===================== */
$token = 'ZsZ2Dp71dyKrgz3YAQKg';

/* =====================
   DATA KARYAWAN (TANGGAL-BULAN SAJA)
===================== */
$employees = [
    ['name' => 'M. Mustofa Khabib Bukhori al-Baghdadi', 'date' => '24-01', 'phone' => '085806241787'],
    ['name' => 'Adinda Gita Puspita', 'date' => '19-02', 'phone' => '085735096566'],
    ['name' => 'Virijani Rossanna', 'date' => '24-02', 'phone' => '081331090331'],
    ['name' => 'Achmad Huriansyah', 'date' => '02-03', 'phone' => '085755313101'],
    ['name' => 'Catur Ardiansyah Wulandana', 'date' => '23-03', 'phone' => '085731122858'],
    ['name' => 'Alida April Lia', 'date' => '08-04', 'phone' => '085733207227'],
    ['name' => 'Syahrul Izha Mahendra', 'date' => '18-05', 'phone' => '085156340619'],
    ['name' => 'Widiawati', 'date' => '31-05', 'phone' => '082141428660'],
    ['name' => 'Arya Ananduta Setyaki', 'date' => '16-06', 'phone' => '085926871752'],
    ['name' => 'Jean Kezia Lovitasari', 'date' => '16-06', 'phone' => '089523365649'],
    ['name' => 'Khodijah Atika', 'date' => '18-06', 'phone' => '085926871752'],
    ['name' => 'Adinda Dewi Zulfia Putri', 'date' => '27-06', 'phone' => '089612686399'],
    ['name' => 'Aditya Yuli Setyawan Pradana', 'date' => '16-07', 'phone' => '0895371819977'],
    ['name' => 'Candra Kurniasih', 'date' => '17-07', 'phone' => '085743103073'],
    ['name' => 'Michella Audry Anjarwati', 'date' => '20-07', 'phone' => '085856777414'],
    ['name' => 'Abah Suriadi Chiannger', 'date' => '28-09', 'phone' => '0816536516'],
    ['name' => 'Alfiyatur Rosida', 'date' => '02-10', 'phone' => '085816236056'],
    ['name' => 'Dwi Wahyu Nursanti', 'date' => '04-11', 'phone' => '089616460526'],
    ['name' => 'Priaji Utomo', 'date' => '13-11', 'phone' => '08563557912'],
    ['name' => 'Purwono', 'date' => '18-12', 'phone' => '083143115467'],
    ['name' => 'Mirza Adriansyah', 'date' => '30-12', 'phone' => '081215908797']
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
$today = new DateTime();
$currentDay = (int) $today->format('j');  // Tanggal (1-31)
$currentMonth = (int) $today->format('n'); // Bulan (1-12)
$currentYear = (int) $today->format('Y'); // Tahun

/* =====================
   DEBUG INFO
===================== */
echo "Tanggal hari ini: " . $today->format('d-m-Y') . "\n";
echo "-----------------------------\n";

/* =====================
   LOOP CEK ULTAH
===================== */
foreach ($employees as $emp) {
    // Parse tanggal ulang tahun
    list($bdayDay, $bdayMonth) = explode('-', $emp['date']);
    $bdayDay = (int) $bdayDay;
    $bdayMonth = (int) $bdayMonth;

    // Hitung hari menuju ulang tahun
    $daysUntil = days_until_birthday($currentDay, $currentMonth, $bdayDay, $bdayMonth);

    // Hanya proses jika H-2, H-1, atau H-0
    if ($daysUntil > 2 || $daysUntil < 0) {
        continue;
    }

    // Buat tanggal ulang tahun untuk tahun ini
    $birthdayDate = DateTime::createFromFormat(
        'd-m-Y',
        str_pad($bdayDay, 2, '0', STR_PAD_LEFT) . '-' .
            str_pad($bdayMonth, 2, '0', STR_PAD_LEFT) . '-' . $currentYear
    );

    // Jika ulang tahun sudah lewat di tahun ini, gunakan tahun depan
    if ($today > $birthdayDate) {
        $birthdayDate->modify('+1 year');
    }

    // Debug info
    echo "Nama: {$emp['name']}\n";
    echo "Ulang tahun: " . $birthdayDate->format('d-m-Y') . "\n";
    echo "Days until: {$daysUntil}\n";
    echo "-----------------------------\n";

    // Tentukan teks untuk hari
    $hariText = '';
    if ($daysUntil == 0) {
        $hariText = 'HARI INI';
    } elseif ($daysUntil == 1) {
        $hariText = 'BESOK';
    } elseif ($daysUntil == 2) {
        $hariText = 'LUSA';
    }

    /* =====================
       TARGET (KECUALI YG ULTAH)
    ====================== */
    $excludePhone = normalizePhone($emp['phone']);
    $targets = [];

    foreach ($employees as $t) {
        if (normalizePhone($t['phone']) === $excludePhone) continue;
        $targets[] = normalizePhone($t['phone']);
    }

    if (empty($targets)) continue;
    $targets = implode(',', $targets);

    /* =====================
       MESSAGE
    ====================== */
    $message = "🎉 *ULTAH ALERT!* 🎉\n\n"
        . "Selamat siang rekan-rekan Asta Homeware 🙏\n\n"
        . "Dalam rangka menyambut H-" . $daysUntil . " ulang tahun karyawan, "
        . "kita mengadakan pengumpulan dana seikhlasnya.\n\n"
        . "🎯 *DETAIL ULTAH*\n"
        . "━━━━━━━━━━━━━━━━━━\n\n"
        . "👤 Nama : {$emp['name']}\n"
        . "📅 Tgl  : {$birthdayDate->format('d-m-Y')}\n"
        . "⏰ Status : {$hariText}\n"
        . "━━━━━━━━━━━━━━━━━━\n\n"
        . "😉 Mohon dijaga kerahasiaannya ya\n"
        . "💳 *INFO TRANSFER*\n"
        . "🏦 Bank : BCA\n"
        . "🔢 No Rek : 5105013938\n"
        . "👩 A.n : Jean Kezia Lovitasari\n"
        . "━━━━━━━━━━━━━━━━━━\n\n"
        . "🙏 *TERIMA KASIH*\n"
        . "Atas partisipasi dan kerja sama rekan-rekan semua kami ucapkan terima kasih😉\n"
        . "━━━━━━━━━━━━━━━━━━\n\n"
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

    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        echo 'Curl error: ' . curl_error($curl) . "\n";
    }
    curl_close($curl);

    echo "Mengirim untuk: {$emp['name']} (H-{$daysUntil})\n";
    echo "Response: " . $response . "\n";
    echo "-----------------------------\n";
}

echo "Cron birthday selesai - " . date('d-m-Y H:i:s') . "\n";

/* =====================
   FUNGSI BANTU: HITUNG HARI MENUJU ULTAH
===================== */
function days_until_birthday($currentDay, $currentMonth, $bdayDay, $bdayMonth)
{
    // Jika bulan ulang tahun sama dengan bulan sekarang
    if ($bdayMonth == $currentMonth) {
        // Jika tanggal ulang tahun sama dengan hari ini
        if ($bdayDay == $currentDay) {
            return 0; // H-0 (hari ini ulang tahun)
        }
        // Jika ulang tahun dalam beberapa hari ke depan
        elseif ($bdayDay > $currentDay) {
            return $bdayDay - $currentDay; // H-1, H-2, dst
        }
        // Jika ulang tahun sudah lewat bulan ini, hitung untuk tahun depan
        else {
            // Hitung sisa hari di bulan ini
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, date('Y'));
            $daysLeftInMonth = $daysInMonth - $currentDay;
            // Tambahkan hari dari bulan depan sampai bulan ulang tahun
            $monthsUntil = 12 - $currentMonth + $bdayMonth;
            $totalDays = $daysLeftInMonth + $bdayDay;

            // Kurangi 1 bulan jika kita melewati tahun baru
            if ($monthsUntil > 1) {
                // Tambahkan hari dari bulan-bulan antara
                for ($i = 1; $i < $monthsUntil; $i++) {
                    $nextMonth = ($currentMonth + $i) % 12;
                    if ($nextMonth == 0) $nextMonth = 12;
                    $totalDays += cal_days_in_month(CAL_GREGORIAN, $nextMonth, date('Y'));
                }
            }

            return $totalDays;
        }
    }
    // Jika bulan ulang tahun lebih besar dari bulan sekarang
    elseif ($bdayMonth > $currentMonth) {
        // Hitung sisa hari di bulan ini
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, date('Y'));
        $daysLeftInMonth = $daysInMonth - $currentDay;

        // Hitung total hari
        $totalDays = $daysLeftInMonth;

        // Tambahkan hari dari bulan antara
        for ($month = $currentMonth + 1; $month < $bdayMonth; $month++) {
            $totalDays += cal_days_in_month(CAL_GREGORIAN, $month, date('Y'));
        }

        // Tambahkan hari ulang tahun
        $totalDays += $bdayDay;

        return $totalDays;
    }
    // Jika bulan ulang tahun sudah lewat tahun ini (tahun depan)
    else {
        // Hitung sisa hari di bulan ini
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, date('Y'));
        $daysLeftInMonth = $daysInMonth - $currentDay;

        // Hitung total hari
        $totalDays = $daysLeftInMonth;

        // Tambahkan hari dari sisa bulan tahun ini
        for ($month = $currentMonth + 1; $month <= 12; $month++) {
            $totalDays += cal_days_in_month(CAL_GREGORIAN, $month, date('Y'));
        }

        // Tambahkan hari dari bulan Januari sampai bulan ulang tahun tahun depan
        for ($month = 1; $month < $bdayMonth; $month++) {
            $nextYear = date('Y') + 1;
            $totalDays += cal_days_in_month(CAL_GREGORIAN, $month, $nextYear);
        }

        // Tambahkan hari ulang tahun
        $totalDays += $bdayDay;

        return $totalDays;
    }
}
