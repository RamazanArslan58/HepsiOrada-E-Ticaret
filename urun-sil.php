<?php
session_start();

// Güvenlik kontrolü
if (!isset($_SESSION['admin_giris']) || $_SESSION['admin_giris'] !== true) {
    header("Location: login.php");
    exit();
}

include 'baglan.php';

// Linkten gelen bir id parametresi var mı bakıyoruz
if (isset($_GET['id'])) {
    $silinecek_id = $_GET['id'];

    // SQL delete sorgusu hazırlıyoruz
    $sorgu = $db->prepare("DELETE FROM urunler WHERE id = ?");
    $sil = $sorgu->execute([$silinecek_id]);

    if ($sil) {
        echo "<script>alert('Ürün başarıyla silindi!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Silme işlemi sırasında bir hata oluştu.'); window.location.href='admin.php';</script>";
    }
} else {
    // ID gönderilmediyse doğrudan panele geri postala
    header("Location: admin.php");
}
?>