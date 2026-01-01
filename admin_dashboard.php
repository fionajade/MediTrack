<?php
session_start();
include("connect.php");

// Only allow admin access
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit();
}

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MediTrack - Admin Dashboard</title>
  <link rel="icon" href="assets/medi_logo.png">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  /* Import Poppins font */

  :root {
    --main-color: #052241;
    --hover-color: #04192d;

    /* Optional: pastel variables for reuse */
    --pastel-blue: #e6f0fa;
    --pastel-teal: #d9f1f0;
    --pastel-sky: #cfe5f0;
    --pastel-cyan: #e4f3f6;
  }

  body {
    background-color: #f8f9fa;
    font-family: 'Poppins', sans-serif;
  }

  .main-content {
    padding: 2rem;
    margin-left: 250px;
    font-family: 'Poppins', sans-serif;
  }

  .dashboard-header {
    background-color: var(--main-color);
    color: white;
    padding: 2rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    font-family: 'Poppins', sans-serif;
  }

  .dashboard-box {
    /* background-color: var(--pastel-blue); ✅ Changed to pastel blue */
    padding: 1.5rem;
    border-radius: 1rem;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    font-family: 'Poppins', sans-serif;
  }

  .dashboard-box.teal {
  background-color: var(--pastel-teal);
  }
  .dashboard-box.sky {
    background-color: var(--pastel-sky);
  }
  .dashboard-box.cyan {
    background-color: var(--pastel-cyan);
  }
  .dashboard-box:hover {
    transform: translateY(-3px);
    transition: 0.2s ease;
  }

  .btn-black {
    background-color: var(--main-color);
    color: white;
    border: none;
    transition: background-color 0.3s ease;
    font-family: 'Poppins', sans-serif;
  }

  .btn-black:hover {
    background-color: var(--hover-color);
  }

  .btn-outline-dark {
    color: var(--main-color);
    border-color: var(--main-color);
    font-family: 'Poppins', sans-serif;
  }

  .btn-outline-dark:hover {
    background-color: var(--main-color);
    color: white;
  }

  .lead {
    font-size: 1.1rem;
    opacity: 0.9;
    font-family: 'Poppins', sans-serif;
  }

  .text-start {
    text-align: start;
    font-family: 'Poppins', sans-serif;
  }

  </style>
</head>
<body>
<?php include 'admin_sidebar.php'; ?>

<div class="main-content">
  <header class="dashboard-header">
    <h1>Welcome to MediTrack!</h1>
    <p class="lead">
      <?php
        if (isset($_SESSION['username'])) {
          echo "Hello " . htmlspecialchars($_SESSION['username']) . ", your dashboard is all set.";
        } else {
          echo "Hello Admin, your dashboard is all set.";
        }
      ?>
    </p>
  </header>

  <div class="container-fluid">
    <div class="row g-4">
      <!-- Stocks Panel -->
      <div class="col-md-4">
        <div class="dashboard-box cyan">
          <div>
            <h5 class="mb-3">Top Stock Categories</h5>
            <?php foreach ($topStockCategories as $cat): ?>
              <a href="medicines_stock.php#category-<?= $cat['id'] ?>" class="btn btn-black w-100 mb-2">
                <?= htmlspecialchars($cat['name']) ?>
              </a>
            <?php endforeach; ?>
          </div>
          <a href="medicines_stock.php" class="btn btn-outline-dark w-100 mt-auto">View More &rsaquo;</a>
        </div>
      </div>

      <!-- Suppliers Panel -->
      <div class="col-md-4">
        <div class="dashboard-box teal">
          <div>
            <h5 class="mb-3">Top Suppliers</h5>
            <?php if (!empty($topSuppliers)): ?>
              <?php foreach ($topSuppliers as $sup): ?>
                <div class="btn btn-black w-100 mb-2 text-start">
                  <?= htmlspecialchars($sup['category_name']) ?> | <?= htmlspecialchars($sup['supplier_name']) ?>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p>No supplier data available.</p>
            <?php endif; ?>
          </div>
          <a href="suppliers.php" class="btn btn-outline-dark w-100 mt-auto">View More &rsaquo;</a>
        </div>
      </div>

      <!-- Statistics Panel -->
      <div class="col-md-4">
        <div class="dashboard-box sky">
          <div>
            <h5 class="mb-3">Quick Statistics</h5>
            <p><strong><?= number_format($totalCustomers) ?></strong> Customers</p>
            <p><strong>₱<?= number_format($totalSales, 2) ?></strong> Total Sales</p>
            <p><strong><?= number_format($totalStocks) ?></strong> Medicine Stock</p>
            <p><strong><?= number_format($expiringSoon) ?></strong> Expiring in 30 Days</p>
          </div>
          <a href="statistics.php" class="btn btn-outline-dark w-100 mt-auto">View More</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
