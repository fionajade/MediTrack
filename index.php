<?php $title = "Pill and Pestle Login"; ?>

<?php
include("connect.php");
session_start();

$error = "";

if (isset($_POST['btnLogin'])) {
    $username = str_replace("'", "", $_POST['username']);
    $password = str_replace("'", "", $_POST['password']);

    $loginQuery = "SELECT * FROM tbl_user WHERE username = '$username' AND password = '$password'";
    $loginResult = executeQuery($loginQuery);

    if (mysqli_num_rows($loginResult) > 0) {
        while ($user = mysqli_fetch_assoc($loginResult)) {
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == "user") {
                header("Location: login.php");
                exit();
            } elseif ($user['role'] == "admin") {
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Invalid role";
            }
        }
    } else {
        $error = "Invalid username or password";
    }
}
?>

<?php include 'user_header.php'; ?>

<body>

    <div class="video-side">
        <video autoplay muted loop>
            <source src="assets/start_video.mp4" type="video/mp4">
        </video>
    </div>

    <div class="form-overlay">
        <div class="login-card">
            <h3 class="text-center mb-4">Login to Pill and Pestle</h3>
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" id="username" required
                        placeholder="Enter your username" />
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" required />
                </div>

                <?php if (!empty($error)): ?>

                        <div class="error-message"><?= $error ?></div>
            <?php endif; ?>

            <button type="submit" name="btnLogin" class="btn btn-primary w-100 mt-3">Login</button>

        </form>
        <p class="text-center mt-3">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
        </div>
</div>
    
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>