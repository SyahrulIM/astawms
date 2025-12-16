<?php

$servername = "103.163.138.82";
$username = "astahome_it";
$password = "astawms=d17d09";
$dbname = "astahome_wms";
$conn = new mysqli($servername, $username, $password, $dbname);
$token = 'ZsZ2Dp71dyKrgz3YAQKg';

$targets = '6285156340619, 6281331090331, 62816536516, 6285755313101, 62895371819977, 6285816236056,  6285731122858, 6285743103073, 6289612686399, 6289616460526, 6285735096566, 6285733207227, 6285856777414, 6281215908797, 6285806241787, 628563557912, 6281340238155, 6285926871752, 6285755692019, 6282141428660';

$message = "🎉 *ULTAH ALERT!* 🎉\n\n"
    . "Selamat siang rekan-rekan Asta Homeware 🙏\n\n"
    . "Dalam rangka menyambut H-2 ulang tahun karyawan, sesuai agenda kita mengadakan pengumpulan dana dengan nominal seikhlasnya.\n\n"
    . "Bersama ini kami informasikan bahwa dalam waktu dekat:\n\n"
    . "🎯 *DETAIL ULTAH* 🎯\n"
    . "━━━━━━━━━━━━━━━━━━\n"
    . "👤 *Nama*       : Purwono\n"
    . "📅 *Tanggal*    : 18 Desember 2025\n"
    . "━━━━━━━━━━━━━━━━━━\n\n"
    . "Kami mengajak seluruh rekan-rekan yang berkenan untuk ikut berpartisipasi dalam pengumpulan dana ini sebagai bentuk perhatian kita bersama.\n\n"
    . "🔒 *Mohon dijaga kerahasiaannya* ya teman-teman, agar ini bisa menjadi kejutan yang spesial untuk beliau hehe 🤓\n\n"
    . "Untuk transfer partisipasi dapat dilakukan ke rekening:\n\n"
    . "💳 *INFO TRANSFER* 💳\n"
    . "━━━━━━━━━━━━━━━━━━\n"
    . "🏦 *Bank*      : BCA\n"
    . "🔢 *No. Rek*   : 6720711981\n"
    . "👩 *Atas Nama* : Imroatin Fauziah\n"
    . "━━━━━━━━━━━━━━━━━━\n\n"
    . "🙏 *TERIMA KASIH* 🙏\n"
    . "Terima kasih atas partisipasi dan kerja sama rekan-rekan semua 😉\n\n"
    . "━━━━━━━━━━━━━━━━━━\n"
    . "_Asta Homeware ERP_";

echo nl2br($message);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.fonnte.com/send',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
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
if ($response === false) {
    echo 'Curl error: ' . curl_error($curl);
} else {
    echo 'Message sent successfully!';
}
curl_close($curl);
