<link rel="stylesheet" href="shared/css/nav.css">

<header>
  <a href="login.php" class="text-decoration-none">
    <img src="assets/medilogo.png" height="20" class="me-2">
  </a>

  <div class="search-container">
    <span class="search-icon">🔍</span>
    <input type="text" id="navbarSearch" placeholder="Search Medicine">
  </div>

  <nav>
    <a href="login.php" onclick="loadLandingPage()"
      style="text-decoration: none; color: #002147; font-size: 18px; font-weight: 500;">Home</a>
    <a href="#about" 
          style="text-decoration: none; color: #002147; font-size: 18px; font-weight: 500;">About</a>
    <a href="view_medicines.php"
      style="text-decoration: none; color: #002147; font-size: 18px; font-weight: 500;">Medicines</a>
    <ul class="navbar-nav">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
          <img src="assets/user.png" alt="User" class="rounded-circle" width="30" height="30">
          <span class="ms-2" style="text-decoration: none; color: #002147; font-size: 18px; font-weight: 500;">Settings
          </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="edit_account.php">Edit Account</a></li>
          <li><a class="dropdown-item" href="logout.php">Log Out</a></li>
        </ul>
      </li>
    </ul>
  </nav>
</header>