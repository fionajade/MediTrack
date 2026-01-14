<?php
$title = "Pill and Pestle Reviews";
$subhead = "Customer Feedback";
$page_title = "Reviews";

session_start();
include("connect.php");

/* Debug (remove later) */
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Security Check
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Filters
$filterDate     = $_GET['filter_date'] ?? '';
$filterSender   = $_GET['sender'] ?? '';
$filterOrder    = $_GET['order_id'] ?? '';
$filterPayment  = $_GET['payment_id'] ?? '';

$feedback = [];

/* ================= FETCH SMS FEEDBACK ================= */
try {
    $sql = "SELECT s.*, u.username
            FROM sms_incoming s
            LEFT JOIN tbl_user u
              ON REPLACE(u.contact, '+63', '0') = REPLACE(s.sender, '+63', '0')
            WHERE 1=1";

    $params = [];

    if (!empty($filterSender)) {
        $sql .= " AND s.sender = ?";
        $params[] = $filterSender;
    }

    if (!empty($filterOrder)) {
        $sql .= " AND s.order_id = ?";
        $params[] = $filterOrder;
    }

    if (!empty($filterPayment)) {
        $sql .= " AND s.payment_id = ?";
        $params[] = $filterPayment;
    }

    if (!empty($filterDate)) {
        $sql .= " AND DATE(s.received_at) = ?";
        $params[] = $filterDate;
    }

    $sql .= " ORDER BY s.received_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Feedback Error: " . $e->getMessage());
}

$displayName = htmlspecialchars($_SESSION['username']);
include('shared/admin/admin_header.php');
?>

<body>

<?php include('admin_sidebar.php'); ?>

<div class="main-content">

    <div class="d-flex justify-content-between align-items-center">
        <div>
            <?php include 'shared/admin/admin_page_title.php'; ?>
        </div>

        <form method="GET" class="filter-container">
            <?php if (!empty($filterDate) || !empty($filterSender) || !empty($filterOrder) || !empty($filterPayment)): ?>
                <a href="reviews.php" class="btn-reset">Show All</a>
            <?php endif; ?>

            <input type="date" name="filter_date" class="date-input"
                   value="<?= htmlspecialchars($filterDate) ?>">

            <input type="text" name="sender" placeholder="Filter by sender"
                   value="<?= htmlspecialchars($filterSender) ?>">

            <input type="text" name="order_id" placeholder="Filter by order ID"
                   value="<?= htmlspecialchars($filterOrder) ?>">

            <input type="text" name="payment_id" placeholder="Filter by payment ID"
                   value="<?= htmlspecialchars($filterPayment) ?>">

            <button type="submit" class="btn-go">Go</button>
        </form>
    </div>

    <div class="divider-line"></div>

    <div class="reviews-container">

        <div class="review-list-header">
            <div class="col-content">Reviews / Feedback</div>
            <div class="col-category">Order ID</div>
            <div class="col-category">Payment ID</div>
            <div class="col-date">Date</div>
        </div>

        <?php if (!empty($feedback)): ?>
            <?php foreach ($feedback as $row): ?>
                <div class="review-item">
                    <div class="col-content">
                        <span class="review-user">
                            <?= htmlspecialchars($row['username'] ?? $row['sender']) ?>
                        </span>
                        <div class="review-text">
                            <?= htmlspecialchars($row['message']) ?>
                        </div>
                    </div>

                    <div class="col-category">
                        <?= htmlspecialchars($row['order_id'] ?? '-') ?>
                    </div>

                    <div class="col-category">
                        <?= htmlspecialchars($row['payment_id'] ?? '-') ?>
                    </div>

                    <div class="col-date">
                        <?= date('M d, Y H:i', strtotime($row['received_at'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="review-item justify-content-center text-center">
                <div style="padding: 30px 0;">
                    <span class="review-user" style="font-size: 1.2rem;">
                        No reviews or feedback found.
                    </span>
                    <p class="review-text">Try changing the filters above.</p>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <div style="height: 50px;"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
