<?php
function sendSMS($number, $message) {
    $apiKey = "YOUR_SEMAPHORE_API_KEY";

    // Basic PH number cleanup
    $number = preg_replace('/[^0-9]/', '', $number);
    if (substr($number, 0, 2) !== "63") {
        $number = "63" . substr($number, -10);
    }

    $ch = curl_init("https://semaphore.co/api/v4/messages");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => [
            'apikey'  => $apiKey,
            'number'  => $number,
            'message' => $message
        ]
    ]);

    curl_exec($ch);
    curl_close($ch);
}
