<?php

$token = 'EyuhsmTqzeKaDknoxdxt';
$message = 'Test';
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
$response = curl_exec($curl);
curl_close($curl);
echo $response;

?>
