<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("connect.php");
session_start();
header('Content-Type: application/json');

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// --- Ensure user is logged in ---
$userID = $_SESSION['userID'] ?? null;
if (!$userID) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

// --- Get POST data ---
$data = json_decode(file_get_contents("php://input"), true);
$paymentID = $data['paymentID'] ?? '';
$cart = $data['cart'] ?? [];
$total = $data['total'] ?? '';
$name = $data['name'] ?? '';
$contact = $data['contact'] ?? '';
$address = $data['address'] ?? '';
$email = $data['email'] ?? $_SESSION['email'] ?? '';

if (!$paymentID || !$cart || !$total || !$name || !$contact || !$address) {
    echo json_encode(['success' => false, 'error' => 'Incomplete data']);
    exit;
}

// --- PHPMailer function ---
function sendReceiptEmail($toEmail, $name, $orderID, $cart, $total, $contact, $address, $paymentMethod, $paymentID)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'piyoacadnotes@gmail.com';
        $mail->Password   = 'zdzr kzod gqti yuji';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('piyoacadnotes@gmail.com', 'Pill and Pestle');
        $mail->addAddress($toEmail, $name);

        $mail->isHTML(true);
        $mail->Subject = "Pill and Pestle Receipt Confirmation";

        $emailBody = file_get_contents('receipt.html');
        $date = date("m/d/Y");
        $time = date("h:i A");

        $itemsTable = "";
        $itemCount = 0;
        foreach ($cart as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $itemsTable .= "
            <tr>
                <td width=\"60%\" align=\"left\" style=\"padding:6px 0;\">" . htmlspecialchars($item['name']) . "</td>
                <td width=\"15%\" align=\"center\" style=\"padding:6px 0;\">{$item['quantity']}</td>
                <td width=\"25%\" align=\"right\" style=\"padding:6px 0;\">₱" . number_format($itemTotal, 2) . "</td>
            </tr>";

            $itemCount++;
        }

        $emailBody = str_replace(
            [
                '{{orderID}}',
                '{{date}}',
                '{{time}}',
                '{{customerName}}',
                '{{contact}}',
                '{{address}}',
                '{{itemsTable}}',
                '{{itemCount}}',
                '{{total}}',
                '{{payment_method}}',
                '{{payment_id}}'
            ],
            [
                $orderID,
                $date,
                $time,
                htmlspecialchars($name),
                htmlspecialchars($contact),
                htmlspecialchars($address),
                $itemsTable,
                $itemCount,
                number_format($total, 2),
                $paymentMethod,
                $paymentID
            ],
            $emailBody
        );

        $mail->Body = $emailBody;
        $mail->AltBody = "Order #$orderID - Total: ₱" . number_format($total, 2);
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $e->getMessage());
        return false;
    }
}

// --- Save order ---
try {
    $pdo->beginTransaction();

    // Insert order
    $stmt = $pdo->prepare("
        INSERT INTO orders (userID, full_name, contact, address, total_amount, payment_method, payment_id, status)
        VALUES (?, ?, ?, ?, ?, 'PayPal', ?, 'Paid')
    ");
    $stmt->execute([$userID, $name, $contact, $address, $total, $paymentID]);
    $orderID = $pdo->lastInsertId();

    // Insert order items & update stock
    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, medicine_id, price, quantity) VALUES (?, ?, ?, ?)");
    $stmtStock = $pdo->prepare("UPDATE medicines SET quantity = quantity - ? WHERE medicine_id = ?");

    foreach ($cart as $item) {
        $stmtItem->execute([$orderID, $item['medicine_id'], $item['price'], $item['quantity']]);
        $stmtStock->execute([$item['quantity'], $item['medicine_id']]);
    }

    $pdo->commit();

    // --- Email & SMS ---
    $emailSent = $email
    ? sendReceiptEmail(
        $email,
        $name,
        $orderID,
        $cart,
        $total,
        $contact,
        $address,
        'PayPal',
        $paymentID
    )
    : false;

    // SMS
    $smsSent = false;
    $smsError = '';
    try {
        $stmtPhone = $pdo->prepare("SELECT contact FROM tbl_user WHERE userID = ?");
        $stmtPhone->execute([$userID]);
        $user = $stmtPhone->fetch(PDO::FETCH_ASSOC);
        if ($user && $user['contact']) {
            $gateway_url = "http://10.150.82.21:8080/messages";
            $username = "sms";
            $password = "ABu1O9aE";
            $message = "Your transaction was successful. Thank you for using our service.";

            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\n" .
                        "Authorization: Basic " . base64_encode("$username:$password"),
                    'content' => json_encode(["phoneNumbers" => [$user['contact']], "message" => $message])
                ]
            ]);

            $resp = @file_get_contents($gateway_url, false, $context);
            if ($resp !== false) $smsSent = true;
            else $smsError = "No response from SMS gateway";
        } else $smsError = "User phone not found";
    } catch (Exception $e) {
        $smsError = $e->getMessage();
    }

    // --- Return final response ---
    echo json_encode([
        'success' => true,
        'orderID' => $orderID,
        'emailSent' => $emailSent,
        'smsSent' => $smsSent,
        'smsError' => $smsError
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}