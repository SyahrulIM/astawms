<?php

$servername = "103.163.138.82";
$username = "astahome_it";
$password = "astawms=d17d09";
$dbname = "astahome_wms";
$conn = new mysqli($servername, $username, $password, $dbname);
$token = 'F9C6K2!5QYkZtW7j5z#M';

$sql = "SELECT handphone FROM user WHERE email = 'chalung.izha@gmail.com' LIMIT 1";
$result = $conn->query($sql);
$row_hp = $result->fetch_assoc();
$targets = $row_hp['handphone'];

$sql1 = "
(
    SELECT no_faktur, 'tiktok' AS source
    FROM acc_tiktok_detail
    WHERE DATE(updated_date) <> CURDATE()
    AND order_date >= (NOW() - INTERVAL 15 DAY)
)
UNION ALL
(
    SELECT no_faktur, 'shopee' AS source
    FROM acc_shopee_detail
    WHERE DATE(updated_date) <> CURDATE()
    AND order_date >= (NOW() - INTERVAL 15 DAY)
)
";

$result1 = $conn->query($sql1);

$message = "Dalam rentang 15 hari semua terupdate";

if ($result1) {
    $total_pending = $result1->num_rows;

    if ($total_pending > 0) {
        $message = "Total faktur yg belum diupdate rentang 15 hari terakhir: $total_pending\nDiantaranya:\n";

        while ($row_data = $result1->fetch_assoc()) {
            $message .= "- " . $row_data['no_faktur'] . " (" . $row_data['source'] . ")\n";
        }
    }
}

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
