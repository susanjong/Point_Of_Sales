<?php
require_once 'config.php';
requireLogin();

if (!isAdmin()) {
    header("Location: index.php");
    exit();
}

$db2 = getDB2Connection();

// Ambil laporan penjualan harian
$dailySales = [];
$result = $db2->query("SELECT * FROM daily_sales_summary ORDER BY report_date DESC LIMIT 7");
while ($row = $result->fetch_assoc()) {
    $dailySales[] = $row;
}

// Ambil top products
$topProducts = [];
$result = $db2->query("SELECT * FROM monthly_top_products LIMIT 10");
while ($row = $result->fetch_assoc()) {
    $topProducts[] = $row;
}

// Ambil activity logs terakhir
$activityLogs = [];
$result = $db2->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 20");
while ($row = $result->fetch_assoc()) {
    $activityLogs[] = $row;
}

$db2->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - POS System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Laporan Penjualan</h1>
        
        <div class="report-section">
            <h2>📊 Penjualan 7 Hari Terakhir (dari DB Reports)</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Total Transaksi</th>
                            <th>Total Pendapatan</th>
                            <th>Item Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dailySales)): ?>
                            <?php foreach ($dailySales as $sale): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($sale['report_date'])) ?></td>
                                <td><?= $sale['total_transactions'] ?></td>
                                <td><?= formatRupiah($sale['total_revenue']) ?></td>
                                <td><?= $sale['total_items_sold'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4">Belum ada data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="report-section">
            <h2>🏆 Top 10 Produk Terlaris Bulan Ini</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah Terjual</th>
                            <th>Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($topProducts)): ?>
                            <?php foreach ($topProducts as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['product_name']) ?></td>
                                <td><?= $product['total_sold'] ?></td>
                                <td><?= formatRupiah($product['revenue']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3">Belum ada data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="report-section">
            <h2>📝 Activity Logs (20 Terakhir)</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aktivitas</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($activityLogs)): ?>
                            <?php foreach ($activityLogs as $log): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                                <td><?= htmlspecialchars($log['username']) ?></td>
                                <td><span class="badge"><?= $log['activity_type'] ?></span></td>
                                <td><?= htmlspecialchars($log['description']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4">Belum ada log aktivitas</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>