<?php
session_start();
include 'baglan.php';

if (isset($_POST['kayit_ol'])) {
    $ad_soyad = $_POST['ad_soyad'];
    $email = $_POST['email'];
    // Güvenlik amacıyla şifreyi hash'liyoruz
    $sifre = password_hash($_POST['sifre'], PASSWORD_DEFAULT);

    // E-posta adresi daha önce alınmış mı kontrolü (uyeler tablosundan)
    $kontrol = $db->prepare("SELECT * FROM uyeler WHERE email = ?");
    $kontrol->execute([$email]);
    
    if ($kontrol->rowCount() > 0) {
        $hata = "Bu e-posta adresi zaten bir üyemize ait!";
    } else {
        // Yeni üyeyi uyeler tablosuna ekliyoruz
        $sorgu = $db->prepare("INSERT INTO uyeler (ad_soyad, email, sifre) VALUES (?, ?, ?)");
        $ekle = $sorgu->execute([$ad_soyad, $email, $sifre]);
        if ($ekle) {
            header("Location: giris.php?durum=kayitli");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Hepsiorada - Üye Ol</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #f1f3f5;">
    <?php include 'nav.php'; ?>
    <div style="max-width: 400px; margin: 80px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center;">
        <div style="display: flex; justify-content: center; margin-bottom: 10px;">
            <a href="index.php" style="text-decoration: none; display: flex; align-items: center;" title="Anasayfaya Git">
                <img src="logo.png" alt="HepsiOrada Logo" style="height: 42px; max-width: 180px; object-fit: contain;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <span style="display: none; font-size: 26px; font-weight: 800; color: #ff6000; letter-spacing: -1px;">Hepsi<span style="color:#0f172a">Orada</span></span>
            </a>
        </div>
        
        <h2 style="font-size: 20px; color: #333; margin-bottom: 20px; margin-top: 0;">Üye Ol</h2>
        
        <?php if(isset($hata)) { echo "<p style='color:red; margin-bottom:15px; font-size:14px;'>$hata</p>"; } ?>

        <form action="" method="POST">
            <input type="text" name="ad_soyad" placeholder="Ad Soyad" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size:14px; box-sizing: border-box;">
            <input type="email" name="email" placeholder="E-posta Adresi" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size:14px; box-sizing: border-box;">
            <input type="password" name="sifre" placeholder="Şifre" required style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; font-size:14px; box-sizing: border-box;">
            <button type="submit" name="kayit_ol" style="width: 100%; padding: 12px; background: #ff6000; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 15px;">Üye Ol</button>
        </form>
        <p style="margin-top: 20px; font-size: 14px; color: #666;">Zaten üye misiniz? <a href="giris.php" style="color: #ff6000; text-decoration: none; font-weight: bold;">Giriş Yap</a></p>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>