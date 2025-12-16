<?php

$servername = "103.163.138.82";
$username = "astahome_it";
$password = "astawms=d17d09";
$dbname = "astahome_wms";
$conn = new mysqli($servername, $username, $password, $dbname);
$token = 'ZsZ2Dp71dyKrgz3YAQKg';

$targets = '6281331090331-1528429522@g.us';

$sql1 = "";

$result1 = $conn->query($sql1);

$message = "Asta Homeware Daily Report:\n\n"
    . "Asta WMS:\n"
    . "#Verifikasi Instock: \n"
    . "-Test01\n"
    . "-Test01\n"
    . "-Test01\n\n"
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

curl_exec($curl);
curl_close($curl);
