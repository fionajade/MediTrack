<?php
header("Content-Type: application/json");

require_once "connect.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
    exit;
}

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$address  = trim($_POST['address'] ?? '');
$contact  = trim($_POST['contact'] ?? '');

if (empty($username) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields"
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO tbl_user (username, email, password, address, contact)
         VALUES (:username, :email, :password, :address, :contact)"
    );

    $stmt->execute([
        ':username' => $username,
        ':email'    => $email,
        ':password' => $password,
        ':address'  => $address,
        ':contact'  => $contact
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "User saved successfully"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database error",
        "debug" => $e->getMessage()
    ]);
}
