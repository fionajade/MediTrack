<?php $title = "Edit Account"; ?>

<?php
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
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);

    if (empty($username) ||  empty($contact) || empty($email) || empty($address) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("UPDATE tbl_user SET username = ?, contact = ?, email = ?, address = ?, password = ? WHERE userID = ?");
        $stmt->execute([$username, $contact, $email, $address, $password, $userID]);
        $success = "Account updated successfully.";
        $_SESSION['username'] = $username; 
    }
}

$stmt = $pdo->prepare("SELECT username, contact, email, address, password FROM tbl_user WHERE userID = ?");
$stmt->execute([$userID]);
$user = $stmt->fetch();
?>

<?php include 'user_header.php'; ?>

<body>
    <div class="top-bar">MediTrack</div>
    <div class="container">
        <?php include 'client_navbar.php'; ?>
        <div class="row">
            <div class="col p-5">
                <div class="container shadow p-5">
                    <h2>Edit Account</h2>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php elseif (isset($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <form method="POST" action="edit_account.php">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" id="username" required
                                value="<?= htmlspecialchars($user['username']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" class="form-control" id="email" required
                                value="<?= htmlspecialchars($user['email']) ?>">
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
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="text" name="password" class="form-control" id="password" required
                                value="<?= htmlspecialchars($user['password']) ?>">
                        </div>
                        <button type="submit" name="btnUpdateAccount" class="btn btn-primary">Update Account</button>
                        <a href="<?= $_SESSION['previous_page'] ?? 'index.php' ?>"
                            class="btn btn-secondary ms-2">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <?php include 'chatbot.php'; ?>
    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>