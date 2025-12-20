<?php
require_once 'config.php';
requireLogin();

// Ambil data dari DB1
$db1 = getDB1Connection();

// Hitung total produk
$result = $db1->query("SELECT COUNT(*) as total FROM products");
$totalProducts = $result->fetch_assoc()['total'];

// Hitung transaksi hari ini
$result = $db1->query("SELECT COUNT(*) as total FROM transactions WHERE DATE(transaction_date) = CURDATE()");
$todayTransactions = $result->fetch_assoc()['total'];

// Revenue hari ini
$result = $db1->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM transactions WHERE DATE(transaction_date) = CURDATE()");
$todayRevenue = $result->fetch_assoc()['total'];

$db1->close();

// Ambil data dari DB2 (laporan)
$db2 = getDB2Connection();

// Total transaksi minggu ini
$result = $db2->query("SELECT COALESCE(SUM(total_transactions), 0) as total FROM daily_sales_summary WHERE report_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
$weeklyTransactions = $result->fetch_assoc()['total'];

$db2->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - POS System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Dashboard</h1>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-info">
                    <h3><?= $totalProducts ?></h3>
                    <p>Total Produk</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🛒</div>
                <div class="stat-info">
                    <h3><?= $todayTransactions ?></h3>
                    <p>Transaksi Hari Ini</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-info">
                    <h3><?= formatRupiah($todayRevenue) ?></h3>
                    <p>Pendapatan Hari Ini</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-info">
                    <h3><?= $weeklyTransactions ?></h3>
                    <p>Transaksi Minggu Ini</p>
                </div>
            </div>
        </div>
        
        <div class="dashboard-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="pos.php" class="btn btn-success btn-large">
                    🛍️ Mulai Transaksi Baru
                </a>
                <a href="products.php" class="btn btn-primary btn-large">
                    📦 Kelola Produk
                </a>
                <a href="transactions.php" class="btn btn-info btn-large">
                    📋 Riwayat Transaksi
                </a>
                <?php if (isAdmin()): ?>
                <a href="reports.php" class="btn btn-warning btn-large">
                    📊 Laporan Penjualan
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>