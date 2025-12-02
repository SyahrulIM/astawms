<?php

if (!function_exists('translate_to_english')) {
    function translate_to_english($text)
    {
        // Basic validation
        if (empty($text) || !is_string($text)) {
            return $text;
        }

        // Config: override by defining constants in your config/ENV
        $url = defined('LIBRETRANSLATE_URL') ? LIBRETRANSLATE_URL : 'https://libretranslate.com/translate';
        $api_key = defined('LIBRETRANSLATE_KEY') ? LIBRETRANSLATE_KEY : null;

        // Prepare payload as form-encoded (more compatible with many instances)
        $payload = [
            'q'      => $text,
            'source' => 'id',
            'target' => 'en',
            'format' => 'text'
        ];

        if (!empty($api_key)) {
            $payload['api_key'] = $api_key;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        // use form-encoded body (application/x-www-form-urlencoded)
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // follow redirects just in case
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // timeout (seconds)
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        // set header for form-encoded
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        // Optional: if your server has self-signed cert or issues, you can disable verify (not recommended for prod)
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($ch);

        // Curl error handling
        if ($result === false) {
            // You can log: curl_error($ch)
            curl_close($ch);
            return $text; // fallback: return original
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code < 200 || $http_code >= 300) {
            // non-successful response
            return $text;
        }

        $response = json_decode($result, true);

        if (!is_array($response) || !isset($response['translatedText'])) {
            // Some instances return different shapes or error messages; fallback safe
            return $text;
        }

        return $response['translatedText'] ?? $text;
    }
}
