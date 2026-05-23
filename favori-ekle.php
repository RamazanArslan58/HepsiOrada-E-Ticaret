<?php
session_start();
include 'baglan.php';

if (!isset($_SESSION['uye_id'])) {
    header("Location: giris.php");
    exit();
}

if (isset($_GET['id'])) {
    $urun_id = $_GET['id'];
    $uye_id = $_SESSION['uye_id'];

    // Bu ürün zaten favorilerde mi kontrol et
    $kontrol = $db->prepare("SELECT * FROM favoriler WHERE uye_id = ? AND urun_id = ?");
    $kontrol->execute([$uye_id, $urun_id]);

    if ($kontrol->rowCount() == 0) {
        // Yoksa ekle
        $ekle = $db->prepare("INSERT INTO favoriler (uye_id, urun_id) VALUES (?, ?)");
        $ekle->execute([$uye_id, $urun_id]);
    } else {
        // Varsa çıkar
        $sil = $db->prepare("DELETE FROM favoriler WHERE uye_id = ? AND urun_id = ?");
        $sil->execute([$uye_id, $urun_id]);
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}