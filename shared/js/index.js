const cart = [];
const badge = document.createElement("span"); // Temporary fix for undefined badge
badge.textContent = "0";

// Fetch and render categories
async function fetchCategories() {
    try {
        const response = await fetch('categories.php');
        const categories = await response.json();
        const dropdown = document.querySelector('#medicineDropdownMenu');
        dropdown.innerHTML = '';
        categories.forEach(cat => {
            const li = document.createElement('li');
            li.innerHTML = `<a class="dropdown-item" href="#" data-category-id="${cat.id}" data-category-name="${cat.name}">${cat.name}</a>`;
            dropdown.appendChild(li);
        });
        dropdown.innerHTML += `<li><hr class="dropdown-divider"></li>
                               <li><a class="dropdown-item" href="#" data-category-id="all" data-category-name="All">All</a></li>`;
        dropdown.querySelectorAll('a.dropdown-item').forEach(item => {
            item.addEventListener('click', e => {
                e.preventDefault();
                document.querySelectorAll('#medicineDropdownMenu a').forEach(a => a.classList.remove('active'));
                item.classList.add('active'); // Mark selected category
                loadMedicines(item.dataset.categoryId, item.dataset.categoryName);
            });

        });
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

async function loadMedicines(category_id = 'all', categoryname = 'All') {
    try {
        let medicines = [];
        if (category_id === 'all') {
            const response = await fetch('categories.php');
            const categories = await response.json();
            for (const cat of categories) {
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

        const area = document.getElementById('content-area');
        let html = `<div class="row">
      <h4 class="mb-3">${categoryname} Medicines</h4>
      <div class="col-12 mb-3">
        <input type="text" id="medicineSearch" class="form-control" placeholder="Search medicines...">
      </div>
      <div id="medicine-list" class="row">`;

        medicines.forEach(med => {
            const isOutOfStock = med.quantity === 0;
            html += `<div class="col-md-4 mb-3">
        <div class="medicine-card ${isOutOfStock ? 'out-of-stock' : ''}" ${!isOutOfStock ? `onclick="addToCart('${med.name}', ${med.unit_price})"` : ''}>
          <img src="assets/img/${med.img}" alt="${med.name}" class="img-fluid mb-2">
          <h5>${med.name}</h5>
          <p>₱${med.unit_price}</p>
          <p><small>In stock: ${med.quantity}</small></p>
        </div>
      </div>`;
        });

        html += `</div></div>`;
        area.innerHTML = html;
        // Show or hide cart based on whether medicines are loaded
        document.getElementById('cart-container').style.display = medicines.length > 0 ? 'block' : 'none';


        // Search filter
        document.getElementById("medicineSearch").addEventListener("input", function () {
            const query = this.value.toLowerCase();
            const filtered = medicines.filter(m => m.name.toLowerCase().includes(query));
            const list = document.getElementById("medicine-list");
            list.innerHTML = filtered.map(med => {
                const isOutOfStock = med.quantity === 0;
                return `<div class="col-md-4 mb-3">
          <div class="medicine-card ${isOutOfStock ? 'out-of-stock' : ''}" ${!isOutOfStock ? `onclick="addToCart('${med.name}', ${med.unit_price})"` : ''}>
            <img src="assets/img/${med.img}" alt="${med.name}" class="img-fluid mb-2">
            <h5>${med.name}</h5>
            <p>₱${med.unit_price}</p>
            <p><small>In stock: ${med.quantity}</small></p>
          </div>
        </div>`;
            }).join('');
        });

    } catch (error) {
        console.error('Error loading medicines:', error);
    }
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
    let total = 0, count = 0;
    cart.forEach(item => {
        const subtotal = item.price * item.quantity;
        total += subtotal;
        count += item.quantity;
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
    badge.textContent = count;
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

            // 👇 Reload medicines after checkout
            const activeCategory = document.querySelector('#medicineDropdownMenu a.active');
            if (activeCategory) {
                const id = activeCategory.dataset.categoryId;
                const name = activeCategory.dataset.categoryName;
                loadMedicines(id, name);
            } else {
                loadMedicines(); // fallback
            }

        } else {
            const text = await response.text();
            alert(text);
        }
    });
}


function loadLandingPage() {
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
});