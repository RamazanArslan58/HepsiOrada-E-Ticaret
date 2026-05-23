<?php 
session_start();
// Güvenlik Kontrolü
if (!isset($_SESSION['admin_giris']) || $_SESSION['admin_giris'] !== true) {
    header("Location: login.php");
    exit();
}

include 'baglan.php'; 

// Düzenlenecek ürünün mevcut bilgilerini forma çekiyoruz
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sorgu = $db->prepare("SELECT * FROM urunler WHERE id = ?");
    $sorgu->execute([$id]);
    $urun = $sorgu->fetch(PDO::FETCH_ASSOC);
    
    if (!$urun) { header("Location: urun-ekle.php"); exit(); }
} else {
    header("Location: urun-ekle.php"); exit();
}

// Form gönderildiğinde veritabanını güncelliyoruz
if (isset($_POST['urun_guncelle'])) {
    $adi = $_POST['urun_adi'];
    $aciklama = $_POST['urun_aciklama'];
    $gorsel = $_POST['urun_gorsel'];
    $fiyat = $_POST['fiyat'];
    $eski_fiyat = !empty($_POST['eski_fiyat']) ? $_POST['eski_fiyat'] : 0; //

    $guncelleSorgu = $db->prepare("UPDATE urunler SET urun_adi = ?, urun_aciklama = ?, urun_gorsel = ?, fiyat = ?, eski_fiyat=? WHERE id = ?");
    $guncelle = $guncelleSorgu->execute([$adi, $aciklama, $gorsel, $fiyat, $eski_fiyat, $id]);

    if ($guncelle) {
        echo "<script>alert('Ürün başarıyla güncellendi!'); window.location.href='urun-ekle.php';</script>";
    } else {
        echo "<script>alert('Güncelleme sırasında bir hata oluştu.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Düzenle - Admin Paneli</title>
</head>
<body style="background: #f5f5f5; font-family: sans-serif;">
    <div style="max-width: 500px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2>Ürün Düzenleme Paneli</h2>
        <br>
        <form action="" method="POST">
            <label>Ürün Adı:</label><br>
            <input type="text" name="urun_adi" value="<?php echo $urun['urun_adi']; ?>" required style="width:100%; padding:8px; margin-bottom:15px; border-radius:4px;"><br>
            
            <label>Ürün Açıklaması:</label><br>
            <textarea name="urun_aciklama" required style="width:100%; padding:8px; margin-bottom:15px; height:100px; border-radius:4px;"><?php echo $urun['urun_aciklama']; ?></textarea><br>
            
            <label>Ürün Görseli Adı (Örn: mouse.jpg):</label><br>
            <input type="text" name="urun_gorsel" value="<?php echo $urun['urun_gorsel']; ?>" required style="width:100%; padding:8px; margin-bottom:15px; border-radius:4px;"><br>
            
            <label>Fiyat (TL):</label><br>
            <input type="number" name="fiyat" value="<?php echo $urun['fiyat']; ?>" required style="width:100%; padding:8px; margin-bottom:15px; border-radius:4px;"><br>
            
            <label>Eski Fiyat (İndirimli göstermek için):</label>
            <input type="number" name="eski_fiyat" value="<?php echo $urun['eski_fiyat']; ?>" required style="width:100%; padding:8px; margin-bottom:15px; border-radius:4px;">

            <button type="submit" name="urun_guncelle" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius:4px; cursor: pointer; width: 100%; font-weight:bold;">Değişiklikleri Kaydet</button>
        </form>
        <br>
        <a href="urun-ekle.php" style="display:block; text-align:center; color:#555; text-decoration:none;">← Geri Dön</a>
    </div>
</body>
</html>