<?php
include("connect.php");
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$searchTerm = $data['search'] ?? null;
$category_id = $data['category_id'] ?? null;

try {
  if ($searchTerm) {
    $stmt = $pdo->prepare("SELECT * FROM medicines WHERE name LIKE ? OR description LIKE ?");
    $stmt->execute(["%$searchTerm%", "%$searchTerm%"]);
  } else {
    $stmt = $pdo->prepare("SELECT * FROM medicines WHERE category_id = ?");
    $stmt->execute([$category_id]);
  }
  $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($medicines);
} catch (Exception $e) {
  echo json_encode([]);
}