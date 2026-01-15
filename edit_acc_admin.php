<?php

$title = "Pill and Pestle | Edit Account";
include("connect.php");
session_start();

$userID = $_SESSION['userID'] ?? null;

if (!$userID) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['previous_page']) && isset($_SERVER['HTTP_REFERER'])) {
    $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($contact) || empty($address) || empty($password)) {
        $error = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE tbl_user SET username = ?, contact = ?, address = ?, password = ? WHERE userID = ?");
            $stmt->execute([$username, $contact, $address, $password, $userID]);
            $success = "Account updated successfully.";
            $_SESSION['username'] = $username; 
        } catch (PDOException $e) {
            $error = "Error updating account: " . $e->getMessage();
        }
    }
}

$stmt = $pdo->prepare("SELECT username, contact, address, password FROM tbl_user WHERE userID = ?");
$stmt->execute([$userID]);
$user = $stmt->fetch();

$page_title = "Edit Account";
?>

<?php include("admin_header.php"); ?>
<body>

  <?php include("admin_sidebar_mobile.php"); ?>

  <div class="container-fluid">
    <div class="row">

      <?php include("admin_sidebar_desktop.php"); ?>


      <main class="col-lg-10 col-12 p-4">
        
        <p class="page-title-pre">Settings</p>
        <h1 class="page-title">Edit Account</h1>
        <hr>

        <div class="form-section">
            <div class="light-card">
                <div class="d-flex align-items-center mb-4 text-primary-navy">
                    <i class="bi bi-person-gear fs-3 me-2" style="color: var(--primary-navy);"></i>
                    <h5 class="mb-0" style="color: var(--primary-navy);">Update Profile Details</h5>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i><?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?= $success ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="edit_acc_admin.php">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label text-muted small">Username</label>
                            <input type="text" name="username" class="form-control" id="username" required
                                   value="<?= htmlspecialchars($user['username']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact" class="form-label text-muted small">Contact Number</label>
                            <input type="text" name="contact" class="form-control" id="contact" required
                                   value="<?= htmlspecialchars($user['contact']) ?>">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="address" class="form-label text-muted small">Address</label>
                            <input type="text" name="address" class="form-control" id="address" required
                                   value="<?= htmlspecialchars($user['address']) ?>">
                        </div>
                        <div class="col-12 mb-4">
                            <label for="password" class="form-label text-muted small">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock"></i></span>
                                <input type="text" name="password" class="form-control border-start-0 ps-0" id="password" required
                                       value="<?= htmlspecialchars($user['password']) ?>">
                            </div>
                            <div class="form-text">Ensure you use a strong password.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" name="btnUpdateAccount" class="btn btn-custom px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>