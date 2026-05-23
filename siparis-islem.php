<?php
session_start();
include 'baglan.php';

// Giriş yapılmadıysa veya sepet boşsa anasayfaya yolla
if (!isset($_SESSION['uye_id']) || !isset($_SESSION['sepet']) || empty($_SESSION['sepet'])) {
    header("Location: index.php");
    exit();
}

$islem = isset($_GET['islem']) ? $_GET['islem'] : '';

if ($islem == "tamamla" && $_SERVER['REQUEST_METHOD'] == "POST") {
    
    // 1. Formdan gelen verileri değişkenlere alıyoruz
    $uye_id       = $_SESSION['uye_id'];
    $adres_baslik = htmlspecialchars($_POST['adres_baslik']);
    $acik_adres   = htmlspecialchars($_POST['acik_adres']);
    $sehir        = htmlspecialchars($_POST['sehir']);
    $telefon      = htmlspecialchars($_POST['telefon']);
    
    // 2. Sepet verilerini işleme ve toplam tutarı çıkarma
    $urun_dizisi = array();
    $toplam_tutar = 0;
    
    foreach ($_SESSION['sepet'] as $urun) {
        $urun_dizisi[] = $urun['adet'] . "x " . $urun['ad'];
        $toplam_tutar += $urun['fiyat'] * $urun['adet'];
    }
    
    // Ürünler özeti artık sadece saf ürün isimlerini barındırıyor, adres metniyle kirlenmiyor
    $urunler_ozeti_metni = implode(', ', $urun_dizisi);

    try {

        $siparis_sorgu = $db->prepare("INSERT INTO siparisler SET 
            uye_id = ?, 
            urunler_ozeti = ?, 
            toplam_tutar = ?, 
            durum = ?,
            adres_baslik = ?,
            acik_adres = ?,
            sehir = ?,
            telefon = ?
        ");
        

        $siparis_sorgu->execute([
            $uye_id, 
            $urunler_ozeti_metni, 
            $toplam_tutar,
            'Hazırlanıyor',
            $adres_baslik,
            $acik_adres,
            $sehir,
            $telefon
        ]);
        
        // Sipariş numarasını kapıyoruz
        $siparis_id = $db->lastInsertId();

        // Başarıyla tamamlandı sepeti imha et
        unset($_SESSION['sepet']);

        header("Location: siparis-basarili.php?kod=" . $siparis_id);
        exit();

    } catch (Exception $e) {
        echo "Veritabanı hatası: " . $e->getMessage();
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>