<?php
header('Content-Type: application/json');
require_once 'paypal-config.php';

$orderID = $_GET['orderID'] ?? '';
if (!$orderID) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing orderID']);
    exit;
}

function getAccessToken()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_API . "/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ":" . PAYPAL_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        http_response_code(500);
        echo json_encode(['error' => curl_error($ch)]);
        exit;
    }

    $tokenData = json_decode($result, true);
    if (!isset($tokenData['access_token'])) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to retrieve access token', 'details' => $tokenData]);
        exit;
    }

    return $tokenData['access_token'];
}

$token = getAccessToken();

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, PAYPAL_API . "/v2/checkout/orders/" . urlencode($orderID) . "/capture");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    http_response_code(502);
    echo json_encode(['error' => curl_error($ch)]);
    exit;
}

$responseData = json_decode($response, true);
if (!$responseData) {
    echo $response;
    exit;
}

echo json_encode($responseData);
