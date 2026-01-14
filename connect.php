<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$db = "pill-and-pestle";

$conn = new mysqli($dbhost, $dbuser, $dbpass, $db);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

try {
	$pdo = new PDO("mysql:host=$dbhost;dbname=$db", $dbuser, $dbpass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);
} catch (PDOException $e) {
	die("Connection failed: " . $e->getMessage());
}

function executeQuery($query) {
	global $conn;
	return mysqli_query($conn, $query);
}
?>

