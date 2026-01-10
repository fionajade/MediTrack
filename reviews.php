<?php
session_start();
include("connect.php");

// Security Check
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$filterDate = $_GET['filter_date'] ?? null; // Get the specific date from URL

$reviews = [];

/* --- DATABASE LOGIC (UNCOMMENT WHEN CONNECTED) ---
try {
    $sql = "SELECT r.*, u.username 
            FROM reviews r 
            JOIN tbl_user u ON r.user_id = u.userID 
            WHERE 1=1";

    $params = [];

    // Filter by specific date if set
    if ($filterDate) {
        $sql .= " AND DATE(r.created_at) = ?";
        $params[] = $filterDate;
    }

    $sql .= " ORDER BY r.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle error
}
*/

// --- DUMMY DATA (For display purposes) ---
if (empty($reviews)) {
    // Sample data
    $allReviews = [
        [
            'username' => 'User name',
            'comment' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin accumsan volutpat augue.',
            'category' => 'Analgesics',
            'created_at' => '2026-01-09'
        ],
        [
            'username' => 'John Doe',
            'comment' => 'Great service and fast delivery. The medicine was exactly what I needed.',
            'category' => 'Antibiotics',
            'created_at' => '2026-01-08'
        ],
        [
            'username' => 'Jane Smith',
            'comment' => 'The packaging was secure, but the delivery took a bit longer than expected.',
            'category' => 'Vitamins',
            'created_at' => '2026-01-05'
        ]
    ];

    if ($filterDate) {
        foreach ($allReviews as $rev) {
            if ($rev['created_at'] === $filterDate) {
                $reviews[] = $rev;
            }
        }
    } else {
        $reviews = $allReviews;
    }
}

$displayName = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin';
?>

<?php include('shared/admin/admin_header.php'); ?>

<body>

    <?php include('admin_sidebar.php'); ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <!-- HEADER SECTION -->
        <div class="header-section align-items-end">
            <div>
                <p class="mb-0 text-muted">Customer Feedback</p>
                <h1 class="page-title">Reviews</h1>
            </div>

            <!-- SINGLE DATE FILTER -->
            <form method="GET" class="filter-container">
                <?php if ($filterDate): ?>
                    <a href="reviews.php" class="btn-reset">Show All</a>
                <?php endif; ?>

                <input type="date" name="filter_date" class="date-input" value="<?= htmlspecialchars($filterDate) ?>"
                    required>

                <button type="submit" class="btn-go">Go</button>
            </form>
        </div>

        <div class="divider-line"></div>

        <!-- REVIEWS CARD -->
        <div class="reviews-container">

            <!-- Header Row -->
            <div class="review-list-header">
                <div class="col-content">Reviews</div>
                <div class="col-category">Category</div>
                <div class="col-date">Date</div>
            </div>

            <!-- Content Rows -->
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $row): ?>
                    <div class="review-item">
                        <!-- Name & Comment -->
                        <div class="col-content">
                            <span class="review-user"><?= htmlspecialchars($row['username']) ?></span>
                            <div class="review-text">
                                <?= htmlspecialchars($row['comment']) ?>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="col-category">
                            <?= htmlspecialchars($row['category']) ?>
                        </div>

                        <!-- Date -->
                        <div class="col-date">
                            <?= date('M d, Y', strtotime($row['created_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="review-item justify-content-center text-center">
                    <div style="padding: 30px 0;">
                        <span class="review-user" style="font-size: 1.2rem;">No reviews found.</span>
                        <p class="review-text">There are no reviews for the selected date.</p>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <div style="height: 50px;"></div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>