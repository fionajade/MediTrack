<?php
include("connect.php");
session_start();

$_SESSION['userID'] = "";
$_SESSION['username'] = "";
$_SESSION['role'] = "";

$error = "";

if (isset($_POST['btnRegister'])) {
    $username = str_replace("'", "", $_POST['username']);
    $contact = str_replace("'", "", $_POST['contact']);
    $address = str_replace("'", "", $_POST['address']);
    $password = str_replace("'", "", $_POST['password']);

    $checkQuery = "SELECT * FROM tbl_user WHERE username = '$username'";
    $checkResult = executeQuery($checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        header("Location: register.php?error=exists");
        exit();
    } else {
        $insertQuery = "INSERT INTO tbl_user (username, password, address, contact, role)
                        VALUES ('$username', '$password', '$address', '$contact', 'user')";
        executeQuery($insertQuery);
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>MediTrack Registration</title>
    <link rel="icon" href="assets/medi_logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
            /* font-weight: bold; */
        }

        .container-wrapper {
            display: flex;
            height: 100vh;
            width: 100vw;

        }

        .video-side {
            flex: 1;
            position: relative;
        }

        .video-side video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .form-side {
            width: 500px;
            max-width: 100%;
            background-color: #eef4f4;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .form-control {
            font-family: 'Poppins', sans-serif;
            border-radius: 2rem;
        }

        .btn-success {
            font-family: 'Poppins', sans-serif;
            border-radius: 2rem;
        }

        .register-card {
            width: 100%;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .container-wrapper {
                flex-direction: column;
            }

            .video-side {
                height: 50vh;
            }

            .form-side {
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>
<body>

<div class="container-wrapper">
    <!-- 🔵 Left Video Panel -->
    <div class="video-side">
        <video autoplay muted loop>
            <source src="assets/start_video.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <!-- 🟢 Right Registration Form -->
    <div class="form-side">
        <div class="register-card" font="Verdana">
            <h3 class="text-center mb-4">Register for MediTrack</h3>
            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" id="username" required />
                </div>
                <div class="mb-3">
                    <label for="contact" class="form-label">Contact Number</label>
                    <input type="tel" name="contact" class="form-control" id="contact" required pattern="[0-9]{10,15}" title="Please enter a valid contact number" />
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea name="address" class="form-control" id="address" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" required />
                </div>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'exists'): ?>
                    <div class="error-message">Username already exists. Please choose another.</div>
                <?php endif; ?>
                <button type="submit" name="btnRegister" class="btn btn-success w-100">Register</button>
            </form>
            <p class="text-center mt-3">
                Already registered? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
