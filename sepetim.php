<?php
session_start();
include 'baglan.php';

// --- SEPETE DİREKT EKLEME ---
if (isset($_GET['islem']) && $_GET['islem'] == 'ekle' && isset($_GET['id'])) {
    $urun_id = intval($_GET['id']);
    
    if ($urun_id > 0) {
        // Eğer ürün sepette zaten varsa adetini artırıyoruz
        if (isset($_SESSION['sepet'][$urun_id])) {
            $_SESSION['sepet'][$urun_id]['adet'] += 1;
        } else {
            // Sepette yoksa veritabanındaki gerçek sütun isimleriyle (urun_adi, urun_gorsel, fiyat) bilgileri çekiyoruz
            $urun_cek = $db->prepare("SELECT urun_adi, urun_gorsel, fiyat FROM urunler WHERE id = ?");
            $urun_cek->execute([$urun_id]);
            $urun = $urun_cek->fetch(PDO::FETCH_ASSOC);
            
            if ($urun) {

                $_SESSION['sepet'][$urun_id] = [
                    'ad'     => $urun['urun_adi'],
                    'fiyat'  => $urun['fiyat'],
                    'gorsel' => $urun['urun_gorsel'],
                    'adet'   => 1
                ];
            }
        }
    }

    header("Location: sepetim.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sepetim - HepsiOrada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'nav.php'; ?>

    <main style="max-width: 1200px; margin: 30px auto; padding: 0 15px; display: flex; gap: 25px; align-items: flex-start;">
        
        <div style="flex: 3; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h2 style="font-size: 20px; color: #333; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Sepetim</h2>
            
            <?php
            $toplam_tutar = 0;
            if (!isset($_SESSION['sepet']) || count($_SESSION['sepet']) == 0) {
                echo "<div style='text-align:center; padding: 40px 0;'>";
                echo "<p style='font-size: 16px; color: #666; margin-bottom: 20px;'>Sepetinizde şu an ürün bulunmamaktadır.</p>";
                echo "<a href='index.php' style='background:#ff6000; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; font-weight:bold;'>Alışverişe Başla</a>";
                echo "</div>";
            } else {
                foreach ($_SESSION['sepet'] as $id => $urun) {
                    $ara_toplam = $urun['fiyat'] * $urun['adet'];
                    $toplam_tutar += $ara_toplam;

                    // Çoklu fotoğraf yapısından dolayı ilk görseli alıyoruz
                    $gorselParcala = explode(',', $urun['gorsel']);
                    $ilkGorsel = trim($gorselParcala[0]);
                    ?>
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #eee;">
                        <div style="display: flex; align-items: center; gap: 15px; flex: 2;">
                            <img src="Gorseller/<?php echo $ilkGorsel; ?>" style="width: 70px; height: 70px; object-fit: cover; border: 1px solid #eee; border-radius: 4px;">
                            <div>
                                <h4 style="font-size: 14px; color: #333; margin-bottom: 5px;"><?php echo $urun['ad']; ?></h4>
                                <p style="font-size: 12px; color: #27ae60; margin-top: 5px;">
                                    🚚 <strong>En geç yarın</strong> kargoda!
                                </p>
                                <a href="sepet-islem.php?islem=sil&id=<?php echo $id; ?>" style="color: #dc3545; font-size: 12px; text-decoration: none; font-weight: 500;">🗑️ Kaldır</a>
                            </div>
                        </div>
                        
                        <div style="flex: 1; display: flex; justify-content: center; align-items: center; gap: 8px;">
                            <a href="sepet-islem.php?islem=azalt&id=<?php echo $id; ?>" style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #f1f3f5; border: 1px solid #dee2e6; color: #333; text-decoration: none; font-weight: bold; border-radius: 4px; transition: all 0.2s;" onmouseover="this.style.background='#e2e6ea'" onmouseout="this.style.background='#f1f3f5'">-</a>
                            
                            <span style="font-size: 14px; font-weight: 600; color: #333; width: 30px; text-align: center; display: inline-block;">
                                <?php echo $urun['adet']; ?>
                            </span>
                            
                            <a href="sepet-islem.php?islem=arttir&id=<?php echo $id; ?>" style="display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #f1f3f5; border: 1px solid #dee2e6; color: #333; text-decoration: none; font-weight: bold; border-radius: 4px; transition: all 0.2s;" onmouseover="this.style.background='#e2e6ea'" onmouseout="this.style.background='#f1f3f5'">+</a>
                        </div>
                        
                        <div style="flex: 1; text-align: right; font-size: 16px; font-weight: 700; color: #ff6000;">
                            <?php echo number_format($ara_toplam, 0, ',', '.'); ?> TL
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>

        <?php if (isset($_SESSION['sepet']) && count($_SESSION['sepet']) > 0): ?>
        <div style="flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 90px;">
            <h3 style="font-size: 16px; color: #333; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Sipariş Özeti</h3>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; color: #666;">
                <span>Ürünün Toplamı:</span>
                <span><?php echo number_format($toplam_tutar, 0, ',', '.'); ?> TL</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 14px; align-items: center;">
                <span style="color: #666; font-weight: 500;">Kargo:</span>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="color: #999; text-decoration: line-through; font-size: 13px;">49,90 TL</span>
                    <span style="color: #27ae60; font-weight: bold;">Bedava 🚚</span>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; border-top: 1px solid #eee; padding-top: 15px; margin-bottom: 20px;">
                <span style="font-weight: bold; color: #333;">Ödenecek Tutar:</span>
                <span style="font-size: 20px; font-weight: bold; color: #ff6000;"><?php echo number_format($toplam_tutar, 0, ',', '.'); ?> TL</span>
            </div>
            
            <a href="odeme.php" style="display: block; background: #ff6000; color: white; text-align: center; padding: 12px 0; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 15px;">
                Alışverişi Tamamla
            </a>
            <div style="margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 8px; border: 1px dashed #ccc;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 12px; color: #555;">
                    <span>🛡️</span> <strong>Güvenli Alışveriş</strong>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 12px; color: #555;">
                    <span>🔄</span> <strong>14 Gün İade Garantisi</strong>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-size: 12px; color: #555;">
                    <span>💳</span> <strong>Kart Bilgileriniz Korunur</strong>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </main>

    <?php include 'footer.php'; ?>

</body>
</html>