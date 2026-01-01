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
    $stmt = $pdo->prepare("SELECT username, contact, address FROM tbl_user WHERE userID = ?");
    $stmt->execute([$userID]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Optional: update session variables (for other areas)
    $_SESSION['username'] = $user['username'];
    $_SESSION['contact'] = $user['contact'];
    $_SESSION['address'] = $user['address'];
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MediTrack | Medicines</title>
  <link rel="icon" href="assets/medi_logo.png">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="shared/css/style.css">
  <link rel="stylesheet" href="shared/css/medicine.css">
</head>
<body>

<?php include 'client_navbar.php'; ?>

<!-- MAIN CONTENT -->
<div class="container-fluid mt-4">
  <div class="row">
    <!-- Medicines List -->
    <div class="col-lg-9">
      <div class="mb-3">
        <input type="text" id="medicineSearch" class="form-control" placeholder="Search medicines...">
      </div>
      <div id="medicineContainer" class="row"></div>
    </div>

    <!-- Cart -->
    <div class="col-lg-3">
      <div id="cart-container">
        <div class="cart-sidebar sticky-top">
          <div class="card p-3 receipt">
            <h5 class="mb-3">🛒 Cart</h5>
            <ul id="receipt" class="list-unstyled"></ul>

            <div class="mt-4 d-flex justify-content-between">
              <div><b>TOTAL</b></div>
              <div><b id="totalValue">₱0</b></div>
            </div>
        <div class="mt-3 d-flex justify-content-between">
          <button class="btn btn-danger btn-sm" onclick="clearCart()">Clear</button>
          <button class="btn btn-success btn-sm" id="checkoutBtn" onclick="checkout()" disabled>Checkout</button>
        </div>


            <hr class="my-3">

            <!-- Delivery or Pickup Selection -->
            <div class="mb-3">
              <label for="orderType" class="form-label"><strong>Order Type</strong></label>
              <select id="orderType" class="form-select">
                <option value="pickup">Pickup</option>
                <option value="delivery">Delivery</option>
              </select>
            </div>

            <!-- User Contact and Address -->
            <div id="userInfoSection">
              <label class="form-label"><strong>Delivery Information</strong></label>
              <div class="mb-3">
                <label for="userName" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="userName" required
                       value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>">
              </div>
            <div class="mb-3">
              <label for="userContact" class="form-label">Contact</label>
              <input type="text" class="form-control" id="userContact" required
                    value="<?= htmlspecialchars($user['contact'] ?? '') ?>">
            </div>

            <div class="mb-3">
              <label for="userAddress" class="form-label">Address</label>
              <input type="text" class="form-control" id="userAddress" required
                    value="<?= htmlspecialchars($user['address'] ?? '') ?>">
            </div>
              <div id="userInfoAlert" class="alert alert-danger py-2 d-none" role="alert">
                Please fill out all delivery information.
              </div>
              <div class="d-grid">
                <button id="infoToggleBtn" class="btn btn-primary btn-sm" onclick="confirmUserInfo()">OK</button>
              </div>
            </div>

            <div class="alert alert-info mt-3" role="alert" id="paymentNotice" style="display: none;">
              Mode of Payment: <strong>Cash on Delivery or Pickup</strong>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<!-- JS LIBRARIES -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

<!-- MAIN SCRIPT -->
<script>
let cart = [];
let allMedicines = [];
let isLocked = false;

// Load Medicines
async function loadAllMedicines() {
  const container = document.getElementById("medicineContainer");
  const res = await fetch('categories.php');
  const categories = await res.json();
  allMedicines = [];

  let html = `<div class="accordion" id="medicineAccordion">`;
  for (const cat of categories) {
    const medRes = await fetch('medicines.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ category_id: cat.id })
    });
    const meds = await medRes.json();
    if (meds.length > 0) {
      const collapseId = `collapse${cat.id}`;
      html += `
        <div class="accordion-item">
          <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button"
                    data-bs-toggle="collapse" data-bs-target="#${collapseId}">
              ${cat.name}
            </button>
          </h2>
          <div id="${collapseId}" class="accordion-collapse collapse" data-bs-parent="#medicineAccordion">
            <div class="accordion-body">
              <div class="row">`;

      meds.forEach(med => {
        allMedicines.push(med);
        const isOut = med.quantity === 0;
        html += `<div class="col-md-4 mb-3 d-flex">
          <div class="medicine-card card w-100 h-100 ${isOut ? 'out-of-stock' : ''}"
               ${!isOut ? `onclick="addToCart(${med.medicine_id}, '${med.name}', ${med.unit_price})"` : ''}>
            <img src="assets/img/${med.img}" alt="${med.name}" class="card-img-top" style="height: 150px; object-fit: contain;"
                 onerror="this.src='assets/img/default.jpg'">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">${med.name}</h5>
              <p class="card-text small text-muted">${med.description || "No description available."}</p>
              <p class="card-text"><strong>₱${med.unit_price}</strong> per tablet</p>
              <p class="card-text mt-auto"><small class="text-muted">In stock: ${med.quantity}</small></p>
            </div>
          </div>
        </div>`;
      });

      html += `</div></div></div></div>`;
    }
  }
  html += `</div>`;
  container.innerHTML = html;
}

// Cart Functions
function addToCart(id, name, price) {
  const existing = cart.find(item => item.medicine_id === id);
  if (existing) {
    existing.quantity++;
  } else {
    cart.push({ medicine_id: id, name, price, quantity: 1 });
  }
  renderCart();
}

function renderCart() {
  const receipt = document.getElementById("receipt");
  const totalEl = document.getElementById("totalValue");
  receipt.innerHTML = "";
  let total = 0;

  cart.forEach(item => {
    const subtotal = item.price * item.quantity;
    total += subtotal;
    receipt.innerHTML += `
      <li class="mb-2">
        <div class="d-flex justify-content-between align-items-center">
          <div>${item.name} × ${item.quantity}</div>
          <div>₱${subtotal.toFixed(2)}</div>
        </div>
        <div class="cart-item-buttons d-flex justify-content-end">
          <button class="btn btn-sm btn-outline-secondary" onclick="increaseQuantity(${item.medicine_id})">+</button>
          <button class="btn btn-sm btn-outline-secondary" onclick="decreaseQuantity(${item.medicine_id})">−</button>
          <button class="btn btn-sm btn-outline-danger" onclick="removeItem(${item.medicine_id})">🗑</button>
        </div>
      </li>`;
  });

  totalEl.textContent = `₱${total.toFixed(2)}`;
}

function increaseQuantity(id) {
  const item = cart.find(i => i.medicine_id === id);
  if (item) item.quantity++;
  renderCart();
}

function decreaseQuantity(id) {
  const item = cart.find(i => i.medicine_id === id);
  if (item && item.quantity > 1) item.quantity--;
  else removeItem(id);
  renderCart();
}

function removeItem(id) {
  const index = cart.findIndex(i => i.medicine_id === id);
  if (index !== -1) cart.splice(index, 1);
  renderCart();
}

function clearCart() {
  cart = [];
  renderCart();
}

function checkout() {
  if (cart.length === 0) {
    alert("Your cart is empty!");
    return;
  }

  if (!isLocked) {
    alert("Please confirm your delivery information before proceeding to checkout.");
    return;
  }

  const username = document.getElementById("userName").value.trim();
  const contact = document.getElementById("userContact").value.trim();
  const address = document.getElementById("userAddress").value.trim();
  const orderType = document.getElementById("orderType").value;

  const checkoutData = {
    cart,
    username,
    contact,
    address,
    orderType
  };

  fetch('checkout.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(checkoutData)
  }).then(async response => {
    let resText = await response.text();
    console.log("Checkout raw response:", resText);

    try {
      const res = JSON.parse(resText);
      if (res.success) {
        alert(res.message);
        clearCart();
        loadAllMedicines();
      } else {
        alert(res.message || "Checkout failed.");
      }
    } catch (e) {
      console.error("Invalid JSON from checkout.php", e);
      alert("Server error: Invalid response format.");
    }
  }).catch(err => {
    console.error("Checkout fetch error:", err);
    alert("Something went wrong. Please try again.");
  });
}






// Delivery Info Edit/Lock
function confirmUserInfo() {
  const nameField = document.getElementById("userName");
  const contactField = document.getElementById("userContact");
  const addressField = document.getElementById("userAddress");
  const orderType = document.getElementById("orderType").value;
  const paymentNotice = document.getElementById("paymentNotice");
  const button = document.getElementById("infoToggleBtn");
  const alertBox = document.getElementById("userInfoAlert");

  if (!isLocked) {
    const name = nameField.value.trim();
    const contact = contactField.value.trim();
    const address = addressField.value.trim();

    if (!name || !contact || !address) {
      alertBox.classList.remove("d-none");
      return;
    } else {
      alertBox.classList.add("d-none");
    }

    nameField.readOnly = true;
    contactField.readOnly = true;
    addressField.readOnly = true;

    paymentNotice.textContent = orderType === "pickup"
      ? "Mode of Payment: Cash upon Pickup"
      : "Mode of Payment: Cash on Delivery";
    paymentNotice.style.display = "block";

    button.textContent = "Edit";
    button.classList.remove("btn-primary");
    button.classList.add("btn-outline-primary");
    isLocked = true;
  } else {
    nameField.readOnly = false;
    contactField.readOnly = false;
    addressField.readOnly = false;
    paymentNotice.style.display = "none";
    button.textContent = "OK";
    button.classList.remove("btn-outline-primary");
    button.classList.add("btn-primary");
    isLocked = false;
  }
}

document.getElementById("userName").value
document.getElementById("userContact").value
document.getElementById("userAddress").value


document.getElementById("orderType").addEventListener("change", function () {
  const paymentNotice = document.getElementById("paymentNotice");
  if (paymentNotice.style.display === "block") {
    paymentNotice.textContent = this.value === "pickup"
      ? "Mode of Payment: Cash upon Pickup"
      : "Mode of Payment: Cash on Delivery";
  }
});

document.getElementById("infoToggleBtn").addEventListener("click", () => {
  const checkoutBtn = document.getElementById("checkoutBtn");
  if (isLocked) {
    checkoutBtn.disabled = false;
  } else {
    checkoutBtn.disabled = true;
  }
});



// Search
document.getElementById("medicineSearch").addEventListener("input", function () {
  const query = this.value.toLowerCase();
  const filtered = allMedicines.filter(m => m.name.toLowerCase().includes(query));
  const container = document.getElementById("medicineContainer");
  let html = `<div class="row">`;
  filtered.forEach(med => {
    const isOut = med.quantity === 0;
    html += `<div class="col-md-4 mb-3 d-flex">
      <div class="medicine-card card w-100 h-100 ${isOut ? 'out-of-stock' : ''}"
           ${!isOut ? `onclick="addToCart(${med.medicine_id}, '${med.name}', ${med.unit_price})"` : ''}>
        <img src="assets/img/${med.img}" alt="${med.name}" class="card-img-top" style="height: 150px; object-fit: contain;">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">${med.name}</h5>
          <p class="card-text">₱${med.unit_price}</p>
          <p class="card-text mt-auto"><small class="text-muted">In stock: ${med.quantity}</small></p>
        </div>
      </div>
    </div>`;
  });
  html += `</div>`;
  container.innerHTML = html;
});

// Load on page
document.addEventListener("DOMContentLoaded", () => {
  loadAllMedicines();

  const name = document.getElementById("userName").value.trim();
  const contact = document.getElementById("userContact").value.trim();
  const address = document.getElementById("userAddress").value.trim();

  if (name && contact && address) {
    confirmUserInfo(); // auto-lock
  }
});

</script>
</body>
</html>
