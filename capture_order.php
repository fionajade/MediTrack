<?php
include 'connect.php';
include 'sms.php';

$clientId = "YOUR_SANDBOX_CLIENT_ID";
$secret   = "YOUR_SANDBOX_SECRET";

$data = json_decode(file_get_contents("php://input"), true);
$orderID = $data['orderID'];

$ch = curl_init("https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderID/capture");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERPWD => "$clientId:$secret",
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"]
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result['status']) && $result['status'] === "COMPLETED") {


    // SMS
    $smsMessage = "Your MediTrack order has been successfully placed. Thank you for choosing MediTrack!";
    sendSMS($data['contact'], $smsMessage);

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}
