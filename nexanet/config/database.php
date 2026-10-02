<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'nexanet_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('BASE_URL', 'http://localhost/nexanet');
define('SITE_NAME', 'NexaNet');
define('SITE_DESC', 'Portal Digital Terpadu untuk Mengelola Layanan Internet Anda');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => true
        ]
    );
} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    die("Terjadi kesalahan koneksi database. Silakan coba lagi nanti.");
}
?>