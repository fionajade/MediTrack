<?php
$title = "Reviews";
$page_title = "Reviews";

session_start();
include("connect.php");

// Only allow admin access
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login.php");
  exit();
}

// --- DATA FETCHING LOGIC ---

$filterDate = $_GET['filter_date'] ?? null; 
$reviews = [];
$dbError = false;

// 1. Try to fetch from Database
try {
    $sql = "SELECT s.*, u.username
            FROM sms_incoming s
            LEFT JOIN tbl_user u
              ON REPLACE(u.contact, '+63', '0') = REPLACE(s.sender, '+63', '0')
            WHERE 1=1";

    $params = [];

    if ($filterDate) {
        $sql .= " AND DATE(r.created_at) = ?";
        $params[] = $filterDate;
    }

    $sql .= " ORDER BY s.received_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // If DB fails (table doesn't exist yet), we fall back to dummy data
    // error_log($e->getMessage()); 
    $dbError = true; 
}

// 2. Fallback to Dummy Data if DB result is empty (for demonstration)
if (empty($reviews)) {
    $allReviews = [
        [
            'username' => 'Fiona Jade',
            'comment' => 'Great service and fast delivery. The medicine was exactly what I needed.',
            'category' => 'Analgesics',
            'created_at' => '2026-01-08 14:30:00'
        ],
        [
            'username' => 'Doxi Wave',
            'comment' => 'The packaging was secure, but the delivery took a bit longer than expected.',
            'category' => 'Vitamins',
            'created_at' => '2026-01-05 09:15:00'
        ],
    ];

    // Apply Filter to Dummy Data
    if ($filterDate) {
        foreach ($allReviews as $rev) {
            if (date('Y-m-d', strtotime($rev['created_at'])) === $filterDate) {
                $reviews[] = $rev;
            }
        }
    } else {
        // If no filter, show all (or if DB failed and we want to show demo data)
        // Only show dummy data if we didn't actually run a successful query that just happened to have 0 results
        // For this template, we'll force show dummy data if array is empty so the UI looks populated.
        $reviews = $allReviews;
    }
}

// Get User Name
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
        
        <!-- Header & Filter -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-3">
            <div>
                <p class="page-title-pre">Customer Feedback</p>
                <h1 class="page-title mb-0">Reviews</h1>
            </div>
            
            <!-- Date Filter Form -->
            <form method="GET" class="filter-container mt-3 mt-md-0">
                <?php if ($filterDate): ?>
                    <a href="reviews.php" class="btn btn-outline-secondary btn-sm">Show All</a>
                <?php endif; ?>

                <div class="input-group">
                    <input type="date" name="filter_date" class="form-control" value="<?= htmlspecialchars($filterDate) ?>" required>
                    <button type="submit" class="btn btn-custom">Go</button>
                </div>
            </form>
        </div>
        
        <hr>

        <!-- Reviews Card -->
        <div class="review-card">

            <!-- Desktop Header Row -->
            <div class="review-list-header">
                <div class="col-content">User & Review</div>
                <div class="col-category">Category</div>
                <div class="col-date">Date</div>
            </div>

            <!-- Review Items -->
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $row): ?>
                    <div class="review-item">
                        
                        <!-- Content -->
                        <div class="col-content mb-2 mb-md-0">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-circle fs-4 text-secondary"></i>
                                <span class="review-user mb-0"><?= htmlspecialchars($row['username']) ?></span>
                            </div>
                            <div class="review-text mt-2 ps-md-5">
                                <?= htmlspecialchars($row['comment']) ?>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="col-category mb-2 mb-md-0 ps-md-0 ps-5">
                            <span class="review-category">
                                <?= htmlspecialchars($row['category'] ?? 'General') ?>
                            </span>
                        </div>

                        <!-- Date -->
                        <div class="col-date ps-md-0 ps-5">
                            <span class="review-date justify-content-md-end">
                                <i class="bi bi-calendar3"></i> 
                                <?= date('M d, Y', strtotime($row['created_at'])) ?>
                            </span>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-chat-square-quote fs-1 mb-3 d-block opacity-25"></i>
                    <h5>No reviews found.</h5>
                    <p class="small">Try selecting a different date or clear the filter.</p>
                </div>
            <?php endif; ?>

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

      </main>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
