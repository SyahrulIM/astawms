<?php

$servername = "103.163.138.82";
$username = "astahome_it";
$password = "astawms=d17d09";
$dbname = "astahome_wms";
$conn = new mysqli($servername, $username, $password, $dbname);
$token = 'EyuhsmTqzeKaDknoxdxt';

$sql = "SELECT handphone FROM user WHERE email = 'chalung.izha@gmail.com' LIMIT 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$targets = $row['handphone'];

$sql1 = "
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

    ORDER BY tanggal DESC, jam DESC
";
$result1 = $conn->query($sql1);
$total_pending = $result1->num_rows;
$message = "\nTotal pending verifikasi: " . $total_pending;

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
