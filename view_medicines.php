<?php
include("connect.php");
include("paypal_config.php");
session_start();

// Initialize user info
$userID = $_SESSION['user_id'] ?? null;
$user = [
    'username' => '',
    'contact'  => '',
    'address'  => ''
];

// Fetch user data from DB if logged in
if ($userID && isset($pdo)) {
    $stmt = $pdo->prepare("SELECT username, contact, address FROM tbl_user WHERE userID = ?");
    $stmt->execute([$userID]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result !== false) {
        $user = $result;

        // Optional: store in session for other pages
        $_SESSION['username'] = $user['username'];
        $_SESSION['contact']  = $user['contact'];
        $_SESSION['address']  = $user['address'];
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
  <!-- PAYPAL (uses PAYPAL_CLIENT_ID from paypal-config.php) -->
  <script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_CLIENT_ID; ?>&currency=PHP"></script>
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

    :root {
      --primary-dark: #002147;
      --text-gray: #666;
    }

    * { margin:0; padding:0; box-sizing:border-box; }

    body {
      font-family: 'SF Pro Display', sans-serif;
      background-color: #ffffff;
      color: var(--primary-dark);
      overflow-x: hidden;
    }

    a { text-decoration: none; }

    .top-bar-wrapper { width: 100%; border-bottom: 1px solid #eee; background: white; }
    .custom-container { max-width: 1100px; margin: 0 auto; padding: 0 20px; }
    .top-bar-nav { padding: 20px 0; }

    .nav-link-custom { color: var(--primary-dark); font-size: 14px; font-weight: 500; margin-left: 30px; transition: 300ms; }
    .nav-link-custom:hover { color: #004080; }
    .user-circle { width:35px; height:35px; background-color: var(--primary-dark); border-radius:50%; margin-left:30px; }

    .page-title { font-size: 3rem; font-weight: 800; margin-top:40px; margin-bottom:30px; }

    .category-list { list-style:none; padding:0; position: sticky; top:20px; }
    .category-item { padding:10px 0; cursor:pointer; color:#999; font-weight:400; transition: all 0.3s ease; margin-bottom:5px; position: relative; }
    .category-item:hover, .category-item.active { color: var(--primary-dark); padding-left:15px; font-weight:600; }
    .category-item::before { content:''; position:absolute; left:0; top:50%; transform:translateY(-50%); width:4px; height:0; background-color: var(--primary-dark); transition: height 0.3s ease; border-radius:2px; }
    .category-item:hover::before, .category-item.active::before { height:70%; }

    .medicine-card { background-color: var(--primary-dark); color:white; border:1px solid var(--primary-dark); border-radius:20px; overflow:hidden; height:320px; position:relative; cursor:pointer; transition: transform 0.2s; margin-bottom:20px; display:flex; flex-direction:column; }
    .medicine-card:hover { transform: translateY(-5px); }
    .price-tag { position:absolute; top:0; right:0; background-color:#fff; color: var(--primary-dark); padding:5px 15px; font-weight:600; border-bottom-left-radius:15px; z-index:2; }
    .med-img-wrapper { height:65%; display:flex; align-items:center; justify-content:center; padding:20px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(4,30,66,0) 70%); }
    .med-img-wrapper img { max-height:100%; max-width:100%; object-fit:contain; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.3)); }
    .med-info { padding:20px; background: linear-gradient(to top, rgba(4,30,66,1) 20%, rgba(4,30,66,0) 100%); margin-top:auto; }
    .med-name { font-size:1.2rem; margin:0; font-weight:600; line-height:1.2; }
    .out-of-stock { opacity:0.6; pointer-events:none; filter:grayscale(100%); }

    .cart-container { border:1px solid #ddd; border-radius:20px; padding:25px; background:#fff; margin-left:10px; }
    .total-row { display:flex; justify-content:space-between; margin-top:20px; font-size:1.1rem; }
    .btn-checkout { background-color: var(--primary-dark); color:white; border:none; }
    .btn-checkout:disabled { background-color:#ccc; }
    .btn-done { background-color: var(--primary-dark); color:white; width:100%; margin-top:10px; }
    .payment-pill { background-color:#798da3; color:white; text-align:center; padding:10px; border-radius:5px; margin-top:10px; font-size:0.9rem; }

    footer { background-color: var(--primary-dark); color:white; padding:60px 0 20px; margin-top:80px; }
    .footer-content { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:40px; }
    .footer-contact p { font-size:14px; margin-bottom:5px; color:#fff; }
    .footer-contact a { color:white; }
    .footer-right { display:flex; align-items:center; gap:15px; max-width:300px; }
    .footer-logo { width:40px; height:40px; border:1px solid white; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .copyright { text-align:center; font-size:10px; opacity:0.6; border-top:1px solid rgba(255,255,255,0.1); padding-top:20px; }
    #receipt li { font-size:0.9rem; border-bottom:1px solid #eee; padding-bottom:5px; }
  </style>
</head>

<body>
  <div class="top-bar">MediTrack</div>

  <div class="top-bar-wrapper">
    <div class="custom-container">
      <header class="d-flex align-items-center justify-content-between py-2">
        <a href="#"><img src="assets/medilogo.png" height="20" class="me-2"></a>
        <div class="search-container">
          <span class="search-icon">🔍</span>
          <input type="text" placeholder="Search Medicine" id="medicineSearch">
        </div>
        <nav class="d-flex align-items-center">
          <a href="index.php" class="nav-link-custom me-3">Home</a>
          <a href="view_medicines.php" class="nav-link-custom me-3">Medicines</a>
          <div class="user-circle"></div>
        </nav>
      </header>
    </div>
  </div>

  <div class="custom-container my-4">
    <h1 class="page-title">Medicine</h1>
    <div class="row">
      <!-- Categories -->
      <div class="col-lg-2">
        <ul class="category-list" id="categoryList">
          <li class="category-item active">Loading...</li>
        </ul>
      </div>

      <!-- Medicines -->
      <div class="col-lg-7">
        <div id="medicineContainer" class="row gx-3 gy-4"></div>
        <div id="loadingMeds" class="text-center mt-5" style="display:none;">
          <div class="spinner-border text-primary" role="status"></div>
        </div>
      </div>

      <!-- Cart -->
      <div class="col-lg-3">
        <div class="cart-container sticky-top" style="top:20px;">
          <div class="d-flex align-items-center mb-3"><span class="h5 m-0">🛒 Cart</span></div>
          <div class="total-row mb-3">
            <span class="text-muted">TOTAL</span>
            <span class="fw-bold" id="totalValue">P0</span>
          </div>
          <div class="d-flex gap-2 mb-1">
            <button class="btn btn-outline-danger btn-sm flex-grow-1" onclick="clearCart()">Clear</button>
            <button class="btn btn-checkout btn-sm flex-grow-1" id="checkoutBtn" onclick="showPayPal()" disabled>Checkout</button>
          </div>
          <div style="max-height:150px; overflow-y:auto; margin-bottom:20px;">
            <ul id="receipt" class="list-unstyled mt-2"></ul>
          </div>
          <hr>
          <h6 class="mt-3" style="color:var(--primary-dark); font-weight:bold; font-size:0.9rem;">Delivery Information</h6>
          <input type="hidden" id="orderType" value="Delivery">
          <div class="mb-2">
            <label class="form-label" style="font-size:0.8rem">Name</label>
            <input type="text" class="form-control form-control-sm" id="userName" value="<?= htmlspecialchars($user['username']) ?>">
          </div>
          <div class="mb-2">
            <label class="form-label" style="font-size:0.8rem">Contact</label>
            <input type="text" class="form-control form-control-sm" id="userContact" value="<?= htmlspecialchars($user['contact']) ?>">
          </div>
          <div class="mb-3">
            <label class="form-label" style="font-size:0.8rem">Address</label>
            <input type="text" class="form-control form-control-sm" id="userAddress" value="<?= htmlspecialchars($user['address']) ?>">
          </div>
          <button class="btn btn-done btn-sm" id="infoToggleBtn" onclick="confirmUserInfo()">Done</button>
          <div class="payment-pill" id="paymentNotice">Mode of Payment: Paypal</div>
          <div id="paypal-button-container" class="mt-3 d-none"></div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <!-- Order Success Modal -->
  <div class="modal fade" id="orderSuccessModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content text-center p-3">
              <div class="modal-body">
                  <h4 class="text-success">✅ Order Placed Successfully!</h4>
                  <p>Thank you for your purchase.</p>
              </div>
          </div>
      </div>
  </div>

  <!-- Toasts -->
  <div class="position-fixed top-0 end-0 p-3" style="z-index:1080;">
      <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
              <div class="toast-body">✅ Order placed successfully!</div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>
      </div>
      <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
              <div class="toast-body">❌ Order saving failed.</div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>
      </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script src="https://www.paypal.com/sdk/js?client-id=YOUR_CLIENT_ID&currency=PHP"></script>
  <script>
    const userID = <?= json_encode($userID) ?>;
    let cart = [];
    let allMedicines = [];
    let isLocked = false;

    const receipt = document.getElementById("receipt");
    const totalValue = document.getElementById("totalValue");
    const checkoutBtn = document.getElementById("checkoutBtn");
    const userName = document.getElementById("userName");
    const userContact = document.getElementById("userContact");
    const userAddress = document.getElementById("userAddress");
    const infoToggleBtn = document.getElementById("infoToggleBtn");
    const paymentNotice = document.getElementById("paymentNotice");
    const medicineContainer = document.getElementById("medicineContainer");
    const medicineSearch = document.getElementById("medicineSearch");

    async function loadAllMedicines() {
        const res = await fetch('categories.php');
        const categories = await res.json();
        allMedicines = [];

        let html = `<div class="accordion">`;
        for (const cat of categories) {
            const medRes = await fetch('medicines.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ category_id: cat.id })
            });
            const meds = await medRes.json();
            if (meds.length) {
                html += `<div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#c${cat.id}">
                            ${cat.name}
                        </button>
                    </h2>
                    <div id="c${cat.id}" class="accordion-collapse collapse">
                        <div class="accordion-body"><div class="row">`;

                meds.forEach(m => {
                    allMedicines.push(m);
                    const out = m.quantity == 0;
                    html += `<div class="col-md-4 mb-3">
                        <div class="card h-100 ${out?'out-of-stock':''}" ${!out?`onclick="addToCart(${m.medicine_id},'${m.name}',${m.unit_price})"`:''}>
                            <img src="assets/img/${m.img}" class="card-img-top" style="height:150px;object-fit:contain">
                            <div class="card-body">
                                <h5>${m.name}</h5>
                                <p>₱${m.unit_price}</p>
                                <small>Stock: ${m.quantity}</small>
                            </div>
                        </div>
                    </div>`;
                });

                html += `</div></div></div></div>`;
            }
        }
        html += `</div>`;
        medicineContainer.innerHTML = html;
    }

    function addToCart(id, name, price) {
        const item = cart.find(i => i.medicine_id === id);
        if(item) item.quantity++;
        else cart.push({ medicine_id:id, name, price, quantity:1 });
        renderCart();
    }

    function renderCart() {
        receipt.innerHTML = '';
        let total = 0;
        cart.forEach(i => {
            total += i.price * i.quantity;
            receipt.innerHTML += `<li class="mb-2">${i.name} × ${i.quantity}<div class="float-end">₱${(i.price*i.quantity).toFixed(2)}</div></li>`;
        });
        totalValue.textContent = `₱${total.toFixed(2)}`;
        checkoutBtn.disabled = !(cart.length > 0 && isLocked);
    }

    function clearCart() {
        cart = [];
        renderCart();
        document.getElementById("paypal-button-container").classList.add("d-none");
    }

    function confirmUserInfo() {
        if(!userName.value || !userContact.value || !userAddress.value) {
            alert("Please fill all delivery info.");
            return;
        }
        isLocked = !isLocked;
        checkoutBtn.disabled = !isLocked;
        paymentNotice.classList.toggle("d-none");
        infoToggleBtn.textContent = isLocked ? "Edit" : "Done";
    }

    function showPayPal() {
        if(!isLocked || cart.length===0){ alert("Confirm delivery info & add items to cart."); return; }
        document.getElementById("paypal-button-container").classList.remove("d-none");
    }

    medicineSearch.addEventListener("input", () => {
        const q = medicineSearch.value.toLowerCase();
        const f = allMedicines.filter(m => m.name.toLowerCase().includes(q));
        medicineContainer.innerHTML = f.map(m => {
            const out = m.quantity == 0;
            return `<div class="col-md-4 mb-3">
                <div class="card ${out?'out-of-stock':''}" ${!out?`onclick="addToCart(${m.medicine_id},'${m.name}',${m.unit_price})"`:''}>
                    <img src="assets/img/${m.img}" class="card-img-top" style="height:150px;object-fit:contain">
                    <div class="card-body">
                        <h5>${m.name}</h5>
                        <p>₱${m.unit_price}</p>
                        <small>Stock: ${m.quantity}</small>
                    </div>
                </div>
            </div>`;
        }).join('');
    });

    function showToast(toastId, message=null){
        const toastEl = document.getElementById(toastId);
        if(message) toastEl.querySelector('.toast-body').textContent = message;
        new bootstrap.Toast(toastEl).show();
    }

    document.addEventListener("DOMContentLoaded", loadAllMedicines);
  </script>
</body>
</html>
