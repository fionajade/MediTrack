<?php
session_start();
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['supplier_id'] ?? null;
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);
    $email = trim($_POST['email']);
    $category_id = $_POST['category_id'];

    if (!$name || !$address || !$contact || !$email || !$category_id) {
        die("All fields are required.");
    }

    try {
        if ($id) {
            // Update existing supplier
            $stmt = $pdo->prepare("UPDATE suppliers SET name = ?, address = ?, contact = ?, email = ?, category_id = ? WHERE id = ?");
            $stmt->execute([$name, $address, $contact, $email, $category_id, $id]);
        } else {
            // Insert new supplier
            $stmt = $pdo->prepare("INSERT INTO suppliers (name, address, contact, email, category_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $address, $contact, $email, $category_id]);
        }

        header("Location: suppliers.php");
        exit();

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    header("Location: suppliers.php");
    exit();
}
