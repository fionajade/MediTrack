<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("connect.php");
session_start();
header('Content-Type: application/json');

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$error = "";
$successMessage = "";

// Make sure user is logged in
$userID = $_SESSION['userID'] ?? null;
if (!$userID) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No data received']);
    exit;
}

$paymentID = $data['paymentID'] ?? '';
$cart = $data['cart'] ?? [];
$total = $data['total'] ?? '';
$name = $data['name'] ?? '';
$contact = $data['contact'] ?? '';
$address = $data['address'] ?? '';
$email = $data['email'] ?? $_SESSION['email'] ?? null; // Get email from POST or session

// Validate required fields
if (!$paymentID || !$cart || !$total || !$name || !$contact || !$address) {
    echo json_encode(['success' => false, 'error' => 'Incomplete data']);
    exit;
}

// Function to send receipt with HTML template
function sendReceiptEmail($toEmail, $name, $orderID, $cart, $total, $contact, $address) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'piyoacadnotes@gmail.com';
        $mail->Password   = 'zdzr kzod gqti yuji'; // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('piyoacadnotes@gmail.com', 'MediTrack');
        $mail->addAddress($toEmail, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "MediTrack Receipt – Order #$orderID";

        // ---------------------------------------------------------
        // 1. LOAD THE TEMPLATE
        // ---------------------------------------------------------
        $emailBody = file_get_contents('receipt.html');

        // ---------------------------------------------------------
        // 2. PREPARE DATA TO REPLACE
        // ---------------------------------------------------------
        $date = date("m/d/Y");
        $time = date("h:i A");

        // Generate items table rows
        $itemsTable = "";
        $itemCount = 0;
        foreach ($cart as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $itemsTable .= "
                <tr>
                    <td class=\"qty-col\">" . $item['quantity'] . "</td>
                    <td class=\"item-col\">" . htmlspecialchars($item['name']) . "</td>
                    <td class=\"price-col\">₱" . number_format($itemTotal, 2) . "</td>
                </tr>";
            $itemCount++;
        }

        // ---------------------------------------------------------
        // 3. REPLACE PLACEHOLDERS
        // ---------------------------------------------------------
        $emailBody = str_replace('{{orderID}}', $orderID, $emailBody);
        $emailBody = str_replace('{{date}}', $date, $emailBody);
        $emailBody = str_replace('{{time}}', $time, $emailBody);
        $emailBody = str_replace('{{customerName}}', htmlspecialchars($name), $emailBody);
        $emailBody = str_replace('{{contact}}', htmlspecialchars($contact), $emailBody);
        $emailBody = str_replace('{{address}}', htmlspecialchars($address), $emailBody);
        $emailBody = str_replace('{{itemsTable}}', $itemsTable, $emailBody);
        $emailBody = str_replace('{{itemCount}}', $itemCount, $emailBody);
        $emailBody = str_replace('{{total}}', number_format($total, 2), $emailBody);

        $mail->Body = $emailBody;
        $mail->AltBody = "Order #$orderID - Total: ₱" . number_format($total, 2);

        $mail->send();
        return true;
    } catch (Exception $e) {
        $errorMsg = "PHPMailer Error: " . $e->getMessage() . " | ErrorInfo: {$mail->ErrorInfo}";
        error_log($errorMsg);
        error_log("Email recipient: " . $toEmail);
        error_log("SMTP Host: smtp.gmail.com, Port: 587, User: piyoacadnotes@gmail.com");
        return false;
    }
}

try {
    // Insert into orders table
    $stmt = $pdo->prepare("
        INSERT INTO orders (userID, full_name, contact, address, total_amount, payment_method, payment_id, status)
        VALUES (?, ?, ?, ?, ?, 'PayPal', ?, 'Paid')
    ");
    $stmt->execute([$userID, $name, $contact, $address, $total, $paymentID]);
    $orderID = $pdo->lastInsertId();

    // Insert order items
    $stmtItem = $pdo->prepare("
        INSERT INTO order_items (order_id, medicine_id, price, quantity)
        VALUES (?, ?, ?, ?)
    ");
    foreach ($cart as $item) {
        $stmtItem->execute([
            $orderID,
            $item['medicine_id'],
            $item['price'],
            $item['quantity']
        ]);

        $stmtStock = $pdo->prepare("UPDATE medicines SET quantity = quantity - ? WHERE medicine_id = ?");
        $stmtStock->execute([$item['quantity'], $item['medicine_id']]);
    }

    // Send email receipt AFTER order is saved
    $emailSent = false;
    $emailError = '';
    if ($email) {
        $emailSent = sendReceiptEmail($email, $name, $orderID, $cart, $total, $contact, $address);
        if (!$emailSent) {
            $emailError = "Order saved but email could not be sent";
        }
    } else {
        $emailError = "No email address found for receipt";
    }

    echo json_encode([
        'success' => true,
        'orderID' => $orderID,
        'emailSent' => $emailSent,
        'emailError' => $emailError
    ]);

} catch (PDOException $e) {
    // Log the exact error for debugging
    error_log("Order save error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
