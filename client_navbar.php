<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3 px-4">
  <div class="container-fluid justify-content-between">
    <div class="d-flex align-items-center">
      <img src="assets/medi_logo.png" alt="Logo" width="40" height="40" class="me-2">
      <h2 class="mb-0 text-white">MediTrack</h2>
    </div>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse show" id="navbarSupportedContent">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link active" href="index.php" onclick="loadLandingPage()">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="view_medicines.php">Medicines</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
            <img src="assets/user.png" alt="User" class="rounded-circle" width="30" height="30">
            <span class="ms-2">Settings</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="edit_account.php">Edit Account</a></li>
            <li><a class="dropdown-item" href="logout.php">Log Out</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>