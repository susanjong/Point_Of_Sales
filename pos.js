// pos.js - JavaScript untuk halaman kasir

let cart = [];

function addToCart(product) {
    const existingItem = cart.find(item => item.id === product.id);
    
    if (existingItem) {
        existingItem.qty++;
        existingItem.subtotal = existingItem.qty * existingItem.price;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: parseFloat(product.price),
            qty: 1,
            subtotal: parseFloat(product.price)
        });
    }
    
    updateCart();
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    updateCart();
}

function updateQuantity(productId, change) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.qty += change;
        if (item.qty <= 0) {
            removeFromCart(productId);
        } else {
            item.subtotal = item.qty * item.price;
            updateCart();
        }
    }
}

function updateCart() {
    const cartItemsDiv = document.getElementById('cartItems');
    const cartTotalSpan = document.getElementById('cartTotal');
    
    if (cart.length === 0) {
        cartItemsDiv.innerHTML = '<p class="empty-cart">Keranjang kosong</p>';
        cartTotalSpan.textContent = 'Rp 0';
        return;
    }
    
    let html = '<div class="cart-items-list">';
    let total = 0;
    
    cart.forEach(item => {
        total += item.subtotal;
        html += `
            <div class="cart-item">
                <div class="cart-item-info">
                    <strong>${item.name}</strong>
                    <p>${formatRupiah(item.price)}</p>
                </div>
                <div class="cart-item-controls">
                    <button onclick="updateQuantity(${item.id}, -1)" class="btn-qty">-</button>
                    <span class="qty">${item.qty}</span>
                    <button onclick="updateQuantity(${item.id}, 1)" class="btn-qty">+</button>
                    <button onclick="removeFromCart(${item.id})" class="btn-remove">🗑️</button>
                </div>
                <div class="cart-item-subtotal">
                    ${formatRupiah(item.subtotal)}
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    cartItemsDiv.innerHTML = html;
    cartTotalSpan.textContent = formatRupiah(total);
    
    // Update payment calculation
    calculateChange();
}

function formatRupiah(number) {
    return 'Rp ' + number.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function clearCart() {
    if (confirm('Yakin ingin mengosongkan keranjang?')) {
        cart = [];
        updateCart();
    }
}

function calculateChange() {
    const total = cart.reduce((sum, item) => sum + item.subtotal, 0);
    const payment = parseFloat(document.getElementById('paymentAmount').value) || 0;
    const change = payment - total;
    
    document.getElementById('changeAmount').value = change >= 0 ? formatRupiah(change) : 'Kurang bayar!';
}

function processCheckout() {
    if (cart.length === 0) {
        alert('Keranjang masih kosong!');
        return false;
    }
    
    const total = cart.reduce((sum, item) => sum + item.subtotal, 0);
    const payment = parseFloat(document.getElementById('paymentAmount').value);
    
    if (payment < total) {
        alert('Jumlah pembayaran kurang!');
        return false;
    }
    
    document.getElementById('cartData').value = JSON.stringify(cart);
    return true;
}

function searchProducts() {
    const searchTerm = document.getElementById('searchProduct').value.toLowerCase();
    const products = document.querySelectorAll('.product-item');
    
    products.forEach(product => {
        const productName = product.getAttribute('data-name');
        if (productName.includes(searchTerm)) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

// Event listener untuk payment amount
document.addEventListener('DOMContentLoaded', function() {
    const paymentInput = document.getElementById('paymentAmount');
    if (paymentInput) {
        paymentInput.addEventListener('input', calculateChange);
    }
});