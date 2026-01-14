<?php
include("connect.php"); // your mysqli connection ($conn)

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- 1. Get raw input and POST data ---
$rawData = file_get_contents('php://input');
$postData = $_POST;

// --- 2. Decode JSON if available, otherwise fallback to POST ---
$data = json_decode($rawData, true);
if (!is_array($data)) {
    $data = $postData; // fallback if not JSON
}

// --- 3. Extract sender and message with multiple possible keys ---
$from = $data['from'] ?? $data['sender'] ?? 'Unknown';
$message = $data['message'] ?? $data['text'] ?? $data['content'] ?? 'No Message';
$received = $data['timestamp'] ?? $data['time'] ?? date("Y-m-d H:i:s");

// --- 4. Try to find the latest order for this sender ---
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
    $order_id = $foundOrderId;       // link SMS to this order
    $payment_id = $foundPaymentId;   // also link payment reference
}
$orderQuery->close();

// --- 5. Insert into sms_incoming with order_id and payment_id ---
$stmt = $conn->prepare("
    INSERT INTO sms_incoming (sender, message, received_at, order_id, payment_id)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("sssis", $from, $message, $received, $order_id, $payment_id);

if ($stmt->execute()) {
    // Optional: log success
} else {
    file_put_contents(__DIR__ . "/sms_log.txt", "[".date("Y-m-d H:i:s")."] DB ERROR: ".$stmt->error.PHP_EOL, FILE_APPEND);
}

// --- 6. Write to general log ---
$logFile = __DIR__ . "/sms_log.txt";
$timestamp = date("Y-m-d H:i:s");
file_put_contents($logFile, "[$timestamp] FROM: $from MESSAGE: $message ORDER_ID: $order_id PAYMENT_ID: $payment_id RAW: $rawData" . PHP_EOL, FILE_APPEND);

// --- 7. Send response ---
http_response_code(200);
echo json_encode(["status" => "ok"]);

// Close statement and connection
$stmt->close();
$conn->close();
?>
