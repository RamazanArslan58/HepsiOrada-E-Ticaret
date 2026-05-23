<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Giriş kontrolü
if (!isset($_SESSION['admin_giris']) || $_SESSION['admin_giris'] !== true) {
    header("Location: login.php");
    exit();
}
?>
<div class="admin-layout">
    <div class="admin-sidebar">
        <h2 style="color: #ff6000; text-align: center; padding: 20px 0;">HepsiOrada Admin</h2>
        <a href="admin.php">📊 Genel Bakış</a>
        <a href="soru-cevap.php">💬 Soru & Cevap Yönetimi</a>
        <a href="urun-ekle.php">📦 Ürün Ekle</a>
        <a href="toplu-yukle.php">📁 Toplu Aktarım</a>
        <a href="kategoriler.php">📂 Kategoriler</a>
        <a href="index.php">🏠 Siteye Dön</a>
    </div>
    <div class="admin-content"> <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { margin: 0; padding: 0; } 
    .admin-layout { display: flex; min-height: 100vh; background: #f4f4f4; font-family: 'Segoe UI', sans-serif; }
    .admin-layout { display: flex; min-height: 100vh; background: #f4f4f4; font-family: 'Segoe UI', sans-serif; }
    .admin-sidebar { width: 250px; background: #1e293b; color: #fff; height: 100vh; position: sticky; top: 0; flex-shrink: 0; }
    .admin-sidebar a { display: block; padding: 15px 25px; color: #cbd5e1; text-decoration: none; border-left: 4px solid transparent; transition: 0.3s; }
    .admin-sidebar a:hover { background: #334155; color: #fff; border-left-color: #ff6000; }
    .admin-content { flex: 1; padding: 30px; }
</style>