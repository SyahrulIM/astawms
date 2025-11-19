<?php

// Database
$servername = "103.163.138.82";
$username = "astahome_it";
$password = "astawms=d17d09";
$dbname = "nama_database_kamu"; // <-- GANTI INI

$conn = new mysqli($servername, $username, $password, $dbname);

$token = 'EyuhsmTqzeKaDknoxdxt';
$message = 'Surat Jalan dengan nomor ' . $id . ' dibuat oleh ' . $username . ' sudah diverifikasi dan sekarang membutuhkan validasi dari bagian accounting di WMS. Mohon segera diproses, terima kasih.';

$target = '85156340619';

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.fonnte.com/send',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => array(
        'target' => $target,
        'message' => $message,
        'countryCode' => '62',
    ),
    CURLOPT_HTTPHEADER => array(
        'Authorization: ' . $token
    ),
));

curl_exec($curl);
curl_close($curl);
