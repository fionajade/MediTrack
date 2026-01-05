<?php
include("connect.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack - Your Trusted Pharmacy</title>
    <link rel="stylesheet" href="shared/css/nav.css">
</head>
<style>
    @font-face {
        font-family: 'SF Pro Display';
        src: url('assets/SF-Pro-Display.ttf') format('truetype');
        font-weight: 400;
        font-style: normal;
    }

    @font-face {
        font-family: 'SF Pro Display';
        src: url('assets/SF-Pro-Display-Regular.otf') format('opentype');
        font-weight: 600;
        font-style: normal;
    }

</style>

<body>
    <div class="top-bar">MediTrack</div>

    <div class="container">
        <header>
            <a href="#" class="text-decoration-none">
                <img src="assets/medilogo.png" height="20" class="me-2">
            </a>

            <div class="search-container">
                <span class="search-icon">🔍</span>
                <input type="text" placeholder="Search Medicine">
            </div>

            <nav>
                <a href="index.php" onclick="loadLandingPage()">Home</a>
                <a href="view_medicines.php">Medicines</a>
                <div class="user-circle"></div>
            </nav>
        </header>

        <div class="hero">
            <h1>Welcome to<br>MediTrack</h1 style="font-size: clamp(3rem, 10vw, 9rem);">
            <p>For Every Family. For Every Health Need.</p>
            <div class="hero-banner">
                <video autoplay muted loop playsinline>
                    <source src="assets/video_banner.mp4" type="video/mp4">
                </video>
                <a href="view_medicines.php"><button class="hero-btn">Get Started</button></a>
            </div>
        </div>

        <div class="about-div">
            <h2>About</h2>
            <p class="about-description">
                Welcome to MediTrack – your trusted partner in accessing affordable and reliable medicines online. We
                make it simple for you to browse, learn about, and order the health products you need, all from the
                comfort of your home.
            </p>

            <div class="cards-grid">
                <div class="card">
                    <h1>Our<br>Commitment</h1>
                    <p>To become the most trusted and convenient online pharmacy platform in the Philippines — where
                        customers feel confident, informed, and cared for.</p>
                </div>

                <div class="card">
                    <h1>Why Choose<br>MediTrack?</h1>
                    <p>Explore a wide selection of medicines organized by category for easier navigation</p><br>
                    <p>Check real-time stock availability before you buy</p><br>
                    <p>Understand each product better with helpful details and transparent pricing</p>
                </div>

                <div class="card">
                    <h1>Our Vision</h1>
                    <p>We aim to empower every customer with a hassle-free and informative pharmacy experience. Whether
                        you're managing prescriptions or looking for over-the-counter remedies, MediTrack is here to
                        help every step of the way.</p>
                </div>
            </div>
        </div>

        <div class="project-info">
            <h2>Project Information</h2>
            <p>MediTrack Pharmacy is a final project developed as a course requirement...</p>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-contact">
                    <p>Email: <a href="mailto:support@meditrack.com">support@meditrack.com</a></p>
                    <p>Phone: +63 912 345 6789</p>
                    <p>123 Health St., Makati City, Philippines</p>
                </div>
                <div class="footer-right">
                    <div class="footer-logo">
                        <div class="logo-icon" style="width:15px; height:15px;"></div>
                    </div>
                    <p>Your health, our priority — trusted care from MediTrack Pharmacy.</p>
                </div>
            </div>
            <div class="copyright">
                © 2025 MediTrack Pharmacy. All rights reserved.
            </div>
        </div>
    </footer>

    <?php include 'chatbot.php'; ?>

</body>
</html>