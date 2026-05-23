<?php
session_start();
include 'baglan.php';

// Giriş yapılmamışsa girişe yolla
if (!isset($_SESSION['uye_id'])) {
    header("Location: giris.php");
    exit();
}

// Sepet boşsa ana sayfaya gönder
if (!isset($_SESSION['sepet']) || count($_SESSION['sepet']) == 0) {
    header("Location: index.php");
    exit();
}

$urun_ozet_dizi = [];
$genel_toplam = 0;

// Sepetteki ürünleri birleştirip özet metin oluşturuyoruz
foreach ($_SESSION['sepet'] as $urun) {
    $urun_ozet_dizi[] = $urun['adet'] . "x " . $urun['ad'];
    $genel_toplam += ($urun['fiyat'] * $urun['adet']);
}
$urunler_ozeti = implode(", ", $urun_ozet_dizi);

// Siparişi veritabanına yaz
$kaydet = $db->prepare("INSERT INTO siparisler (uye_id, urunler_ozeti, toplam_tutar) VALUES (?, ?, ?)");
$sonuc = $kaydet->execute([$_SESSION['uye_id'], $urunler_ozeti, $genel_toplam]);

if ($sonuc) {
    // Sipariş başarılı! Sepeti boşaltıyoruz
    unset($_SESSION['sepet']);
    header("Location: siparislerim.php?durum=basarili");
} else {
    echo "Bir hata oluştu!";
}
?>