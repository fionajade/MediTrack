<?php
include("connect.php"); 
ini_set('display_errors', 1);
error_reporting(E_ALL);

$rawData = file_get_contents('php://input');
$postData = $_POST;

$data = json_decode($rawData, true);
if (!is_array($data)) {
    $data = $postData; 
}

$from = $data['from'] ?? $data['sender'] ?? 'Unknown';
$message = $data['message'] ?? $data['text'] ?? $data['content'] ?? 'No Message';
$received = $data['timestamp'] ?? $data['time'] ?? date("Y-m-d H:i:s");

$order_id = null;
$payment_id = null;

$orderQuery = $conn->prepare("
    SELECT order_id, payment_id
    FROM orders
    WHERE contact = ?
    ORDER BY created_at DESC
    LIMIT 1
");
$orderQuery->bind_param("s", $from);
$orderQuery->execute();
$orderQuery->bind_result($foundOrderId, $foundPaymentId);
if ($orderQuery->fetch()) {
    $order_id = $foundOrderId;       
    $payment_id = $foundPaymentId;   
}
$orderQuery->close();

$stmt = $conn->prepare("
    INSERT INTO sms_incoming (sender, message, received_at, order_id, payment_id)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("sssis", $from, $message, $received, $order_id, $payment_id);

if ($stmt->execute()) {
} else {
    file_put_contents(__DIR__ . "/sms_log.txt", "[".date("Y-m-d H:i:s")."] DB ERROR: ".$stmt->error.PHP_EOL, FILE_APPEND);
}

$logFile = __DIR__ . "/sms_log.txt";
$timestamp = date("Y-m-d H:i:s");
file_put_contents($logFile, "[$timestamp] FROM: $from MESSAGE: $message ORDER_ID: $order_id PAYMENT_ID: $payment_id RAW: $rawData" . PHP_EOL, FILE_APPEND);

http_response_code(200);
echo json_encode(["status" => "ok"]);

$stmt->close();
$conn->close();
?>
