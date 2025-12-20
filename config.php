<?php
// config.php - Konfigurasi koneksi ke 2 database

// Konfigurasi Database 1 - Main POS (Transaksi & Produk)
define('DB1_HOST', 'node71961-env-269043susan.user.cloudjkt01.com');
define('DB1_USER', 'root');
define('DB1_PASS', 'AKMktb51252');
define('DB1_NAME', 'pos_main');

// Konfigurasi Database 2 - Reports & Logs
define('DB2_HOST', 'node71960-env-074737777.user.cloudjkt01.com');
define('DB2_USER', 'root');
define('DB2_PASS', 'OVFtkc43857');
define('DB2_NAME', 'pos_reports');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi ke Database 1
function getDB1Connection() {
    try {
        $conn = new mysqli(DB1_HOST, DB1_USER, DB1_PASS, DB1_NAME);
        if ($conn->connect_error) {
            die("Koneksi DB1 gagal: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        die("Error DB1: " . $e->getMessage());
    }
}

// Koneksi ke Database 2
function getDB2Connection() {
    try {
        $conn = new mysqli(DB2_HOST, DB2_USER, DB2_PASS, DB2_NAME);
        if ($conn->connect_error) {
            die("Koneksi DB2 gagal: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        die("Error DB2: " . $e->getMessage());
    }
}

// Function untuk log activity ke DB2
function logActivity($user_id, $username, $activity_type, $description, $ip_address = null) {
    $db2 = getDB2Connection();
    
    if ($ip_address === null) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $stmt = $db2->prepare("INSERT INTO activity_logs (user_id, username, activity_type, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $username, $activity_type, $description, $ip_address, $user_agent);
    $stmt->execute();
    $stmt->close();
    $db2->close();
}

// Function untuk cek login
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

// Function untuk cek role admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Function untuk redirect jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Function untuk format rupiah
function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

// Function untuk generate transaction code
function generateTransactionCode() {
    return 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}
?>