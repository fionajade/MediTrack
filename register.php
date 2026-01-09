<?php
include("connect.php");
session_start();

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


$error = "";
$successMessage = "";

if (isset($_POST['btnRegister'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $contact  = mysqli_real_escape_string($conn, $_POST['contact']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // 🔍 Check if username OR email already exists
    $checkQuery = "SELECT userID FROM tbl_user WHERE username = '$username' OR email = '$email'";
    $checkResult = executeQuery($checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $error = "Username or email already exists.";
    } else {

        // ➕ Insert user
        $insertQuery = "INSERT INTO tbl_user
            (username, email, password, address, contact, role)
            VALUES
            ('$username', '$email', '$password', '$address', '$contact', 'user')";

        if (executeQuery($insertQuery)) {

            $userID = mysqli_insert_id($conn);

            // Auto-login
            $_SESSION['userID'] = $userID;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'user';

            // 📧 PREPARE EMAIL CONTENT
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'piyoacadnotes@gmail.com';
                $mail->Password   = 'zdzr kzod gqti yuji'; // Keep your App Password safe!
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                // Recipients
                $mail->setFrom('piyoacadnotes@gmail.com', 'MediTrack Security');
                $mail->addAddress($email, $username);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Welcome to MediTrack!';

                // ---------------------------------------------------------
                // 1. LOAD THE TEMPLATE
                // ---------------------------------------------------------
                $emailBody = file_get_contents('email_template.html');

                // ---------------------------------------------------------
                // 2. DEFINE THE LINK
                // (Change 'localhost/meditrack' to your actual website URL)
                // ---------------------------------------------------------
                $loginLink = "http://localhost/Workspace/MediTrack/index.php"; 

                // ---------------------------------------------------------
                // 3. REPLACE PLACEHOLDERS
                // ---------------------------------------------------------
                $emailBody = str_replace('{{username}}', $username, $emailBody);
                $emailBody = str_replace('{{link}}', $loginLink, $emailBody);

                $mail->Body = $emailBody;
                $mail->AltBody = "Welcome $username! Your account is created. Please login at $loginLink";

                $mail->send();
                $successMessage = "Registration successful! Welcome email sent.";

            } catch (Exception $e) {
                // Use $mail->ErrorInfo to see specific error if needed
                $successMessage = "Registered successfully, but email could not be sent. Error: " . $mail->ErrorInfo;
            }

            // Redirect after 3 seconds
            header("Refresh: 3; URL=index.php");

        } else {
            $error = "Failed to register. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>MediTrack Registration</title>

    <link rel="icon" href="assets/medlogotop.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="shared/css/login.css" rel="stylesheet">
</head>

<body>

<div class="video-side">
    <video autoplay muted loop>
        <source src="assets/start_video.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>

<div class="form-overlay">
    <div class="register-card">

        <h3 class="text-center mb-4">Register for MediTrack</h3>

        <form action="register.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="tel" name="contact" class="form-control"
                       pattern="[0-9]{10,15}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <!-- 🔔 MESSAGE DISPLAY (ABOVE BUTTON) -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center mb-3">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success text-center mb-3">
                    <?= $successMessage ?>
                </div>
            <?php endif; ?>

            <button type="submit" name="btnRegister" class="btn btn-primary w-100">
                Register
            </button>

        </form>

        <p class="text-center mt-3">
            Already registered? <a href="index.php">Login here</a>
        </p>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
