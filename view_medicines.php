<?php
include("connect.php");
session_start();

$userID = $_SESSION['user_id'] ?? null;
$user = [
  'username' => '',
  'contact' => '',
  'address' => ''
];

if ($userID) {
  // Assuming $pdo is defined in connect.php
  if (isset($pdo)) {
    $stmt = $pdo->prepare("SELECT username, contact, address FROM tbl_user WHERE userID = ?");
    $stmt->execute([$userID]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userData) {
      $user = $userData;
      $_SESSION['username'] = $user['username'];
      $_SESSION['contact'] = $user['contact'];
      $_SESSION['address'] = $user['address'];
    }
  }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MediTrack | Medicine</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="shared/css/nav.css" rel="stylesheet">
  <style>
    /* --- FONTS --- */
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

    :root {
      --primary-dark: #002147;
      /* Matched to your header color */
      --text-gray: #666;
    }

    /* --- RESET & GLOBAL --- */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'SF Pro Display', sans-serif;
      background-color: #ffffff;
      color: var(--primary-dark);
      overflow-x: hidden;
    }

    a {
      text-decoration: none;
    }

    /* --- HEADER SPECIFIC STYLES --- */
    .top-bar-wrapper {
      width: 100%;
      border-bottom: 1px solid #eee;
      background: white;
    }

    /* The specific container width requested */
    .custom-container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 20px;
    }

    .top-bar-nav {
      padding: 20px 0;
      /* Vertical padding for the header */
    }

    /* Search Bar Styling */
    /* .search-bar-container {
      flex: 1;
      margin: 0 40px;
      position: relative;
    }

    .search-bar-container input {
      width: 100%;
      padding: 10px 20px 10px 40px;
      border: 1px solid #ccc;
      border-radius: 25px;
      outline: none;
    }

    .search-icon {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #999;
      font-size: 14px;
    } */

    /* Navigation Links */
    .nav-link-custom {
      color: var(--primary-dark);
      font-size: 14px;
      font-weight: 500;
      margin-left: 30px;
      transition: 300ms;
    }

    .nav-link-custom:hover {
      color: #004080;
    }

    .user-circle {
      width: 35px;
      height: 35px;
      background-color: var(--primary-dark);
      border-radius: 50%;
      margin-left: 30px;
    }


    /* --- PAGE CONTENT LAYOUT (Keep existing logic) --- */
    .page-title {
      font-size: 3rem;
      font-weight: 800;
      color: var(--primary-dark);
      margin-top: 40px;
      margin-bottom: 30px;
      line-height: 1;
    }

    /* Sidebar Categories */
    .category-list {
      list-style: none;
      padding: 0;
      position: sticky;
      top: 20px;
    }

    .category-item {
      padding: 10px 0;
      cursor: pointer;
      color: #999;
      font-weight: 400;
      transition: all 0.3s ease;
      margin-bottom: 5px;
      position: relative;
    }

    .category-item:hover,
    .category-item.active {
      color: var(--primary-dark);
      padding-left: 15px;
      font-weight: 600;
    }

    .category-item::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 4px;
      height: 0;
      background-color: var(--primary-dark);
      transition: height 0.3s ease;
      border-radius: 2px;
    }

    .category-item:hover::before,
    .category-item.active::before {
      height: 70%;
    }

    /* Medicine Cards */
    .medicine-card {
      background-color: var(--primary-dark);
      color: white;
      border: 1px solid var(--primary-dark);
      border-radius: 20px;
      overflow: hidden;
      height: 320px;
      position: relative;
      cursor: pointer;
      transition: transform 0.2s;
      margin-bottom: 20px;
      display: flex;
      flex-direction: column;
    }

    .medicine-card:hover {
      transform: translateY(-5px);
    }

    .price-tag {
      position: absolute;
      top: 0;
      right: 0;
      background-color: #fff;
      color: var(--primary-dark);
      padding: 5px 15px;
      font-weight: 600;
      border-bottom-left-radius: 15px;
      z-index: 2;
    }

    .med-img-wrapper {
      height: 65%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(4, 30, 66, 0) 70%);
    }

    .med-img-wrapper img {
      max-height: 100%;
      max-width: 100%;
      object-fit: contain;
      filter: drop-shadow(0 10px 10px rgba(0, 0, 0, 0.3));
    }

    .med-info {
      padding: 20px;
      background: linear-gradient(to top, rgba(4, 30, 66, 1) 20%, rgba(4, 30, 66, 0) 100%);
      margin-top: auto;
    }

    .med-name {
      font-size: 1.2rem;
      margin: 0;
      font-weight: 600;
      line-height: 1.2;
    }

    .out-of-stock {
      opacity: 0.6;
      pointer-events: none;
      filter: grayscale(100%);
    }

    /* --- Cart Styling --- */
    .cart-container {
      border: 1px solid #ddd;
      border-radius: 20px;
      padding: 25px;
      background: #fff;
      margin-left: 10px;
    }

    .total-row {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
      font-size: 1.1rem;
    }

    .btn-checkout {
      background-color: var(--primary-dark);
      color: white;
      border: none;
    }

    .btn-checkout:disabled {
      background-color: #ccc;
    }

    .btn-done {
      background-color: var(--primary-dark);
      color: white;
      width: 100%;
      margin-top: 10px;
    }

    .payment-pill {
      background-color: #798da3;
      color: white;
      text-align: center;
      padding: 10px;
      border-radius: 5px;
      margin-top: 10px;
      font-size: 0.9rem;
    }

    /* Footer */
    footer {
      background-color: var(--primary-dark);
      color: white;
      padding: 60px 0 20px;
      margin-top: 80px;
    }

    .footer-content {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 40px;
    }

    .footer-contact p {
      font-size: 14px;
      margin-bottom: 5px;
      color: #fff;
    }

    .footer-contact a {
      color: white;
    }

    .footer-right {
      display: flex;
      align-items: center;
      gap: 15px;
      max-width: 300px;
    }

    .footer-logo {
      width: 40px;
      height: 40px;
      border: 1px solid white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .copyright {
      text-align: center;
      font-size: 10px;
      opacity: 0.6;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      padding-top: 20px;
    }

    #receipt li {
      font-size: 0.9rem;
      border-bottom: 1px solid #eee;
      padding-bottom: 5px;
    }
  </style>
</head>

<body>
  <div class="top-bar">MediTrack</div>

  <!-- Header Wrapper (Full Width Border) as requested -->
  <div class="top-bar-wrapper">
    <!-- Content Container (1100px) -->
    <div class="custom-container">
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
    </div>
  </div>

  <!-- Main Content (1100px) -->
  <div class="custom-container">
    <!-- Title -->
    <h1 class="page-title">Medicine</h1>

    <div class="row">
      <!-- Left Sidebar: Categories -->
      <div class="col-lg-2">
        <ul class="category-list" id="categoryList">
          <li class="category-item active">Loading...</li>
        </ul>
      </div>

      <!-- Center: Medicine Grid -->
      <div class="col-lg-7">
        <div id="medicineGrid" class="row gx-3 gy-4">
          <!-- Javascript will load items here -->
        </div>
        <div id="loadingMeds" class="text-center mt-5" style="display:none;">
          <div class="spinner-border text-primary" role="status"></div>
        </div>
      </div>

      <!-- Right: Cart & Checkout -->
      <div class="col-lg-3">
        <div class="cart-container sticky-top" style="top: 20px;">
          <div class="d-flex align-items-center mb-3">
            <span class="h5 m-0">🛒 Cart</span>
          </div>

          <div class="total-row mb-3">
            <span class="text-muted">TOTAL</span>
            <span class="fw-bold" id="totalValue">P0</span>
          </div>

          <div class="d-flex gap-2 mb-1">
            <button class="btn btn-outline-danger btn-sm flex-grow-1" onclick="clearCart()">Clear</button>
            <button class="btn btn-checkout btn-sm flex-grow-1" id="checkoutBtn" onclick="checkout()"
              disabled>Checkout</button>
          </div>

          <div style="max-height: 150px; overflow-y: auto; margin-bottom: 20px;">
            <ul id="receipt" class="list-unstyled mt-2"></ul>
          </div>

          <hr>

          <!-- Delivery Info -->
          <h6 class="mt-3" style="color:var(--primary-dark); font-weight:bold; font-size: 0.9rem;">Delivery Information
          </h6>

          <!-- Hidden field for order type default -->
          <input type="hidden" id="orderType" value="Delivery">

          <div class="mb-2">
            <label class="form-label" style="font-size:0.8rem">Name</label>
            <input type="text" class="form-control form-control-sm" id="userName"
              value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>">
          </div>

          <div class="mb-2">
            <label class="form-label" style="font-size:0.8rem">Contact</label>
            <input type="text" class="form-control form-control-sm" id="userContact"
              value="<?= htmlspecialchars($_SESSION['contact'] ?? '') ?>">
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-size:0.8rem">Address</label>
            <input type="text" class="form-control form-control-sm" id="userAddress"
              value="<?= htmlspecialchars($_SESSION['address'] ?? '') ?>">
          </div>

          <button class="btn btn-done btn-sm" id="infoToggleBtn" onclick="confirmUserInfo()">Done</button>

          <div class="payment-pill" id="paymentNotice">
            Mode of Payment: Paypal
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer Wrapper -->
  <footer>
    <div class="custom-container">
      <div class="footer-content">
        <div class="footer-contact">
          <p>Email: <a href="mailto:support@meditrack.com">support@meditrack.com</a></p>
          <p>Phone: +63 912 345 6789</p>
          <p>123 Health St., Makati City, Philippines</p>
        </div>
        <div class="footer-right">
          <div class="footer-logo">
            <!-- Simple square icon to match CSS -->
            <div
              style="width: 15px; height: 15px; border: 2px solid white; border-radius: 2px; transform: rotate(45deg);">
            </div>
          </div>
          <p>Your health, our priority — trusted care from MediTrack Pharmacy.</p>
        </div>
      </div>
      <div class="copyright">
        © 2025 MediTrack Pharmacy. All rights reserved.
      </div>
    </div>
  </footer>

  <!-- JS Logic -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    let cart = [];
    let currentCategoryId = null;
    let isInfoLocked = false;

    document.addEventListener("DOMContentLoaded", () => {
      loadCategories();

      // FIXED: Only auto-lock if ALL fields are filled
      const name = document.getElementById("userName").value.trim();
      const contact = document.getElementById("userContact").value.trim();
      const address = document.getElementById("userAddress").value.trim();

      if (name && contact && address) {
          confirmUserInfo();
      }

      document.getElementById("globalSearch").addEventListener("input", function (e) {
        filterMedicines(e.target.value);
      });
    });

    // --- Category Logic ---
    async function loadCategories() {
      try {
        const res = await fetch('categories.php');
        const categories = await res.json();

        const listEl = document.getElementById("categoryList");
        listEl.innerHTML = "";

        if (categories.length > 0) {
          categories.forEach((cat, index) => {
            const li = document.createElement("li");
            li.className = "category-item";
            li.textContent = cat.name;
            li.onclick = () => selectCategory(cat.id, li);
            listEl.appendChild(li);

            if (index === 0) selectCategory(cat.id, li);
          });
        } else {
          listEl.innerHTML = "<li class='category-item'>No Categories</li>";
        }

      } catch (err) {
        console.error("Error categories", err);
        // Fallback for demo purposes if PHP not running
        const listEl = document.getElementById("categoryList");
        listEl.innerHTML = `<li class="category-item active" onclick="selectCategory(1, this)">All Medicines</li>`;
        loadMedicines(1);
      }
    }

    function selectCategory(id, element) {
      document.querySelectorAll('.category-item').forEach(el => el.classList.remove('active'));
      element.classList.add('active');
      currentCategoryId = id;
      loadMedicines(id);
    }

    // --- Medicine Logic ---
    async function loadMedicines(catId) {
      const grid = document.getElementById("medicineGrid");
      const spinner = document.getElementById("loadingMeds");

      grid.innerHTML = "";
      spinner.style.display = "block";

      try {
        const res = await fetch('medicines.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ category_id: catId })
        });
        const medicines = await res.json();
        spinner.style.display = "none";
        renderMedicines(medicines);
      } catch (err) {
        console.error("Error medicines", err);
        spinner.style.display = "none";
        grid.innerHTML = "<p class='text-muted'>Unable to load medicines (Database connection needed).</p>";
      }
    }

    function renderMedicines(meds) {
      const grid = document.getElementById("medicineGrid");
      grid.innerHTML = "";

      if (!meds || meds.length === 0) {
        grid.innerHTML = "<p class='text-center text-muted col-12'>No medicines found.</p>";
        return;
      }

      meds.forEach(med => {
        const isOut = parseInt(med.quantity) === 0;
        const col = document.createElement('div');
        col.className = "col-md-4"; // 3 items per row

        col.innerHTML = `
                    <div class="medicine-card ${isOut ? 'out-of-stock' : ''}" 
                         onclick="${!isOut ? `addToCart(${med.medicine_id}, '${escapeHtml(med.name)}', ${med.unit_price})` : ''}">
                        <div class="price-tag">P${parseFloat(med.unit_price).toFixed(2)}</div>
                        <div class="med-img-wrapper">
                            <img src="assets/img/${med.img}" alt="${med.name}" onerror="this.src='https://via.placeholder.com/150/ffffff/000000?text=Medicine'">
                        </div>
                        <div class="med-info">
                            <h5 class="med-name">${med.name}</h5>
                            <p class="card-text small text-white-50 mt-1" style="line-height:1.2; height: 35px; overflow:hidden;">${med.description || ""}</p>
                            <small class="text-white">Stock: ${med.quantity}</small>
                        </div>
                    </div>
                `;
        grid.appendChild(col);
      });
    }

    function filterMedicines(query) {
      const cards = document.querySelectorAll('.medicine-card');
      query = query.toLowerCase();
      cards.forEach(card => {
        const name = card.querySelector('.med-name').textContent.toLowerCase();
        // Toggle visibility of the parent column
        card.parentElement.style.display = name.includes(query) ? "block" : "none";
      });
    }

    // --- Cart Logic ---
    function addToCart(id, name, price) {
      const existing = cart.find(item => item.medicine_id === id);
      if (existing) existing.quantity++;
      else cart.push({ medicine_id: id, name, price, quantity: 1 });
      renderCart();
    }

    function renderCart() {
      const receipt = document.getElementById("receipt");
      const totalEl = document.getElementById("totalValue");
      const checkoutBtn = document.getElementById("checkoutBtn");

      receipt.innerHTML = "";
      let total = 0;

      cart.forEach(item => {
        const subtotal = item.price * item.quantity;
        total += subtotal;
        receipt.innerHTML += `
                    <li class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>${item.name}</strong> <small>x${item.quantity}</small>
                        </div>
                        <div class="text-end">
                            <span class="d-block fw-bold">P${subtotal.toFixed(2)}</span>
                            <div class="btn-group btn-group-sm mt-1">
                                <button class="btn btn-outline-secondary py-0" style="font-size:0.7rem" onclick="updateQty(${item.medicine_id}, -1)">-</button>
                                <button class="btn btn-outline-secondary py-0" style="font-size:0.7rem" onclick="updateQty(${item.medicine_id}, 1)">+</button>
                            </div>
                        </div>
                    </li>`;
      });

      totalEl.textContent = `P${total.toFixed(2)}`;

      // Enable checkout only if cart has items AND info is locked (Done clicked)
      checkoutBtn.disabled = !(cart.length > 0 && isInfoLocked);
    }

    function updateQty(id, change) {
      const item = cart.find(i => i.medicine_id === id);
      if (!item) return;
      item.quantity += change;
      if (item.quantity <= 0) cart = cart.filter(i => i.medicine_id !== id);
      renderCart();
    }

    function clearCart() {
      cart = [];
      renderCart();
    }

    function confirmUserInfo() {
      const inputs = ['userName', 'userContact', 'userAddress'].map(id => document.getElementById(id));
      const btn = document.getElementById("infoToggleBtn");
      const paymentNotice = document.getElementById("paymentNotice");
      // const orderType = document.getElementById("orderType").value; 

      if (!isInfoLocked) {
        if (inputs.some(input => !input.value.trim())) {
          alert("Please fill all delivery fields.");
          return;
        }
        inputs.forEach(input => input.disabled = true);
        btn.textContent = "Edit";
        btn.className = "btn btn-outline-primary btn-sm w-100 mt-2";
        paymentNotice.textContent = "Mode of Payment: Paypal";
        isInfoLocked = true;
      } else {
        inputs.forEach(input => input.disabled = false);
        btn.textContent = "Done";
        btn.className = "btn btn-done btn-sm w-100 mt-2";
        isInfoLocked = false;
      }
      renderCart(); // Re-check button state
    }

    function checkout() {
      if (cart.length === 0) return;
      const checkoutData = {
        cart: cart,
        username: document.getElementById("userName").value,
        contact: document.getElementById("userContact").value,
        address: document.getElementById("userAddress").value,
        orderType: document.getElementById("orderType").value
      };

      fetch('checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(checkoutData)
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert("Order placed successfully!");
            clearCart();
            loadMedicines(currentCategoryId);
          } else {
            alert("Failed: " + data.message);
          }
        })
        .catch(e => {
          console.error(e);
          alert("Error connecting to server.");
        });
    }

    function escapeHtml(text) {
      if (!text) return "";
      return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }
  </script>
</body>

</html>