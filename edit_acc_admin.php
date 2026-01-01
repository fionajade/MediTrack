<?php
include("connect.php");
session_start();

$userID = $_SESSION['userID'] ?? null;
if (!$userID) {
    header("Location: login.php");
    exit;
}

// Store referrer once (only if not already stored)
if (!isset($_SESSION['previous_page']) && isset($_SERVER['HTTP_REFERER'])) {
    $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($contact) || empty($address) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("UPDATE tbl_user SET username = ?, contact = ?, address = ?, password = ? WHERE userID = ?");
        $stmt->execute([$username, $contact, $address, $password, $userID]);
        $success = "Account updated successfully.";
        $_SESSION['username'] = $username; // update session username
    }
}

// Fetch current user data
$stmt = $pdo->prepare("SELECT username, contact, address, password FROM tbl_user WHERE userID = ?");
$stmt->execute([$userID]);
$user = $stmt->fetch();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Account</title>
    <link rel="icon" href="assets/medi_logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fa;
        }
        .main-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }

        .card {
            font-family: 'Poppins', sans-serif;
            border: none;
            border-radius: 1rem;
        }

        .card-header {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ADFCF9, #064278);
            color: white;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            padding: 1.25rem;
        }

        .card-body {
            font-family: 'Poppins', sans-serif;
            padding: 2rem;
        }

        .form-label {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            color: #052241;
        }

        .btn-primary {
            font-family: 'Poppins', sans-serif;
            background-color: #052241;
            border: none;
        }

        .btn-primary:hover {
            background-color: #04192d;
        }
        .container {
            max-width: 1200px;
            }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main-wrapper">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card shadow-lg">
          <div class="card-header">
            <h3 class="mb-0"><i class="bi bi-person-gear me-2"></i>Edit Account</h3>
          </div>
          <div class="card-body">

            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?= $error ?></div>
            <?php elseif (isset($success)): ?>
              <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" action="edit_acc_admin.php">
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" id="username" required
                       value="<?= htmlspecialchars($user['username']) ?>">
              </div>
              <div class="mb-3">
                <label for="contact" class="form-label">Contact</label>
                <input type="text" name="contact" class="form-control" id="contact" required
                       value="<?= htmlspecialchars($user['contact']) ?>">
              </div>
              <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" name="address" class="form-control" id="address" required
                       value="<?= htmlspecialchars($user['address']) ?>">
              </div>
              <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="text" name="password" class="form-control" id="password" required
                       value="<?= htmlspecialchars($user['password']) ?>">
              </div>
              <div class="d-flex justify-content-end">
                <button type="submit" name="btnUpdateAccount" class="btn btn-primary">Update</button>
                <!-- <a href="<?= $_SESSION['previous_page'] ?? 'admin_dashboard.php' ?>" class="btn btn-secondary ms-2">Back</a> -->
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>