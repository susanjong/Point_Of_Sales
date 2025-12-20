<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <a href="index.php">🛒 POS System</a>
        </div>
        
        <nav class="main-nav">
            <a href="index.php">Dashboard</a>
            <a href="pos.php">Kasir</a>
            <a href="products.php">Produk</a>
            <a href="transactions.php">Transaksi</a>
            <?php if (isAdmin()): ?>
            <a href="reports.php">Laporan</a>
            <?php endif; ?>
        </nav>
        
        <div class="user-menu">
            <span class="user-name">
                👤 <?= htmlspecialchars($_SESSION['full_name']) ?>
                <span class="role-badge"><?= htmlspecialchars($_SESSION['role']) ?></span>
            </span>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</header>