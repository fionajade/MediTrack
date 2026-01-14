<?php
include("connect.php"); // your mysqli connection ($conn)

// Enable error reporting for debugging (optional)
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
$orderQuery = $conn->prepare("SELECT order_id FROM orders WHERE contact = ? ORDER BY created_at DESC LIMIT 1");
$orderQuery->bind_param("s", $from);
$orderQuery->execute();
$orderQuery->bind_result($foundOrderId);
if ($orderQuery->fetch()) {
    $order_id = $foundOrderId; // link SMS to this order
}
$orderQuery->close();

// --- 5. Insert into sms_incoming with optional order_id ---
$stmt = $conn->prepare("INSERT INTO sms_incoming (sender, message, received_at, order_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $from, $message, $received, $order_id);
if ($stmt->execute()) {
    // Success (optional logging)
} else {
    file_put_contents(__DIR__ . "/sms_log.txt", "[".date("Y-m-d H:i:s")."] DB ERROR: ".$stmt->error.PHP_EOL, FILE_APPEND);
}

// --- 6. Write to log ---
$logFile = __DIR__ . "/sms_log.txt";
$timestamp = date("Y-m-d H:i:s");
file_put_contents($logFile, "[$timestamp] FROM: $from MESSAGE: $message ORDER_ID: $order_id RAW: $rawData" . PHP_EOL, FILE_APPEND);

// --- 7. Send response ---
http_response_code(200);
echo json_encode(["status" => "ok"]);

// Close statement and connection
$stmt->close();
$conn->close();
?>
