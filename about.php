<?php include("connect.php"); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>About - MediTrack</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="assets/medi_logo.png">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="shared/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f7fa;
      color: #052241;
    }

    .about-container {
      background: white;
      border-radius: 15px;
      padding: 40px;
      margin-top: 50px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .about-container h1,
    .about-container h3 {
      color: #052241;
      font-weight: 700;
    }

    .about-container ul {
      padding-left: 20px;
    }

    .about-container p,
    .about-container li {
      font-size: 1.1rem;
      line-height: 1.6;
    }

    .info-card {
      border: none;
      margin-top: 40px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
      border-radius: 15px;
    }

    .info-card .card-header {
      background: linear-gradient(90deg, #052241, #0c3a60);
      color: white;
      border-top-left-radius: 15px;
      border-top-right-radius: 15px;
    }
  </style>
</head>
<body>

<?php include 'client_navbar.php'; ?>

<!-- About Content Section -->
<div class="container about-container">
  <h1>About MediTrack</h1>
  <p class="lead">Welcome to MediTrack – your trusted partner in accessing affordable and reliable medicines online. We make it simple for you to browse, learn about, and order the health products you need, all from the comfort of your home.</p>

  <h3 class="mt-4">Why Choose MediTrack?</h3>
  <ul>
    <li>Explore a wide selection of medicines organized by category for easier navigation</li>
    <li>Check real-time stock availability before you buy</li>
    <li>Understand each product better with helpful details and transparent pricing</li>
  </ul>

  <h3 class="mt-4">Our Commitment</h3>
  <p>We aim to empower every customer with a hassle-free and informative pharmacy experience. Whether you're managing prescriptions or looking for over-the-counter remedies, MediTrack is here to help every step of the way.</p>

  <h3 class="mt-4">Our Vision</h3>
  <p>To become the most trusted and convenient online pharmacy platform in the Philippines — where customers feel confident, informed, and cared for.</p>

  <!-- Course Requirement Info Card -->
  <div class="card info-card">
    <div class="card-header">
      <h5 class="mb-0"><i class="bi bi-info-circle-fill me-2"></i>Project Information</h5>
    </div>
    <div class="card-body">
      <p><strong>MediTrack Pharmacy</strong> is a final project developed as a course requirement by the following BS Information Technology 2-2 students: <strong>Ilagan, Jan Maridel T.</strong>, <strong>Mercado, Jerome P.</strong>, <strong>Marasigan, Marcus Gabriel O.</strong>, and <strong>Villanueva, Fiona Jade M.</strong> of the <strong>Polytechnic University of the Philippines - Sto. Tomas Campus</strong>.</p>
      <p>This project represents the application of their learning in real-world software development — focusing on customer service, pharmacy operations, inventory management, and e-commerce functionality.</p>
      <p>It showcases the use of web technologies such as HTML, CSS, Bootstrap, JavaScript, PHP, and MySQL, aiming to bridge the gap between classroom theory and practical solutions in healthcare accessibility.</p>
    </div>

  </div>
</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
