<?php
include("connect.php");

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Meditrack</title>
    <link rel="icon" href="assets/medi_logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="shared/css/style.css">
    <link rel="stylesheet" href="shared/css/medicine.css">
</head>

<body>

<?php include 'client_navbar.php'; ?>

<!-- HEADER -->
<header>
  <h1>Welcome to MediTrack!</h1>
  <p class="lead">For Every Family. For Every Health Need</p>
</header>

<!-- MAIN CONTENT & CART SIDEBAR -->
<main class="container-fluid p-0">
  <!-- Video Banner Section -->
  <div id="video-banner" class="video-banner">
    <video autoplay muted loop playsinline class="w-100">
      <source src="assets/video_banner.mp4" type="video/mp4">
      Your browser does not support the video tag.
    </video>
  </div>
</main>

<?php include 'footer.php'; ?>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

<!-- MAIN JS SCRIPT
<script>
const cart = [];

async function fetchCategories() {
  const res = await fetch("medicine_dropdown.php");
  const data = await res.json();
  const dropdown = document.getElementById("medicineDropdownMenu");
  dropdown.innerHTML = "";

  data.forEach(cat => {
    const li = document.createElement("li");
    li.innerHTML = `<a class="dropdown-item" href="#" data-category-id="${cat.id}" data-category-name="${cat.name}">${cat.name}</a>`;
    dropdown.appendChild(li);
  });

  // Add "All" option
  dropdown.innerHTML += `<li><hr class="dropdown-divider"></li>
                         <li><a class="dropdown-item" href="#" data-category-id="all" data-category-name="All">All</a></li>`;

  // Event listeners
  dropdown.querySelectorAll('a.dropdown-item').forEach(item => {
    item.addEventListener('click', e => {
      e.preventDefault();
      document.querySelectorAll('#medicineDropdownMenu a').forEach(a => a.classList.remove('active'));
      item.classList.add('active');
      document.getElementById("video-banner").style.display = "none"; // 👈 hide video
      loadMedicines(item.dataset.categoryId, item.dataset.categoryName);
    });
  });
}


async function loadMedicines(category_id = 'all', categoryname = 'All') {
  const area = document.getElementById('content-area');
  area.innerHTML = `<p class="text-center">Loading...</p>`;
  let medicines = [];

  if (category_id === 'all') {
    const res = await fetch('categories.php');
    const cats = await res.json();
    for (const cat of cats) {
      const res = await fetch('medicines.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ category_id: cat.id })
      });
      const meds = await res.json();
      medicines = medicines.concat(meds);
    }
  } else {
    const res = await fetch('medicines.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ category_id })
    });
    medicines = await res.json();
  }

  let html = `<div class="row">
    <h4 class="mb-3">${categoryname} Medicines</h4>
    <div class="col-12 mb-3">
      <input type="text" id="medicineSearch" class="form-control" placeholder="Search medicines...">
    </div>
    <div id="medicine-list" class="row">`;

  medicines.forEach(med => {
    const isOut = med.quantity === 0;
    html += `<div class="col-md-4 mb-3">
      <div class="medicine-card ${isOut ? 'out-of-stock' : ''}" ${!isOut ? `onclick="addToCart('${med.name}', ${med.unit_price})"` : ''}>
        <img src="assets/img/${med.img}" alt="${med.name}" class="img-fluid mb-2">
        <h5>${med.name}</h5>
        <p>₱${med.unit_price}</p>
        <p><small>In stock: ${med.quantity}</small></p>
      </div>
    </div>`;
  });

  html += `</div></div>`;
  area.innerHTML = html;
  document.getElementById('cart-container').style.display = medicines.length ? 'block' : 'none';

  document.getElementById("medicineSearch").addEventListener("input", function () {
    const query = this.value.toLowerCase();
    const filtered = medicines.filter(m => m.name.toLowerCase().includes(query));
    const list = document.getElementById("medicine-list");
    list.innerHTML = filtered.map(med => {
      const isOut = med.quantity === 0;
      return `<div class="col-md-4 mb-3">
        <div class="medicine-card ${isOut ? 'out-of-stock' : ''}" ${!isOut ? `onclick="addToCart('${med.name}', ${med.unit_price})"` : ''}>
          <img src="assets/img/${med.img}" alt="${med.name}" class="img-fluid mb-2">
          <h5>${med.name}</h5>
          <p>₱${med.unit_price}</p>
          <p><small>In stock: ${med.quantity}</small></p>
        </div>
      </div>`;
    }).join('');
  });
}

function addToCart(name, price) {
  const existing = cart.find(item => item.name === name);
  if (existing) existing.quantity++;
  else cart.push({ name, price, quantity: 1 });
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
          <button class="btn btn-sm btn-outline-secondary" onclick="increaseQuantity('${item.name}')">+</button>
          <button class="btn btn-sm btn-outline-secondary" onclick="decreaseQuantity('${item.name}')">−</button>
          <button class="btn btn-sm btn-outline-danger" onclick="removeItem('${item.name}')">🗑</button>
        </div>
      </li>`;
  });
  totalEl.textContent = `₱${total.toFixed(2)}`;
}

function increaseQuantity(name) {
  const item = cart.find(i => i.name === name);
  if (item) item.quantity++;
  renderCart();
}

function decreaseQuantity(name) {
  const item = cart.find(i => i.name === name);
  if (item && item.quantity > 1) item.quantity--;
  else removeItem(name);
  renderCart();
}

function removeItem(name) {
  const i = cart.findIndex(i => i.name === name);
  if (i !== -1) cart.splice(i, 1);
  renderCart();
}

function clearCart() {
  cart.length = 0;
  renderCart();
}

function checkout() {
  fetch('checkout.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(cart)
  }).then(async response => {
    if (response.ok) {
      alert('Checkout successful!');
      clearCart();
      const activeCat = document.querySelector('#medicineDropdownMenu a.active');
      if (activeCat) loadMedicines(activeCat.dataset.categoryId, activeCat.dataset.categoryName);
      else loadMedicines();
    } else {
      const text = await response.text();
      alert(text);
    }
  });
}

function loadLandingPage() {
  document.getElementById("video-banner").style.display = "block"; // 👈 show video
  const area = document.getElementById("content-area");
  area.innerHTML = `
    <div class="text-center mt-5">
      <h2>Welcome to MediTrack!</h2>
      <p class="lead">Manage your medicines efficiently and effortlessly.</p>
      <img src="images/assets/pharmacy-illustration.png" alt="Welcome" class="img-fluid mt-3" style="max-width: 400px;">
    </div>
  `;
}


document.addEventListener("DOMContentLoaded", () => {
  fetchCategories();
  loadLandingPage();
}); -->
</script>
</body>
</html>