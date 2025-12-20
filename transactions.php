<?php
require_once 'config.php';
requireLogin();

$db1 = getDB1Connection();

// Ambil daftar transaksi
$transactions = [];
$result = $db1->query("SELECT t.*, c.name as customer_name FROM transactions t LEFT JOIN customers c ON t.customer_id = c.id ORDER BY t.transaction_date DESC LIMIT 50");
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

$db1->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - POS System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Riwayat Transaksi</h1>
        
        <div class="report-section">
            <h2>📋 50 Transaksi Terakhir</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                            <th>Kasir</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $trx): ?>
                        <tr>
                            <td><?= htmlspecialchars($trx['transaction_code']) ?></td>
                            <td><?= htmlspecialchars($trx['customer_name']) ?></td>
                            <td><?= formatRupiah($trx['total_amount']) ?></td>
                            <td><?= ucfirst($trx['payment_method']) ?></td>
                            <td><?= htmlspecialchars($trx['cashier_name']) ?></td>
                            <td><span class="badge"><?= $trx['status'] ?></span></td>
                            <td><?= date('d/m/Y H:i', strtotime($trx['transaction_date'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>