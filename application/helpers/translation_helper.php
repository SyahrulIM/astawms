<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('translate_id_to_en')) {
    function translate_id_to_en($text)
    {
        if (empty(trim($text))) {
            return $text;
        }

        // Cache the translation to avoid repeated API calls
        $cache_key = 'translation_' . md5($text);

        // Try to get from cache first
        $ci = &get_instance();
        if ($ci->load->driver('cache', array('adapter' => 'file', 'backup' => 'dummy'))) {
            $cached = $ci->cache->get($cache_key);
            if ($cached !== FALSE) {
                return $cached;
            }
        }

        // MyMemory Translation API (Free)
        $api_url = "https://api.mymemory.translated.net/get";

        // Prepare parameters
        $params = [
            'q' => $text,
            'langpair' => 'id|en',
            'de' => 'suriadi@astablue.com', // Replace with your email
            'mt' => '1',
            'onlyprivate' => '0',
            'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1'
        ];

        // Build URL
        $url = $api_url . '?' . http_build_query($params);

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

        // Execute request
        $response = curl_exec($ch);

        $translated_text = $text; // Default to original

        if (!curl_errno($ch)) {
            $result = json_decode($response, true);

            if (isset($result['responseData']['translatedText'])) {
                $translated_text = $result['responseData']['translatedText'];

                // Clean up the translation
                $translated_text = html_entity_decode($translated_text, ENT_QUOTES | ENT_HTML5);
                $translated_text = trim($translated_text);
                $translated_text = strip_tags($translated_text);
            }
        }

        curl_close($ch);

        // Cache the translation for 30 days
        if (isset($ci->cache)) {
            $ci->cache->save($cache_key, $translated_text, 2592000); // 30 days
        }

        return $translated_text;
    }
}

if (!function_exists('simple_translate_id_to_en')) {
    function simple_translate_id_to_en($text)
    {
        // Simple dictionary-based translation as fallback
        $dictionary = [
            // Colors
            'merah' => 'red', 'biru' => 'blue', 'hijau' => 'green', 'kuning' => 'yellow',
            'hitam' => 'black', 'putih' => 'white', 'abu-abu' => 'gray', 'coklat' => 'brown',
            'emas' => 'gold', 'perak' => 'silver', 'ungu' => 'purple', 'pink' => 'pink',

            // Materials
            'katun' => 'cotton', 'sutra' => 'silk', 'wol' => 'wool', 'kulit' => 'leather',
            'plastik' => 'plastic', 'kayu' => 'wood', 'besi' => 'iron', 'baja' => 'steel',

            // Clothing
            'baju' => 'shirt', 'kemeja' => 'shirt', 'kaos' => 't-shirt', 'celana' => 'pants',
            'jeans' => 'jeans', 'rok' => 'skirt', 'jaket' => 'jacket', 'hoodie' => 'hoodie',
            'sweater' => 'sweater', 'dress' => 'dress', 'blouse' => 'blouse',

            // Footwear
            'sepatu' => 'shoes', 'sandal' => 'sandal', 'sendal' => 'sandal',

            // Accessories
            'tas' => 'bag', 'topi' => 'hat', 'dasi' => 'tie', 'kacamata' => 'glasses',

            // Sizes
            'kecil' => 'small', 'sedang' => 'medium', 'besar' => 'large',
            's' => 'S', 'm' => 'M', 'l' => 'L', 'xl' => 'XL', 'xxl' => 'XXL',

            // Common words
            'panjang' => 'long', 'pendek' => 'short', 'tipis' => 'thin', 'tebal' => 'thick',
            'baru' => 'new', 'lama' => 'old', 'ringan' => 'light', 'berat' => 'heavy',
            'murah' => 'cheap', 'mahal' => 'expensive', 'wanita' => 'women', 'pria' => 'men',
            'anak' => 'kids', 'model' => 'model', 'design' => 'design', 'style' => 'style',
        ];

        $result = $text;

        // Replace words (case insensitive)
        foreach ($dictionary as $id => $en) {
            $result = preg_replace('/\b' . preg_quote($id, '/') . '\b/i', $en, $result);
        }

        // Clean up
        $result = preg_replace('/\s+/', ' ', $result);
        $result = trim($result);
        $result = ucwords(strtolower($result));

        return $result;
    }
}
