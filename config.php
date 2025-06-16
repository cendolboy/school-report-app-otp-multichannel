<?php
// Kode merchant tetap (biasanya sama untuk semua metode)
define('MERCHANT_KEY', 'isi dengan marchant key dari fazpass');

// Endpoint API Fazpass
define('API_URL_SEND', 'https://api.fazpass.com/v1/otp/request');
define('API_URL_VERIFY', 'https://api.fazpass.com/v1/otp/verify');

// Daftar metode pengiriman dan masing-masing gateway_key
$otp_gateways = [
    'sms'      => 'isi dengan gateway api key dari fazpass',
    'whatsapp' => 'isi dengan gateway api key dari fazpass',
    'telepon'  => 'isi dengan gateway api key dari fazpass',
    'misscall' => 'isi dengan gateway api key dari fazpass',
];

// Default method fallback
define('DEFAULT_METHOD', 'whatsapp');

/**
 * Fungsi validasi dan pengambilan gateway_key berdasarkan metode OTP
 * @param string $method Metode yang dipilih user
 * @return array [method => valid method, gateway_key => string]
 */
function get_valid_method_and_key($method) {
    global $otp_gateways;

    $method = strtolower(trim($method));
    if (array_key_exists($method, $otp_gateways)) {
        return ['method' => $method, 'gateway_key' => $otp_gateways[$method]];
    }

    // fallback jika tidak valid
    return ['method' => DEFAULT_METHOD, 'gateway_key' => $otp_gateways[DEFAULT_METHOD]];
}
?>
