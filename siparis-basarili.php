<?php 
session_start();
include 'baglan.php';

// Eğer URL'de bir sipariş kodu yoksa veya kullanıcı giriş yapmamışsa ana sayfaya şutla
if (!isset($_GET['kod']) || !isset($_SESSION['uye_id'])) {
    header("Location: index.php");
    exit();
}

$siparis_id = intval($_GET['kod']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişiniz Alındı - HepsiOrada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #f1f3f5;">

    <?php include 'nav.php'; ?>

    <main style="max-width: 650px; margin: 80px auto; padding: 0 15px;">
        <div style="background: #fff; padding: 40px 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); text-align: center;">
            
            <div style="width: 80px; height: 80px; background: #27ae60; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; box-shadow: 0 4px 10px rgba(39, 174, 96, 0.2);">
                <span style="color: white; font-size: 40px; font-weight: bold;">✓</span>
            </div>

            <h1 style="font-size: 24px; color: #333; margin-bottom: 10px; font-weight: 700;">Siparişiniz Başarıyla Alındı!</h1>
            <p style="font-size: 15px; color: #666; line-height: 1.6; margin-bottom: 30px;">
                Harika bir seçim yaptınız! Siparişiniz hazırlanmak üzere lojistik ekibimize iletildi. Güncel durumunu profilinizden anlık olarak takip edebilirsiniz.
            </p>

            <div style="background: #f8f9fa; border: 1px solid #e9ecef; padding: 15px 20px; border-radius: 8px; display: inline-block; margin-bottom: 35px;">
                <span style="font-size: 13px; color: #888; display: block; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px;">Sipariş Numarası</span>
                <strong style="font-size: 20px; color: #ff6000; letter-spacing: 0.5px;">#HO-<?php echo $siparis_id; ?></strong>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin-bottom: 30px;">

            <div style="display: flex; gap: 15px; justify-content: center;">
                <a href="index.php" style="flex: 1; max-width: 200px; background: #f1f3f5; color: #495057; padding: 12px 0; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; border: 1px solid #ced4da; transition: background 0.2s;" onmouseover="this.style.background='#e2e6ea'" onmouseout="this.style.background='#f1f3f5'">
                    🛒 Alışverişe Devam Et
                </a>
                
                <a href="siparislerim.php" style="flex: 1; max-width: 200px; background: #ff6000; color: white; padding: 12px 0; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; box-shadow: 0 4px 10px rgba(255, 96, 0, 0.2); transition: background 0.2s;" onmouseover="this.style.background='#ff7900'" onmouseout="this.style.background='#ff6000'">
                    📦 Siparişlerime Git
                </a>
            </div>

            <p style="font-size: 12px; color: #999; margin-top: 35px; line-height: 1.4;">
                Kargoya verildiğinde kayıtlı telefon numaranıza ve e-posta adresinize bilgilendirme mesajı gönderilecektir. Bizi tercih ettiğiniz için teşekkür ederiz!
            </p>
            
        </div>
    </main>

    <?php include 'footer.php'; ?>

</body>
</html>