<?php
// cron_send_login.php - For cronjob

date_default_timezone_set('Asia/Jakarta');

$token = 'ZsZ2Dp71dyKrgz3YAQKg';

$recipients = [
    ['name' => 'Aditya', 'phone' => '0895371819977', 'user' => 'Adi', 'pass' => 'adi456'],
    ['name' => 'Aji', 'phone' => '08563557912', 'user' => 'Aji', 'pass' => 'aji456'],
    ['name' => 'Alfi', 'phone' => '085816236056', 'user' => 'Alfi', 'pass' => 'Sama kayak WMS Mbak'],
    ['name' => 'Atika', 'phone' => '085926871752', 'user' => 'Atika', 'pass' => 'atika456'],
    ['name' => 'Catur', 'phone' => '085731122858', 'user' => 'Catur', 'pass' => 'catur456'],
    ['name' => 'Purwono', 'phone' => '083143115467', 'user' => 'Purwono', 'pass' => 'purwono456'],
    ['name' => 'Widiawati', 'phone' => '082141428660', 'user' => 'Widia', 'pass' => 'widia456'],
];

$log = date('Y-m-d H:i:s') . " ======= START LOGIN CREDENTIALS SENDING =======\n";
$log .= "Total recipients: " . count($recipients) . "\n\n";

$success_count = 0;
$fail_count = 0;

foreach ($recipients as $recipient) {

    $message = "Sore " . $recipient['name'] . ",\n\n"
        . "Sekarang Asta People (aplikasi web Asta untuk personalia, absensi, tools, dan inventory) sudah bisa diakses online dari mana saja.\n"
        . "Berikut akun untuk login:\n\n"
        . "Website: https://people.astahomeware.com/\n"
        . "Username: " . $recipient['user'] . "\n"
        . "Password: " . $recipient['pass'] . "\n\n"
        . "Silakan dicoba. Kalau ada kendala, pertanyaan, dan masukan langsung sampaikan saja, atas perhatiannya saya ucapkan terima kasih 🙏🏾."
        . "_Asta Homeware ERP_";

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
            'target' => $recipient['phone'],
            'message' => $message,
            'delay' => '2',
            'countryCode' => '62',
        ),
        CURLOPT_HTTPHEADER => array(
            'Authorization: ' . $token
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($response, true);

    if ($data && isset($data['status']) && $data['status'] == true) {
        $log .= "✓ " . $recipient['name'] . " (" . $recipient['phone'] . "): Success\n";
        $success_count++;
    } else {
        $log .= "✗ " . $recipient['name'] . " (" . $recipient['phone'] . "): Failed - " . $response . "\n";
        $fail_count++;
    }

    sleep(2);
}

$log .= "\n" . date('Y-m-d H:i:s') . " ======= FINISHED =======\n";
$log .= "Success: " . $success_count . "\n";
$log .= "Failed: " . $fail_count . "\n";
$log .= "Total: " . ($success_count + $fail_count) . "\n";

// Save log
file_put_contents('/path/to/logs/whatsapp_login.log', $log, FILE_APPEND);

// Output for cron
echo $log;
