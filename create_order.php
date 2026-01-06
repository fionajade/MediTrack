<?php
$clientId = "YOUR_SANDBOX_CLIENT_ID";
$secret   = "YOUR_SANDBOX_SECRET";

$data = json_decode(file_get_contents("php://input"), true);

$total = 0;
foreach ($data['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

$ch = curl_init("https://api-m.sandbox.paypal.com/v2/checkout/orders");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_USERPWD => "$clientId:$secret",
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
  CURLOPT_POSTFIELDS => json_encode([
    "intent" => "CAPTURE",
    "purchase_units" => [[
      "amount" => [
        "currency_code" => "PHP",
        "value" => number_format($total, 2, '.', '')
      ]
    ]]
  ])
]);

echo curl_exec($ch);
curl_close($ch);
