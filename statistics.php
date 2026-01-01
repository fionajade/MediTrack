<?php
session_start();
include("connect.php");

// Total Customers
$totalCustomers = $pdo->query("SELECT COUNT(*) FROM tbl_user WHERE role = 'user'")->fetchColumn();

// Total Sales
$totalSales = $pdo->query("SELECT IFNULL(SUM(total_price), 0) FROM sales")->fetchColumn();

// Total Stocks
$totalStocks = $pdo->query("SELECT SUM(quantity) FROM medicines")->fetchColumn();

// Fetch users for dropdown
$allUsers = $pdo->query("SELECT username FROM tbl_user WHERE role = 'user' ORDER BY username ASC")->fetchAll(PDO::FETCH_COLUMN);

// Initialize date filters
$from = $_GET['from_date'] ?? null;
$to = $_GET['to_date'] ?? null;

// Filtered Sales Breakdown
$salesBreakdownQuery = "
  SELECT DATE_FORMAT(sale_date, '%Y-%m') AS sale_month, SUM(total_price) AS total
  FROM sales
  WHERE 1=1";
if (!empty($from) && !empty($to)) {
  $salesBreakdownQuery .= " AND DATE(sale_date) BETWEEN '$from' AND '$to'";
}
$salesBreakdownQuery .= "
  GROUP BY sale_month
  ORDER BY sale_month DESC
  LIMIT 6";
$salesBreakdown = $pdo->query($salesBreakdownQuery)->fetchAll(PDO::FETCH_ASSOC);

// Stock by Category
$stockPerCategory = $pdo->query("
  SELECT c.name AS category, SUM(m.quantity) AS total_quantity
  FROM medicines m
  JOIN categories c ON m.category_id = c.id
  GROUP BY c.name
  ORDER BY total_quantity DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Low Stock Medicines
$lowStock = $pdo->query("
  SELECT name, quantity
  FROM medicines
  WHERE quantity <= 10
  ORDER BY quantity ASC
")->fetchAll(PDO::FETCH_ASSOC);

// About to Expire Medicines
$aboutToExpire = $pdo->query("
  SELECT name, expiry_date
  FROM medicines
  WHERE expiry_date IS NOT NULL AND expiry_date <= CURDATE() + INTERVAL 30 DAY
  ORDER BY expiry_date ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Top Selling Medicines
$topSelling = $pdo->query("
  SELECT m.name, SUM(s.quantity) AS sold
  FROM sales s
  JOIN medicines m ON s.medicine_id = m.medicine_id
  GROUP BY s.medicine_id
  ORDER BY sold DESC
  LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Filtered Sales Records
$filterQuery = "SELECT s.sale_id, u.username, m.name AS medicine, s.quantity, s.total_price, s.sale_date
                FROM sales s
                JOIN tbl_user u ON s.user_id = u.userID
                JOIN medicines m ON s.medicine_id = m.medicine_id
                WHERE 1=1";
if (!empty($_GET['filter_user'])) {
  $user = $_GET['filter_user'];
  $filterQuery .= " AND u.username LIKE " . $pdo->quote("%$user%");
}
if (!empty($_GET['filter_medicine'])) {
  $med = $_GET['filter_medicine'];
  $filterQuery .= " AND m.name LIKE " . $pdo->quote("%$med%");
}
if (!empty($from) && !empty($to)) {
  $filterQuery .= " AND DATE(s.sale_date) BETWEEN '$from' AND '$to'";
}
$allSales = $pdo->query($filterQuery)->fetchAll(PDO::FETCH_ASSOC);

// Filtered Customer Count
$filteredCustomerQuery = "SELECT COUNT(DISTINCT s.user_id) FROM sales s WHERE 1=1";
if (!empty($from) && !empty($to)) {
  $filteredCustomerQuery .= " AND DATE(s.sale_date) BETWEEN '$from' AND '$to'";
}
$filteredCustomerCount = $pdo->query($filteredCustomerQuery)->fetchColumn();

// Customer Breakdown Table
$customerBreakdown = $pdo->query("
  SELECT u.username, COUNT(s.sale_id) AS purchases, SUM(s.total_price) AS total_spent
  FROM sales s
  JOIN tbl_user u ON s.user_id = u.userID
  WHERE 1=1
    " . (!empty($from) && !empty($to) ? " AND DATE(s.sale_date) BETWEEN '$from' AND '$to'" : "") . "
  GROUP BY s.user_id
  ORDER BY total_spent DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Excel Export
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
  header("Content-Type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=sales_report.xls");
  echo "Sale ID\tUser\tMedicine\tQuantity\tTotal Price\tSale Date\n";
  foreach ($allSales as $row) {
    echo "{$row['sale_id']}\t{$row['username']}\t{$row['medicine']}\t{$row['quantity']}\t{$row['total_price']}\t{$row['sale_date']}\n";
  }
  exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="icon" href="assets/medi_logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #052241;
      font-family: 'Poppins', sans-serif;
      color: #f0f4f8;
    }
    .main-content {
      font-family: 'Poppins', sans-serif;
      margin-left: 260px;
      padding: 40px 30px;
    }
    .dashboard-header {
      margin-bottom: 30px;
      font-weight: 600;
      font-size: 1.8rem;
    }
    .gradient-card {
      background: linear-gradient(50deg, #ADFCF9, #052241);
      color: #ffffff;
    }

    .gradient-card h5,
    .gradient-card p {
      color: #000000;
    }

    .card {
      border: none;
      border-radius: 16px;
      background-color: #0b2e52;
      color: #f0f4f8;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }
    .card-header {
      background: linear-gradient(45deg, #ADFCF9, #052241);
      color: #ffffff;
      font-size: 1.25rem;
      font-weight: 600;
      border-radius: 16px 16px 0 0;
    }
    .card p {
      font-size: 2rem;
      font-weight: bold;
      margin: 0;
    }
    .table {
      background-color: #0b2e52;
      color: #f0f4f8;
      font-size: 0.95rem;
    }
    .table th {
      background-color: #0E4D92;
      color: white;
      font-weight: 600;
    }
    .form-control,
    .form-select {
      background-color: #fefefe;
      border: 1px solid #ccc;
      color: #052241;
    }
    .form-control:focus,
    .form-select:focus {
      border-color: #0E4D92;
      box-shadow: 0 0 0 0.2rem rgba(14, 77, 146, 0.25);
    }
    .btn-light {
      background-color: #0E4D92;
      border-color: #0E4D92;
      color: #fff;
    }
    .btn-light:hover {
      background-color: #126dc4;
      border-color: #126dc4;
    }
    .btn-success {
      background-color: #198754;
      border-color: #198754;
    }
    .btn-success:hover {
      background-color: #146c43;
      border-color: #146c43;
    }
    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<?php include('admin_sidebar.php'); ?>

<div class="main-content">
  <h2 class="dashboard-header text-center">Admin Dashboard Statistics</h2>

  <div class="row g-4 mb-5">
    <div class="col-md-4">
      <div class="card text-center py-4 gradient-card">
        <div class="card-body">
          <h5>Total Customers</h5>
          <p><?= $totalCustomers ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center py-4 gradient-card">
        <div class="card-body">
          <h5>Total Sales</h5>
          <p>₱<?= number_format($totalSales ?? 0, 2) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center py-4 gradient-card">
        <div class="card-body">
          <h5>Total Stocks</h5>
          <p><?= $totalStocks ?></p>
        </div>
      </div>
    </div>
  </div>


  <hr class="my-5">


  <!-- <div class="row mb-4 align-items-end">
  Date Filter for Breakdown
  <form method="GET" class="col-md-4 bg-light p-3 rounded text-dark">
    <h5 class="mb-3">Filter by Date</h5>
    <div class="mb-2">
      <label for="from_date" class="form-label">From</label>
      <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label for="to_date" class="form-label">To</label>
      <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">
    </div>
    <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
  </form> -->

  <!-- Customer Breakdown Card -->
  <div class="card mb-5">
    <div class="card-header d-flex justify-content-between bold align-items-center">
      <span><i class="bi bi-people-fill me-2"></i>Customers Breakdown</span>
      <form method="GET" class="d-flex gap-2">
        <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
        <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">
        <button class="btn btn-light" type="submit">Filter</button>
      </form>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered text-center">
          <thead>
            <tr>
              <th>Username</th>
              <th>Purchases</th>
              <th>Total Spent (₱)</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($customerBreakdown)): ?>
              <?php foreach ($customerBreakdown as $cust): ?>
                <tr>
                  <td><?= htmlspecialchars($cust['username']) ?></td>
                  <td><?= $cust['purchases'] ?></td>
                  <td>₱<?= number_format($cust['total_spent'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3">No data available for the selected range.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Sales Breakdown -->
  <div class="card mb-5">
    <div class="card-header">
      <i class="bi bi-bar-chart-fill me-2"></i>Sales Breakdown<?= (!empty($from) && !empty($to)) ? " ({$from} to {$to})" : " (Last 6 Months)" ?>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered text-center">
          <thead>
            <tr>
              <th>Month</th>
              <th>Total Sales (₱)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($salesBreakdown as $row): ?>
              <tr>
                <td><?= $row['sale_month'] ?></td>
                <td><?= number_format($row['total'], 2) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Stock by Category -->
  <div class="card mb-5">
    <div class="card-header">
      <i class="bi bi-box-seam me-2"></i>Stock Breakdown by Category
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered text-center">
          <thead>
            <tr>
              <th>Category</th>
              <th>Total Quantity</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($stockPerCategory as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= $row['total_quantity'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Low Stock and Expiry -->
  <div class="row g-4 mb-5">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>Low Stock Medicines (≤10)
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered text-center">
              <thead>
                <tr>
                  <th>Medicine</th>
                  <th>Quantity</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($lowStock as $med): ?>
                  <tr>
                    <td><?= htmlspecialchars($med['name']) ?></td>
                    <td class="text-danger fw-bold"><?= $med['quantity'] ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <i class="bi bi-calendar-x-fill me-2"></i>About to Expire Medicines (Next 30 Days)
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered text-center">
              <thead>
                <tr>
                  <th>Medicine</th>
                  <th>Expiration Date</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($aboutToExpire as $med): ?>
                  <tr>
                    <td><?= htmlspecialchars($med['name']) ?></td>
                    <td class="text-danger fw-bold"><?= htmlspecialchars($med['expiry_date']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Top Selling -->
  <div class="card mb-5">
    <div class="card-header">
      <i class="bi bi-star-fill me-2"></i>Top 5 Best-Selling Medicines
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered text-center">
          <thead>
            <tr>
              <th>Medicine</th>
              <th>Units Sold</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($topSelling as $med): ?>
              <tr>
                <td><?= htmlspecialchars($med['name']) ?></td>
                <td><?= $med['sold'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- All Sales Records -->
  <div class="card mb-5">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span><i class="bi bi-clipboard-data me-2"></i>All Sales Records</span>
      <form method="GET" class="row g-2">
        <div class="col-md-3">
          <select name="filter_user" class="form-select">
            <option value="">All Users</option>
            <?php foreach ($allUsers as $user): ?>
              <option value="<?= htmlspecialchars($user) ?>" <?= (isset($_GET['filter_user']) && $_GET['filter_user'] === $user) ? 'selected' : '' ?>>
                <?= htmlspecialchars($user) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <input type="text" name="filter_medicine" class="form-control" placeholder="Filter by Medicine" value="<?= htmlspecialchars($_GET['filter_medicine'] ?? '') ?>">
        </div>
        <div class="col-md-2">
          <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
        </div>
        <div class="col-md-2">
          <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">
        </div>
        <div class="col-md-2">
          <button class="btn btn-light w-100" type="submit">Apply</button>
        </div>
        <div class="col-12 text-end mt-2">
          <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'excel'])) ?>" class="btn btn-success">Export to Excel</a>
        </div>
      </form>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered text-center">
          <thead>
            <tr>
              <th>User</th>
              <th>Medicine</th>
              <th>Quantity</th>
              <th>Total Price</th>
              <th>Sale Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($allSales as $sale): ?>
              <tr>
                <td><?= htmlspecialchars($sale['username']) ?></td>
                <td><?= htmlspecialchars($sale['medicine']) ?></td>
                <td><?= $sale['quantity'] ?></td>
                <td>₱<?= number_format($sale['total_price'], 2) ?></td>
                <td><?= $sale['sale_date'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body>
</html>