<?php $title = "Pill and Pestle - Your Trusted Pharmacy"; ?>

<?php
include("connect.php");
?>

<?php include 'user_header.php'; ?>

<body>
    <div class="top-bar">Pill and Pestle</div>

    <div class="container">
        <?php include 'client_navbar.php'; ?>

        <div class="hero">
            <h1>Welcome to<br>Pill and Pestle</h1 style="font-size: clamp(3rem, 10vw, 9rem);">
            <p>For Every Family. For Every Health Need.</p>
            <div class="hero-banner">
                <video autoplay muted loop playsinline>
                    <source src="assets/video_banner.mp4" type="video/mp4">
                </video>
                <a href="view_medicines.php"><button class="hero-btn">Get Started</button></a>
            </div>
        </div>

        <div class="about-div" id="about">
            <h2>About</h2>
            <p class="about-description">
                Welcome to Pill and Pestle – your trusted partner in accessing affordable and reliable medicines online. We
                make it simple for you to browse, learn about, and order the health products you need, all from the
                comfort of your home.
            </p>

            <div class="cards-grid">
                <div class="card"
                    style="border: 1px solid #002552; border-radius: 20px; display: flex; flex-direction: column;">
                    <h1>Our<br>Commitment</h1>
                    <p>To become the most trusted and convenient online pharmacy platform in the Philippines — where
                        customers feel confident, informed, and cared for.</p>
                </div>

                <div class="card"
                    style="border: 1px solid #002552; border-radius: 20px; display: flex; flex-direction: column;">
                    <h1>Why Choose<br>Pill and Pestle?</h1>
                    <p>Explore a wide selection of medicines organized by category for easier navigation</p><br>
                    <p>Check real-time stock availability before you buy</p><br>
                    <p>Understand each product better with helpful details and transparent pricing</p>
                </div>

                <div class="card"
                    style="border: 1px solid #002552; border-radius: 20px; display: flex; flex-direction: column;">
                    <h1>Our Vision</h1>
                    <p>We aim to empower every customer with a hassle-free and informative pharmacy experience. Whether
                        you're managing prescriptions or looking for over-the-counter remedies, Pill and Pestle is here to
                        help every step of the way.</p>
                </div>
            </div>
        </div>

        <div class="project-info">
            <h2>Project Information</h2>
            <p>Pill and Pestle Pharmacy is a final project developed as a course requirement...</p>
        </div>
    </div>


    <?php include 'footer.php'; ?>
    <?php include 'chatbot.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.getElementById("navbarSearch");

            if (searchInput) {
                searchInput.addEventListener("keypress", function (e) {
                    if (e.key === "Enter") {
                        const query = e.target.value.trim();
                        if (query.length > 0) {
                            window.location.href = "view_medicines.php?search=" + encodeURIComponent(query);
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>