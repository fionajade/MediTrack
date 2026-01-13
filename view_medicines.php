<?php $title = "Pill and Pestle | Medicine"; ?>

<?php
include("connect.php");
include("paypal_config.php"); // Make sure this has your PayPal credentials
session_start();

// Initialize user info
$userID = $_SESSION['userID'] ?? $_SESSION['user_id'] ?? null;
$userEmail = $_SESSION['email'] ?? null;
$user = [
  'username' => '',
  'contact' => '',
  'address' => ''
];

if ($userID) {
  $stmt = $pdo->prepare("SELECT username, contact, address, email FROM tbl_user WHERE userID = ?");
  $stmt->execute([$userID]);

  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($result !== false) {
    $user = $result;
    if (isset($result['email'])) {
      $userEmail = $result['email'];
    }
  }
}
?>

<?php include 'user_header.php'; ?>
  <script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_CLIENT_ID; ?>&currency=PHP"></script>


<body>
  <div class="top-bar">Pill and Pestle</div>

  <!-- Main Content (1100px) -->
  <div class="custom-container">
    <?php include 'client_navbar.php'; ?>
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

          <div id="paypal-button-container" class="d-none mt-3"></div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>
  <?php include 'chatbot.php'; ?>
  <!-- Toast Notifications -->
  <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">

    <!-- Success Toast -->
    <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body">
          ✅ Order placed successfully!
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>

    <!-- Error Toast -->
    <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body">
          ❌ Something went wrong. Please try again.
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>

  </div>



  <!-- JS Logic -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    let cart = [];
    let currentCategoryId = null;
    let isInfoLocked = false;
    let searchTimeout;

    /* DOM ELEMENTS */
    const receipt = document.getElementById("receipt");
    const totalValue = document.getElementById("totalValue");
    const checkoutBtn = document.getElementById("checkoutBtn");
    const userName = document.getElementById("userName");
    const userContact = document.getElementById("userContact");
    const userAddress = document.getElementById("userAddress");
    const userInfoAlert = document.getElementById("userInfoAlert");
    const infoToggleBtn = document.getElementById("infoToggleBtn");
    const paymentNotice = document.getElementById("paymentNotice");
    const medicineSearch = document.getElementById("medicineSearch");
    const medicineContainer = document.getElementById("medicineGrid");
    const userEmail = <?= json_encode($userEmail ?? null) ?>;

    /* UTILITY FUNCTION */
    function escapeHtml(text) {
      const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      };
      return text.replace(/[&<>"']/g, m => map[m]);
    }

    document.addEventListener("DOMContentLoaded", () => {
      const urlParams = new URLSearchParams(window.location.search);
      const urlSearchQuery = urlParams.get('search');
      const searchInput = document.getElementById("navbarSearch");

      loadCategories(urlSearchQuery ? true : false);

      if (urlSearchQuery && searchInput) {
        searchInput.value = urlSearchQuery;
        globalSearchMedicines(urlSearchQuery);
      } else {
        const name = document.getElementById("userName").value.trim();
        const contact = document.getElementById("userContact").value.trim();
        const address = document.getElementById("userAddress").value.trim();
        if (name && contact && address) {
          confirmUserInfo();
        }
      }

      if (searchInput) {
        searchInput.addEventListener("input", function(e) {
          const query = e.target.value.trim();
          clearTimeout(searchTimeout);
          searchTimeout = setTimeout(() => {
            if (query.length > 0) {
              globalSearchMedicines(query);
            } else {
              if (currentCategoryId) loadMedicines(currentCategoryId);
            }
          }, 300);
        });

        searchInput.addEventListener("keypress", function(e) {
          if (e.key === "Enter") {
            globalSearchMedicines(this.value.trim());
          }
        });
      }
    });

    // --- Category Logic ---
    async function loadCategories(isSearching) {
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

            if (index === 0 && !isSearching) {
              selectCategory(cat.id, li);
            }
          });
        } else {
          listEl.innerHTML = "<li class='category-item'>No Categories</li>";
        }
      } catch (err) {
        console.error("Error categories", err);
      }
    }

    function selectCategory(id, element) {
      document.getElementById("navbarSearch").value = "";

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
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            category_id: catId
          })
        });
        const medicines = await res.json();
        spinner.style.display = "none";
        renderMedicines(medicines);
      } catch (err) {
        spinner.style.display = "none";
        grid.innerHTML = "<p class='text-muted'>Error loading medicines.</p>";
      }
    }

    async function globalSearchMedicines(query) {
      const grid = document.getElementById("medicineGrid");
      const spinner = document.getElementById("loadingMeds");

      document.querySelectorAll('.category-item').forEach(el => el.classList.remove('active'));

      grid.innerHTML = "";
      spinner.style.display = "block";

      try {
        const res = await fetch('medicines.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            search: query
          })
        });
        const medicines = await res.json();
        spinner.style.display = "none";
        renderMedicines(medicines);
      } catch (err) {
        spinner.style.display = "none";
        grid.innerHTML = "<p class='text-muted text-center col-12'>Error performing search.</p>";
      }
    }

    function renderMedicines(meds) {
      const grid = document.getElementById("medicineGrid");
      grid.innerHTML = "";

      if (!meds || meds.length === 0) {
        grid.innerHTML = "<p class='text-center text-muted col-12 mt-5'>No medicines found matching that search.</p>";
        return;
      }

      meds.forEach(med => {
        const isOut = parseInt(med.quantity) === 0;
        const col = document.createElement('div');
        col.className = "col-md-4";
        col.innerHTML = `
        <div class="medicine-card ${isOut ? 'out-of-stock' : ''}"
             onclick="${!isOut ? `addToCart(${med.medicine_id}, '${escapeHtml(med.name)}', ${med.unit_price})` : ''}">
            <div class="price-tag">P${parseFloat(med.unit_price).toFixed(2)}</div>
            <div class="med-img-wrapper">
                <img src="assets/img/${med.img}" alt="${med.name}" onerror="this.src='https://via.placeholder.com/150/ffffff/000000?text=Medicine'">
            </div>
            <div class="med-info">
                <h5 class="med-name">${med.name}</h5>
                <p class="card-text small text-white-50 mt-1" style="line-height:1.2; overflow:hidden; margin-bottom: 0px;">${med.description || ""}</p>
                <small class="text-white">Stock: ${med.quantity}</small>
            </div>
        </div>
      `;
        grid.appendChild(col);
      });
    }

    // --- Cart & Other Logic ---
    function addToCart(id, name, price) {
      const existing = cart.find(item => item.medicine_id === id);
      if (existing) existing.quantity++;
      else cart.push({
        medicine_id: id,
        name,
        price,
        quantity: 1
      });
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
            <div><strong>${item.name}</strong> <small>x${item.quantity}</small></div>
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

      if (!isInfoLocked) {
        if (inputs.some(input => !input.value.trim())) {
          alert("Please fill all delivery fields.");
          return;
        }
        inputs.forEach(input => input.disabled = true);
        btn.textContent = "Edit";
        btn.className = "btn btn-outline-primary btn-sm w-100 mt-2";
        isInfoLocked = true;
      } else {
        inputs.forEach(input => input.disabled = false);
        btn.textContent = "Done";
        btn.className = "btn btn-done btn-sm w-100 mt-2";
        isInfoLocked = false;
      }
      renderCart();
    }
    /* CHECKOUT FUNCTION */
    function checkout() {
      showPayPal();
    }

    /* SHOW PAYPAL BUTTON */
    function showPayPal() {
      if (!isInfoLocked || cart.length === 0) {
        alert("Please confirm your delivery info and ensure cart is not empty.");
        return;
      }
      document.getElementById("paypal-button-container").classList.remove("d-none");
    }

    /* INIT PAYPAL BUTTON */
    paypal.Buttons({
      style: {
        layout: 'vertical',
        color: 'gold',
        shape: 'rect',
        tagline: false
      },

      createOrder: function(data, actions) {
        const totalPHP = cart.reduce((sum, i) => sum + i.price * i.quantity, 0);
        const totalAmount = totalPHP.toFixed(2);

        return fetch('paypal_create_order.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              amount: totalAmount
            })
          })
          .then(res => res.json())
          .then(data => {
            if (data.error) {
              console.error('Create order response error:', data);
              throw new Error(data.error || 'Failed to create order');
            }
            if (!data.id) {
              console.error('Create order unexpected response:', data);
              throw new Error('Failed to create order: no id returned');
            }
            return data.id;
          });
      },

      onApprove: function(data, actions) {
        // Capture the PayPal order first
        return fetch('paypal_capture_order.php?orderID=' + data.orderID, {
            method: 'POST'
          })
          .then(res => res.json())
          .then(details => {
            if (details.error) {
              showToast('errorToast', "Payment failed. Check console.");
              console.error(details.error);
              return;
            }

            const total = cart.reduce((s, i) => s + i.price * i.quantity, 0);


            // Save order to DB
            return fetch('save_order.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                  paymentID: data.orderID,
                  cart: cart,
                  total: total.toFixed(2),
                  name: userName.value,
                  contact: userContact.value,
                  address: userAddress.value,
                  email: userEmail
                })
              })
              .then(res => res.json())
              .then(r => {
                if (r.success) {
                  // Show success toast
                  showToast('successToast');

                  // Clear cart and reload after 2 seconds
                  setTimeout(() => {
                    clearCart();
                    location.reload();
                  }, 2000);
                } else {
                  console.error("Order save failed:", r.error);
                  showToast('errorToast', "Order saving failed.");
                }
              })
              .catch(err => {
                console.error("Error saving order:", err);
                showToast('errorToast', "An error occurred while saving your order.");
              });
          })
          .catch(err => {
            console.error("PayPal capture error:", err);
            showToast('errorToast', "Payment capture failed. Check console.");
          });
      },

      onError: function(err) {
        console.error("PayPal error:", err);
        alert("An error occurred with PayPal. Check console.");
      }

    }).render('#paypal-button-container');

    function showToast(id, message = null) {
      const toastEl = document.getElementById(id);

      if (!toastEl) {
        console.error(`Toast element #${id} not found`);
        return;
      }

      if (message) {
        const body = toastEl.querySelector('.toast-body');
        if (body) body.textContent = message;
      }

      const toast = new bootstrap.Toast(toastEl);
      toast.show();
    }



    // /* INIT */
    // document.addEventListener("DOMContentLoaded", loadAllMedicines);
  </script>
</body>

</html>