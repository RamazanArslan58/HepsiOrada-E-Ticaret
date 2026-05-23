<?php 
session_start();
include 'baglan.php';

// Sepet boşsa veya tanımlı değilse ödeme sayfasına girmesin, sepete geri atsın
if (!isset($_SESSION['sepet']) || empty($_SESSION['sepet'])) {
    header("Location: sepetim.php");
    exit();
}

if (!isset($_SESSION['uye_id'])) {
    header("Location: giris.php?hata=önce-giriş-yapın");
    exit();
}

// Sağ taraftaki özet kutusu için sepet toplamını hesaplıyoruz
$toplam_fiyat = 0;
foreach ($_SESSION['sepet'] as $urun) {
    $toplam_fiyat += $urun['fiyat'] * $urun['adet'];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme ve Teslimat Bilgileri - HepsiOrada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'nav.php'; ?>

<main style="max-width: 1200px; margin: 40px auto; padding: 0 15px;">
    <form action="siparis-islem.php?islem=tamamla" method="POST" style="display: flex; gap: 30px; align-items: flex-start;">
        
        <div style="flex: 3; display: flex; flex-direction: column; gap: 20px;">
            
            <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <h3 style="margin-top:0; color:#333; border-bottom: 1px solid #eee; padding-bottom: 10px; font-size: 16px;">📍 1. Teslimat Adresi</h3>
                <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
                    <div>
                        <label style="font-size:13px; font-weight:600; color:#555; display:block; margin-bottom:5px;">Adres Başlığı</label>
                        <input type="text" name="adres_baslik" placeholder="Evim, İş Yerim..." required style="width:100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;">
                    </div>
                    
                    <div>
                        <label style="font-size:13px; font-weight:600; color:#555; display:block; margin-bottom:5px;">Açık Adres</label>
                        <textarea name="acik_adres" rows="3" placeholder="Mahalle, sokak, kapı numarası ve daire..." required style="width:100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; resize: none; box-sizing: border-box; font-family:inherit;"></textarea>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="font-size:13px; font-weight:600; color:#555; display:block; margin-bottom:5px;">Şehir</label>
                            <input type="text" name="sehir" placeholder="İstanbul" required style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%; box-sizing: border-box;">
                        </div>
                        <div>
                            <label style="font-size:13px; font-weight:600; color:#555; display:block; margin-bottom:5px;">Telefon Numarası</label>
                            <input type="tel" name="telefon" placeholder="05xx xxx xx xx" required style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width: 100%; box-sizing: border-box;">
                        </div>
                    </div>
                </div>
            </div>

            <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <h3 style="margin-top:0; color:#333; border-bottom: 1px solid #eee; padding-bottom: 10px; font-size: 16px;">💳 2. Ödeme Bilgileri</h3>
                
                <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
                    <div>
                        <label style="font-size:13px; font-weight:600; color:#555; display:block; margin-bottom:5px;">Kart Üzerindeki İsim</label>
                        <input type="text" placeholder="Ad Soyad" required style="width:100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;">
                    </div>

                    <div>
                        <label style="font-size:13px; font-weight:600; color:#555; display:block; margin-bottom:5px;">Kart Numarası</label>
                        <input type="text" maxlength="16" placeholder="0000 0000 0000 0000" required style="width:100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="font-size:13px; font-weight:600; color:#555; display:block; margin-bottom:5px;">Son Kullanma Tarihi</label>
                            <input type="text" placeholder="AA/YY" required style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width:100%; box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="font-size:13px; font-weight:600; color:#555; display:block; margin-bottom:5px;">CVC (Güvenlik Kodu)</label>
                            <input type="text" maxlength="3" placeholder="000" required style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; width:100%; box-sizing:border-box;">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div style="flex: 1; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 100px;">
            <h3 style="margin-top:0; color:#333; border-bottom: 1px solid #eee; padding-bottom: 10px; font-size: 16px;">Sipariş Özeti</h3>
            
            <div style="margin: 15px 0; font-size: 14px; color: #555; display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Ürünlerin Toplamı:</span>
                    <strong><?php echo number_format($toplam_fiyat, 0, ',', '.'); ?> TL</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Kargo Ücreti:</span>
                    <strong style="color: #27ae60;">Bedava 🚚</strong>
                </div>
                <hr style="border: 0; border-top: 1px solid #eee; margin: 5px 0;">
                <div style="display: flex; justify-content: space-between; font-size: 18px; color: #333;">
                    <span>Ödenecek Tutar:</span>
                    <strong style="color: #ff6000; font-weight: 800;"><?php echo number_format($toplam_fiyat, 0, ',', '.'); ?> TL</strong>
                </div>
            </div>

            <button type="submit" style="background: #ff6000; color: white; width: 100%; padding: 14px; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background 0.2s; margin-top: 10px;" onmouseover="this.style.background='#ff7900'" onmouseout="this.style.background='#ff6000'">
                🔒 Siparişi Onayla ve Bitir
            </button>
            <p style="font-size:11px; color:#999; text-align:center; margin-top:12px; line-height:1.4;">
                "Siparişi Onayla" butonuna basarak Mesafeli Satış Sözleşmesi şartlarını onaylamış olursunuz.
            </p>
        </div>

    </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>