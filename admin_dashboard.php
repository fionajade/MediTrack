<?php
session_start();
include("connect.php");

// Only allow admin access
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit();
}

// --- PHP LOGIC (UNCHANGED) ---

// Total Statistics
$totalStocks = $pdo->query("SELECT SUM(quantity) FROM medicines")->fetchColumn();
$totalSuppliers = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
$totalSales = $pdo->query("SELECT IFNULL(SUM(total_price), 0) FROM sales")->fetchColumn();
$totalCustomers = $pdo->query("SELECT COUNT(*) FROM tbl_user WHERE role = 'user'")->fetchColumn();
$expiringSoon = $pdo->query("SELECT COUNT(*) FROM medicines WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();

// Fetch top stock categories
$topStockCategories = [];
try {
  $stockStmt = $pdo->query("SELECT c.id, c.name FROM categories c
        JOIN medicines m ON c.id = m.category_id
        GROUP BY c.id ORDER BY COUNT(m.medicine_id) DESC LIMIT 5");
  $topStockCategories = $stockStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  error_log("Category fetch error: " . $e->getMessage());
}

// Fetch top suppliers
$topSuppliers = [];
try {
  $supplierStmt = $pdo->query("SELECT c.name AS category_name, s.name AS supplier_name
        FROM categories c
        JOIN medicines m ON c.id = m.category_id
        JOIN suppliers s ON m.supplier_id = s.id
        GROUP BY c.name, s.name
        ORDER BY c.name LIMIT 5");
  $topSuppliers = $supplierStmt->fetchAll();
} catch (PDOException $e) {
  error_log("Supplier fetch error: " . $e->getMessage());
}

// Get User Name for Header
$displayName = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MediTrack - Dashboard</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    @font-face {
      font-family: 'SF Pro Display';
      src: url('assets/SF-Pro-Display.ttf') format('truetype');
      font-weight: 400;
      font-style: normal;
    }

    @font-face {
      font-family: 'SF Pro Display';
      src: url('assets/SF-Pro-Display-Regular.otf') format('opentype');
      font-weight: 600;
      font-style: normal;
    }

    :root {
      --primary-dark: #001f3f;
      /* Deep Navy Blue */
      --text-gray: #6c757d;
      --bg-light: #ffffff;
      --sidebar-width: 250px;
    }

    body {
      font-family: 'SF-Pro-Display', sans-serif;
      background-color: var(--bg-light);
      margin: 0;
      overflow-x: hidden;
      display: flex;
    }

    /* --- Sidebar Styling --- */
    .sidebar {
      width: var(--sidebar-width);
      height: 100vh;
      background-color: #fff;
      position: fixed;
      left: 0;
      top: 0;
      padding: 2rem 1.5rem;
      border-right: 1px solid #e0e0e0;
      display: flex;
      flex-direction: column;
      z-index: 1000;
    }

    .brand-logo {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--primary-dark);
      margin-bottom: 3rem;
      text-decoration: none;
      display: block;
    }

    .nav-links {
      flex-grow: 1;
      list-style: none;
      padding: 0;
    }

    .nav-item {
      margin-bottom: 1rem;
    }

    .nav-link {
      color: var(--primary-dark);
      text-decoration: none;
      font-weight: 400;
      padding: 10px 15px;
      display: block;
      border-radius: 8px;
      transition: all 0.3s;
    }

    /* Active State (Home) */
    .nav-link.active {
      background-color: var(--primary-dark);
      color: #fff;
    }

    .nav-link:hover:not(.active) {
      background-color: #f0f4f8;
    }

    .bottom-links {
      margin-top: auto;
      list-style: none;
      padding: 0;
    }

    .bottom-links a {
      color: var(--primary-dark);
      text-decoration: none;
      display: block;
      margin-bottom: 0.8rem;
      font-size: 0.95rem;
    }

    /* --- Main Content Styling --- */
    .main-content {
      margin-left: var(--sidebar-width);
      width: calc(100% - var(--sidebar-width));
      padding: 2.5rem 4rem;
    }

    /* Header Section */
    .header-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .welcome-text {
      color: var(--primary-dark);
      font-size: 1.1rem;
      font-weight: 400;
      margin: 0;
    }

    .page-title {
      color: var(--primary-dark);
      font-size: 3rem;
      font-weight: 600;
      margin: 0;
      line-height: 1.1;
      letter-spacing: -1px;
    }

    .user-profile {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .user-name {
      font-weight: 500;
      color: var(--primary-dark);
      font-size: 1.1rem;
    }

    .profile-circle {
      width: 50px;
      height: 50px;
      background-color: var(--primary-dark);
      border-radius: 50%;
    }

    .divider-line {
      border-top: 1px solid #000;
      margin-bottom: 3rem;
      opacity: 0.2;
    }

    /* Cards Container */
    .cards-container {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 2rem;
      height: 65vh;
      /* Taller cards as per design */
    }

    /* Individual Card Design */
    .dark-card {
      background-color: var(--primary-dark);
      border-radius: 20px;
      color: white;
      padding: 2.5rem 1.5rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      box-shadow: 0 10px 30px rgba(5, 34, 65, 0.3);
    }

    .card-heading {
      font-size: 2.5rem;
      line-height: 0.9;
      text-align: center;
      margin-bottom: 3rem;
      font-weight: 400;
      /* Thinner weight */
    }

    .card-heading span {
      display: block;
      font-weight: 600;
      /* Bolder for second word if needed */
    }

    /* List Content inside cards */
    .card-list {
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1.2rem;
      width: 100%;
      overflow-y: auto;
    }

    .list-item {
      color: #fff;
      text-decoration: none;
      font-size: 1rem;
      font-weight: 300;
      text-align: center;
      transition: opacity 0.3s;
    }

    .list-item:hover {
      opacity: 0.7;
      color: #fff;
    }

    /* View More Button */
    .btn-view-more {
      width: 80%;
      border: 1px solid white;
      background: transparent;
      color: white;
      border-radius: 50px;
      padding: 10px;
      text-align: center;
      text-decoration: none;
      font-size: 0.9rem;
      margin-top: auto;
      /* Push to bottom */
      transition: background 0.3s, color 0.3s;
    }

    .btn-view-more:hover {
      background: white;
      color: var(--primary-dark);
    }

    /* Scrollbar for inside cards */
    .card-list::-webkit-scrollbar {
      width: 4px;
    }

    .card-list::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.3);
      border-radius: 4px;
    }
  </style>
</head>

<body>

  <!-- SIDEBAR (Replicating design) -->
  <aside class="sidebar">
    <a href="#" class="brand-logo">MediTrack</a>

    <ul class="nav-links">
      <li class="nav-item">
        <a href="dashboard.php" class="nav-link active">Home</a>
      </li>
      <li class="nav-item">
        <a href="medicines_stock.php" class="nav-link">Medicine Stock</a>
      </li>
      <li class="nav-item">
        <a href="suppliers.php" class="nav-link">Suppliers</a>
      </li>
      <li class="nav-item">
        <a href="statistics.php" class="nav-link">Statistics</a>
      </li>
    </ul>

    <ul class="bottom-links">
      <li><a href="backup.php">Backup</a></li>
      <li><a href="restore.php">Restore</a></li>
      <li><a href="edit_account.php">Edit Account</a></li>
      <li><a href="../logout.php">Log Out</a></li>
    </ul>
  </aside>

  <!-- MAIN CONTENT -->
  <div class="main-content">

    <!-- HEADER -->
    <div class="header-section">
      <div class="header-left">
        <p class="welcome-text">Welcome back, <?= $displayName ?>!</p>
        <h1 class="page-title">Dashboard</h1>
      </div>
      <div class="user-profile">
        <div class="profile-circle"></div>
      </div>
    </div>

    <div class="divider-line"></div>

    <!-- CARDS ROW -->
    <div class="cards-container">

      <!-- Card 1: Categories -->
      <div class="dark-card">
        <div class="card-heading">
          Top Stock<br><span>Categories</span>
        </div>

        <div class="card-list">
          <?php foreach ($topStockCategories as $cat): ?>
            <a href="medicines_stock.php#category-<?= $cat['id'] ?>" class="list-item">
              <?= htmlspecialchars($cat['name']) ?>
            </a>
          <?php endforeach; ?>
        </div>

        <a href="medicines_stock.php" class="btn-view-more">View More</a>
      </div>

      <!-- Card 2: Suppliers -->
      <div class="dark-card">
        <div class="card-heading">
          Top<br><span>Suppliers</span>
        </div>

        <div class="card-list" style="justify-content: center;">
          <?php if (!empty($topSuppliers)): ?>
            <?php foreach ($topSuppliers as $sup): ?>
              <div class="list-item">
                <?= htmlspecialchars($sup['supplier_name']) ?>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <span class="list-item">No Supplier Data<br>Available.</span>
          <?php endif; ?>
        </div>

        <a href="suppliers.php" class="btn-view-more">View More</a>
      </div>

      <!-- Card 3: Statistics -->
      <div class="dark-card">
        <div class="card-heading">
          Quick<br><span>Statistics</span>
        </div>

        <div class="card-list">
          <div class="list-item">
            Customers: <?= number_format($totalCustomers) ?>
          </div>
          <div class="list-item">
            Total Sales: ₱<?= number_format($totalSales, 2) ?>
          </div>
          <div class="list-item">
            Medicine Stock: <?= number_format($totalStocks) ?>
          </div>
          <div class="list-item">
            Expiring (30d): <?= number_format($expiringSoon) ?>
          </div>
          <div class="list-item">
            Suppliers: <?= number_format($totalSuppliers) ?>
          </div>
        </div>

        <a href="statistics.php" class="btn-view-more">View More</a>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>