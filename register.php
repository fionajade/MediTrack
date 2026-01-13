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
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // 🔍 Check if username OR email already exists
    $checkQuery = "SELECT userID FROM tbl_user WHERE username = '$username' OR email = '$email'";
    $checkResult = executeQuery($checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $error = "Username or email already exists.";
    } else {

        // ➕ 1. INSERT INTO LOCAL DATABASE (MAC)
        $insertQuery = "INSERT INTO tbl_user
            (username, email, password, address, contact, role)
            VALUES
            ('$username', '$email', '$password', '$address', '$contact', 'user')";

        // Check if LOCAL insertion worked
        if (executeQuery($insertQuery)) {

            $userID = mysqli_insert_id($conn);

            // Auto-login
            $_SESSION['userID'] = $userID;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'user';

            // 🔗 SEND DATA TO HOROLOGE API
            $apiUrl = "http://172.20.10.6/Horologe/api.php";

            // Split username into fname / lname (best-effort)
            $nameParts = explode(" ", $username, 2);
            $fname = $nameParts[0];
            $lname = $nameParts[1] ?? "";

            $postData = [
                'fname' => $fname,
                'lname' => $lname,
                'email' => $email,
                'password' => $password, // keep plain OR hash both sides consistently
                'phone_number' => $contact
            ];

            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $apiResponse = curl_exec($ch);
            curl_close($ch);

            // 📧 PREPARE EMAIL CONTENT
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'piyoacadnotes@gmail.com';
                $mail->Password   = 'zdzr kzod gqti yuji'; // Keep your App Password safe!
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('piyoacadnotes@gmail.com', 'MediTrack Security');
                $mail->addAddress($email, $username);

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
                $mail->send();

            } catch (Exception $e) {
                // Use $mail->ErrorInfo to see specific error if needed
                $successMessage = "Registered successfully, but email could not be sent. Error: " . $mail->ErrorInfo;
            }

            $successMessage = "Registration successful! Redirecting...";
            header("Refresh: 3; URL=index.php");

        } else {
            $error = "Failed to register local account. Please try again.";
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
                    <input type="tel" name="contact" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center mb-3"><?= $error ?></div>
                <?php endif; ?>
                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success text-center mb-3"><?= $successMessage ?></div>
                <?php endif; ?>
                <button type="submit" name="btnRegister" class="btn btn-primary w-100">Register</button>
            </form>
            <p class="text-center mt-3">Already registered? <a href="index.php">Login here</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>