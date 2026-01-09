<?php
header('Content-Type: application/json');
require_once 'paypal_config.php';

$input = json_decode(file_get_contents('php://input'), true);
$amount = $input['amount'] ?? null;

if ($amount === null || $amount === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing amount']);
    exit;
}

$amount = number_format((float)$amount, 2, '.', '');

function getAccessToken()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_API . "/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ":" . PAYPAL_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        curl_close($ch);
        http_response_code(500);
        echo json_encode(['error' => 'cURL Error: ' . curl_error($ch)]);
        exit;
    }

    curl_close($ch);

    if ($httpCode >= 400) {
        http_response_code($httpCode);
        $result = json_decode($response, true);
        echo json_encode(['error' => 'PayPal auth failed (HTTP ' . $httpCode . ')', 'details' => $result]);
        exit;
    }

    $result = json_decode($response, true);
    if (!isset($result['access_token'])) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to retrieve access token', 'details' => $result]);
        exit;
    }

    return $result['access_token'];
}

$token = getAccessToken();

$orderData = [
    'intent' => 'CAPTURE',
    'purchase_units' => [[
        'amount' => ['currency_code' => 'PHP', 'value' => $amount]
    ]]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, PAYPAL_API . "/v2/checkout/orders");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    curl_close($ch);
    http_response_code(502);
    echo json_encode(['error' => 'cURL Error: ' . curl_error($ch)]);
    exit;
}

curl_close($ch);

if ($httpCode >= 400) {
    http_response_code($httpCode);
    $responseData = json_decode($response, true);
    if ($responseData) {
        echo json_encode($responseData);
    } else {
        echo json_encode(['error' => 'PayPal API error (HTTP ' . $httpCode . ')', 'raw' => substr($response, 0, 200)]);
    }
    exit;
}

$responseData = json_decode($response, true);
if (!$responseData) {
    http_response_code(502);
    echo json_encode(['error' => 'Invalid JSON from PayPal', 'raw' => substr($response, 0, 200)]);
    exit;
}

echo json_encode($responseData);
