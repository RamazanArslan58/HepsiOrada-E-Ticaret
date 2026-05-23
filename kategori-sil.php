<?php
session_start();
include 'baglan.php';

if (isset($_GET['id'])) {
    $silinecek_id = $_GET['id'];

    $sorgu = $db->prepare("DELETE FROM kategoriler WHERE id = ?");
    $durum = $sorgu->execute([$silinecek_id]);

    if ($durum) {
        // Silme başarılıysa listeye geri dön
        header("Location: kategoriler.php?durum=silindi");
        exit();
    } else {
        // Bir hata oluştuysa hata mesajıyla dön
        header("Location: kategoriler.php?durum=hata");
        exit();
    }
} else {
    // ID gelmediyse direkt geri gönder
    header("Location: kategoriler.php");
    exit();
}
?>