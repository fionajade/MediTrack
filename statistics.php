<?php $title = "Pill and Pestle Statistics"; $subhead = "Analytics & Reports";  $page_title = "Statistics"; 

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

include 'shared/admin/admin_header.php'; ?>

<body>

    <?php include 'admin_sidebar.php'; ?>


    <!-- MAIN CONTENT -->
    <div class="main-content">

<?php include 'shared/admin/admin_page_title.php'; ?>
<div class="divider-line"></div>

        <!-- Top Metrics Row -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-title">Total Customers</div>
                    <div class="stat-value"><?= number_format($totalCustomers) ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-title">Total Sales Revenue</div>
                    <div class="stat-value">₱<?= number_format($totalSales ?? 0, 2) ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-title">Medicine In Stock</div>
                    <div class="stat-value"><?= number_format($totalStocks) ?></div>
                </div>
            </div>
        </div>

        <!-- ROW 1: Sales & Customers Breakdown -->
        <div class="row g-4 mb-4">
            <!-- Sales Breakdown -->
            <div class="col-lg-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="card-label"><i class="bi bi-graph-up"></i> Sales History</div>
                        <small class="text-muted"><?= (!empty($from) && !empty($to)) ? "Filtered" : "Last 6 Months" ?></small>
                    </div>
                    <div class="card-body-scroll">
                        <table class="table table-hover text-center">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($salesBreakdown): ?>
                                    <?php foreach ($salesBreakdown as $row): ?>
                                        <tr>
                                            <td><?= $row['sale_month'] ?></td>
                                            <td class="fw-bold">₱<?= number_format($row['total'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" class="py-4 text-muted">No sales data found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Customer Breakdown -->
            <div class="col-lg-6">
                <div class="data-card">
                    <div class="data-card-header flex-column flex-sm-row align-items-start align-items-sm-center gap-2">
                        <div class="card-label"><i class="bi bi-people"></i> Top Customers</div>
                        <!-- Mini Filter for this card -->
                        <form method="GET" class="d-flex gap-2">
                            <input type="date" name="from_date" class="form-control form-control-sm" style="width: 110px;" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
                            <input type="date" name="to_date" class="form-control form-control-sm" style="width: 110px;" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">
                            <button class="btn btn-custom btn-sm py-1" type="submit">Go</button>
                        </form>
                    </div>
                    <div class="card-body-scroll">
                        <table class="table table-hover text-center">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Orders</th>
                                    <th>Spent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($customerBreakdown)): ?>
                                    <?php foreach ($customerBreakdown as $cust): ?>
                                        <tr>
                                            <td class="text-start ps-4"><?= htmlspecialchars($cust['username']) ?></td>
                                            <td><?= $cust['purchases'] ?></td>
                                            <td class="fw-bold">₱<?= number_format($cust['total_spent'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="py-4 text-muted">No data available.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 2: Stock & Top Selling -->
        <div class="row g-4 mb-4">
            <!-- Stock by Category -->
            <div class="col-lg-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="card-label"><i class="bi bi-box-seam"></i> Stock by Category</div>
                    </div>
                    <div class="card-body-scroll">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="ps-4">Category</th>
                                    <th class="text-end pe-4">Total Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stockPerCategory as $row): ?>
                                    <tr>
                                        <td class="ps-4"><?= htmlspecialchars($row['category']) ?></td>
                                        <td class="text-end pe-4"><?= $row['total_quantity'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Selling -->
            <div class="col-lg-6">
                <div class="data-card">
                    <div class="data-card-header">
                        <div class="card-label"><i class="bi bi-trophy"></i> Best Sellers</div>
                    </div>
                    <div class="card-body-scroll">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="ps-4">Medicine</th>
                                    <th class="text-end pe-4">Units Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topSelling as $med): ?>
                                    <tr>
                                        <td class="ps-4"><?= htmlspecialchars($med['name']) ?></td>
                                        <td class="text-end pe-4 fw-bold"><?= $med['sold'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 3: Alerts (Low Stock / Expiring) -->
        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <div class="data-card border-danger" style="border-width: 0 0 0 4px;">
                    <div class="data-card-header">
                        <div class="card-label text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Low Stock (≤10)</div>
                    </div>
                    <div class="card-body-scroll">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="ps-4">Medicine</th>
                                    <th class="text-end pe-4">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($lowStock): ?>
                                    <?php foreach ($lowStock as $med): ?>
                                        <tr>
                                            <td class="ps-4"><?= htmlspecialchars($med['name']) ?></td>
                                            <td class="text-end pe-4 text-danger fw-bold"><?= $med['quantity'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" class="text-center py-3 text-muted">Stocks are healthy.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="data-card border-warning" style="border-width: 0 0 0 4px;">
                    <div class="data-card-header">
                        <div class="card-label text-warning"><i class="bi bi-clock-history"></i> Expiring Soon (30 Days)</div>
                    </div>
                    <div class="card-body-scroll">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="ps-4">Medicine</th>
                                    <th class="text-end pe-4">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($aboutToExpire): ?>
                                    <?php foreach ($aboutToExpire as $med): ?>
                                        <tr>
                                            <td class="ps-4"><?= htmlspecialchars($med['name']) ?></td>
                                            <td class="text-end pe-4 fw-bold"><?= htmlspecialchars($med['expiry_date']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" class="text-center py-3 text-muted">No expiring items.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Full Sales Record -->
        <div class="data-card mb-5 h-auto">
            <div class="data-card-header d-block d-md-flex">
                <div class="card-label mb-3 mb-md-0"><i class="bi bi-clipboard-data"></i> All Sales Records</div>
                
                <form method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                    <select name="filter_user" class="form-select" style="width: auto;">
                        <option value="">All Users   </option>
                        <?php foreach ($allUsers as $user): ?>
                            <option value="<?= htmlspecialchars($user) ?>" <?= (isset($_GET['filter_user']) && $_GET['filter_user'] === $user) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="text" name="filter_medicine" class="form-control" placeholder="Medicine Name" style="width: 150px;" value="<?= htmlspecialchars($_GET['filter_medicine'] ?? '') ?>">
                    
                    <div class="input-group" style="width: auto;">
                        <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
                        <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">
                    </div>

                    <button class="btn btn-custom" type="submit">Filter</button>
                    <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'excel'])) ?>" class="btn btn-green"><i class="bi bi-file-earmark-spreadsheet"></i> Export</a>
                </form>
            </div>
            
            <div class="card-body-scroll" style="max-height: 500px;">
                <table class="table table-hover text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Medicine</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($allSales): ?>
                            <?php foreach ($allSales as $sale): ?>
                                <tr>
                                    <td>#<?= $sale['sale_id'] ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($sale['username']) ?></td>
                                    <td><?= htmlspecialchars($sale['medicine']) ?></td>
                                    <td><?= $sale['quantity'] ?></td>
                                    <td>₱<?= number_format($sale['total_price'], 2) ?></td>
                                    <td class="text-muted small"><?= $sale['sale_date'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="py-5 text-muted">No records found matching criteria.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="height: 50px;"></div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>