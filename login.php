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
                header("Location: index.php");
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>MediTrack Login</title>
    <link rel="icon" href="assets/medi_logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="shared/css/login.css"> -->
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
            border-radius: 2rem;
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

        .btn-primary {
            font-family: 'Poppins', sans-serif;
            border-radius: 2rem;
        }
        .login-card {
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
    <!-- 🔵 Left Video Section -->
    <div class="video-side">
        <video autoplay muted loop>
            <source src="assets/start_video.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <!-- 🟢 Right Form Section -->
    <div class="form-side">
        <div class="login-card">
            <h3 class="text-center mb-4">Login to MediTrack</h3>
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" id="username" required placeholder="Enter your username" />
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" required />
                </div>

                <!-- Error message placed above the Login button -->
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
