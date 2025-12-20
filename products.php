<?php
require_once 'config.php';
requireLogin();

$db1 = getDB1Connection();

// Ambil daftar produk
$products = [];
$result = $db1->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.name");
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$db1->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - POS System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Manajemen Produk</h1>
        
        <div class="report-section">
            <h2>📦 Daftar Produk</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>SKU</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $product['id'] ?></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['sku']) ?></td>
                            <td><?= htmlspecialchars($product['category_name']) ?></td>
                            <td><?= formatRupiah($product['price']) ?></td>
                            <td><?= $product['stock'] ?></td>
                            <td><?= date('d/m/Y', strtotime($product['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>