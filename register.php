<?php $title = "Pill and Pestle - Your Trusted Pharmacy"; ?>

<?php
include("connect.php");
session_start();

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

    $checkQuery = "SELECT userID FROM tbl_user WHERE username = '$username' OR email = '$email'";
    $checkResult = executeQuery($checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $error = "Username or email already exists.";
    } else {

        $insertQuery = "INSERT INTO tbl_user
            (username, email, password, address, contact, role)
            VALUES
            ('$username', '$email', '$password', '$address', '$contact', 'user')";

        if (executeQuery($insertQuery)) {

            $userID = mysqli_insert_id($conn);

            $_SESSION['userID'] = $userID;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'user';

<<<<<<< HEAD
            // 🔗 SEND DATA TO HOROLOGE API
            $apiUrl = "http://172.20.10.3/Horologe/api.php";
=======
            $apiUrl = "http://172.20.10.8/Horologe/api.php";
>>>>>>> 25635515cdb2f483c20ea7b4ea13eb976c9f13b0

            $nameParts = explode(" ", $username, 2);
            $fname = $nameParts[0];
            $lname = $nameParts[1] ?? "";

            $postData = [
                'fname' => $fname,
                'lname' => $lname,
                'email' => $email,
                'password' => $password,
                'phone_number' => $contact
            ];

            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $apiResponse = curl_exec($ch);
            curl_close($ch);


            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'pillandpestle@gmail.com';
                $mail->Password   = 'sakq hyep fnug vybj';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('pillandpestle@gmail.com', 'Pill and Pestle');
                $mail->addAddress($email, $username);

                $mail->isHTML(true);
                $mail->Subject = 'Welcome to Pill and Pestle!';

                $emailBody = file_get_contents('email_template.html');

                $loginLink = "http://localhost/workspace/pill-and-pestle/";

                $emailBody = str_replace('{{username}}', $username, $emailBody);
                $emailBody = str_replace('{{link}}', $loginLink, $emailBody);

                $mail->Body = $emailBody;
                $mail->send();

            } catch (Exception $e) {
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

<?php include 'user_header.php'; ?>

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