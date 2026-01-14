<?php
$title = "Pill and Pestle Dashboard";
$page_title = "Dashboard";

session_start();
include("connect.php");

// Only allow admin access
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit();
}

// --- DATA FETCHING ---

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

<?php include("admin_header.php"); ?>

<body>

  <?php include("admin_sidebar_mobile.php"); ?>

  <div class="container-fluid">
    <div class="row">
      <?php include("admin_sidebar_desktop.php"); ?>
      <!-- === MAIN CONTENT AREA === -->
      <main class="col-lg-10 col-12 p-4">

        <p class="page-title-pre">Welcome back, <?= $displayName ?>!</p>
        <h1 class="page-title">Dashboard</h1>
        <hr>

        <!-- CARDS ROW -->
        <div class="row g-4 justify-content-center">

          <!-- Card 1: Top Stock Categories -->
          <div class="col-lg-4 col-md-6 col-12">
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
          </div>

          <!-- Card 2: Top Suppliers -->
          <div class="col-lg-4 col-md-6 col-12">
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
          </div>

          <!-- Card 3: Statistics -->
          <div class="col-lg-4 col-md-6 col-12">
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

        <div class="mb-5"></div>
      </main>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>