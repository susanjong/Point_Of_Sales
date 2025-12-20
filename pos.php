<?php
require_once 'config.php';
requireLogin();

$db1 = getDB1Connection();

// Handle transaksi baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $cart = json_decode($_POST['cart'], true);
    $customer_id = $_POST['customer_id'] ?? 1;
    $payment_method = $_POST['payment_method'] ?? 'cash';
    $payment_amount = floatval($_POST['payment_amount']);
    
    if (!empty($cart)) {
        $total_amount = 0;
        foreach ($cart as $item) {
            $total_amount += $item['subtotal'];
        }
        
        $change_amount = $payment_amount - $total_amount;
        $transaction_code = generateTransactionCode();
        
        // Insert transaksi
        $stmt = $db1->prepare("INSERT INTO transactions (transaction_code, customer_id, total_amount, payment_method, payment_amount, change_amount, cashier_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sidsdds", $transaction_code, $customer_id, $total_amount, $payment_method, $payment_amount, $change_amount, $_SESSION['full_name']);
        
        if ($stmt->execute()) {
            $transaction_id = $db1->insert_id;
            
            // Insert detail transaksi dan update stock
            foreach ($cart as $item) {
                $stmt2 = $db1->prepare("INSERT INTO transaction_details (transaction_id, product_id, product_name, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt2->bind_param("iisidd", $transaction_id, $item['id'], $item['name'], $item['qty'], $item['price'], $item['subtotal']);
                $stmt2->execute();
                
                // Update stock
                $db1->query("UPDATE products SET stock = stock - {$item['qty']} WHERE id = {$item['id']}");
            }
            
            // Log ke DB2
            logActivity($_SESSION['user_id'], $_SESSION['username'], 'transaction', "Transaksi berhasil: $transaction_code - Total: " . formatRupiah($total_amount));
            
            $success = "Transaksi berhasil! Kode: $transaction_code";
        }
        
        $stmt->close();
    }
}

// Ambil daftar produk
$products = [];
$result = $db1->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.stock > 0 ORDER BY p.name");
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Ambil daftar customer
$customers = [];
$result = $db1->query("SELECT * FROM customers ORDER BY name");
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

$db1->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - POS System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Kasir / Point of Sales</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <div class="pos-container">
            <div class="pos-products">
                <h2>Daftar Produk</h2>
                <div class="search-box">
                    <input type="text" id="searchProduct" placeholder="Cari produk..." onkeyup="searchProducts()">
                </div>
                <div class="product-grid" id="productGrid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-item" data-name="<?= strtolower($product['name']) ?>">
                        <div class="product-info">
                            <strong><?= htmlspecialchars($product['name']) ?></strong>
                            <p><?= htmlspecialchars($product['category_name']) ?></p>
                            <p class="price"><?= formatRupiah($product['price']) ?></p>
                            <p class="stock">Stok: <?= $product['stock'] ?></p>
                        </div>
                        <button onclick="addToCart(<?= htmlspecialchars(json_encode($product)) ?>)" class="btn btn-sm btn-primary">
                            + Tambah
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="pos-cart">
                <h2>Keranjang Belanja</h2>
                <div id="cartItems"></div>
                <div class="cart-total">
                    <h3>Total: <span id="cartTotal">Rp 0</span></h3>
                </div>
                
                <form method="POST" action="" onsubmit="return processCheckout()">
                    <input type="hidden" name="cart" id="cartData">
                    
                    <div class="form-group">
                        <label>Customer</label>
                        <select name="customer_id" class="form-control">
                            <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['id'] ?>"><?= htmlspecialchars($customer['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Metode Pembayaran</label>
                        <select name="payment_method" class="form-control">
                            <option value="cash">Cash</option>
                            <option value="debit">Kartu Debit</option>
                            <option value="credit">Kartu Kredit</option>
                            <option value="ewallet">E-Wallet</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Jumlah Bayar</label>
                        <input type="number" name="payment_amount" id="paymentAmount" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Kembalian</label>
                        <input type="text" id="changeAmount" class="form-control" readonly>
                    </div>
                    
                    <button type="submit" name="checkout" class="btn btn-success btn-block">Proses Pembayaran</button>
                    <button type="button" onclick="clearCart()" class="btn btn-danger btn-block">Bersihkan Keranjang</button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="pos.js"></script>
</body>
</html>