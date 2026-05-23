<?php
session_start();
include 'baglan.php';

// Ürün Ekleme
if (isset($_GET['islem']) && $_GET['islem'] == "ekle" && $_SERVER['REQUEST_METHOD'] == "POST") {
    $urun_id = intval($_POST['urun_id']);

    if (!isset($_SESSION['sepet'])) {
        $_SESSION['sepet'] = array();
    }

    if (isset($_SESSION['sepet'][$urun_id])) {
        $_SESSION['sepet'][$urun_id]['adet'] += 1;
    } else {
        $sorgu = $db->prepare("SELECT * FROM urunler WHERE id = ?");
        $sorgu->execute([$urun_id]);
        $urun = $sorgu->fetch(PDO::FETCH_ASSOC);

        if ($urun) {
            $_SESSION['sepet'][$urun_id] = array(
                'ad' => $urun['urun_adi'],
                'fiyat' => $urun['fiyat'],
                'gorsel' => $urun['urun_gorsel'],
                'adet' => 1
            );
        }
    }
    
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Ürün Silme
if (isset($_GET['islem']) && $_GET['islem'] == "sil" && isset($_GET['id'])) {
    $silinecek_id = intval($_GET['id']);
    if (isset($_SESSION['sepet'][$silinecek_id])) {
        unset($_SESSION['sepet'][$silinecek_id]);
    }
    header("Location: sepetim.php");
    exit();
}

// Adet Arttırma
if (isset($_GET['islem']) && $_GET['islem'] == "arttir" && isset($_GET['id'])) {
    $urun_id = intval($_GET['id']);
    if (isset($_SESSION['sepet'][$urun_id])) {
        $_SESSION['sepet'][$urun_id]['adet'] += 1;
    }
    header("Location: sepetim.php");
    exit();
}

// Adet Azaltma
if (isset($_GET['islem']) && $_GET['islem'] == "azalt" && isset($_GET['id'])) {
    $urun_id = intval($_GET['id']);
    if (isset($_SESSION['sepet'][$urun_id])) {
        $_SESSION['sepet'][$urun_id]['adet'] -= 1;
        
        // Eğer adet 0 veya altına düşerse ürünü sepetten tamamen kaldır
        if ($_SESSION['sepet'][$urun_id]['adet'] <= 0) {
            unset($_SESSION['sepet'][$urun_id]);
        }
    }
    header("Location: sepetim.php");
    exit();
}
?>