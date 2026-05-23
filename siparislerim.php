<?php
session_start();
include 'baglan.php';

if (!isset($_SESSION['uye_id'])) {
    header("Location: giris.php");
    exit();
}

// Kullanıcının siparişlerini çek
$siparis_sorgu = $db->prepare("SELECT * FROM siparisler WHERE uye_id = ? ORDER BY siparis_tarihi DESC");
$siparis_sorgu->execute([$_SESSION['uye_id']]);
$siparisler = $siparis_sorgu->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Siparişlerim - Hepsiorada</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profil-sidebar { width: 280px; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 15px 0; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); height: fit-content; }
        .profil-sidebar a { display: block; padding: 14px 25px; color: #475569; text-decoration: none; font-size: 14.5px; font-weight: 600; transition: all 0.2s; border-left: 4px solid transparent; }
        .profil-sidebar a:hover { background: #f8fafc; color: #ff6000; }
        .profil-sidebar a.aktif { background: #fff5ed; color: #ff6000; border-left-color: #ff6000; }

        .siparis-kart * {
            transform: none !important;
        }

        .siparis-kart .ok-donus-ikonu { 
            display: inline-block !important;
            transition: transform 0.3s ease-in-out !important;
            transform: rotate(0deg) !important;
        }

        .siparis-kart.aktif .ok-donus-ikonu {
            transform: rotate(180deg) !important;
        }

        .detay-paneli {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease-in-out, opacity 0.3s ease-in-out;
            opacity: 0;
            background: #fafafa;
        }
        
        .siparis-kart.aktif .detay-paneli {
            opacity: 1;
        }
    </style>
</head>
<body style="background: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif;">

    <?php include 'nav.php'; ?>

    <main style="max-width: 1200px; margin: 40px auto; padding: 0 15px; display: flex; gap: 30px;">
        
        <aside class="profil-sidebar">
            <div style="padding: 0 25px 15px 25px; border-bottom: 1px solid #f1f5f9; margin-bottom: 10px;">
                <h3 style="margin: 0; font-size: 18px; color: #1e293b;">Hesabım</h3>
            </div>
            <a href="profil.php">Profil Bilgilerim</a>
            <a href="siparislerim.php" class="aktif">Siparişlerim</a>
            <a href="profil.php?sayfa=begendiklerim">Beğendiklerim</a>
            <a href="profil.php?sayfa=sorularim">Sorularım</a>
            <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 10px 0;">
            <a href="cikis.php" style="color: #dc3545;">Güvenli Çıkış</a>
        </aside>

        <div style="flex: 1;">
            <h2 style="font-size: 22px; color: #333; margin-bottom: 25px; font-weight: 700;">Geçmiş Siparişlerim</h2>
              
            <?php if (count($siparisler) > 0): ?>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    
                    <?php foreach ($siparisler as $siparis): ?>
                    <div class="siparis-kart" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: all 0.3s;">
                        
                        <div class="kart-baslik" style="display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; background: #fff; cursor: pointer; user-select: none;">
                            <div style="display: flex; gap: 40px; font-size: 14px; color: #555;">
                                <div>
                                    <span style="color: #94a3b8; display: block; font-size: 12px; margin-bottom: 4px;">Sipariş Tarihi</span>
                                    <strong style="color: #334155;"><?php echo date('d M Y H:i', strtotime($siparis['siparis_tarihi'])); ?></strong>
                                </div>
                                <div>
                                    <span style="color: #94a3b8; display: block; font-size: 12px; margin-bottom: 4px;">Sipariş No</span>
                                    <strong style="color: #334155;">469 <?php echo $siparis['id']; ?> 165</strong>
                                </div>
                                <div>
                                    <span style="color: #94a3b8; display: block; font-size: 12px; margin-bottom: 4px;">Özet</span>
                                    <span style="color: #475569; font-weight: 500; max-width: 250px; display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo $siparis['urunler_ozeti']; ?>
                                    </span>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 25px;">
                                <div style="display: flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 600; color: #27ae60;">
                                    <span style="width: 8px; height: 8px; background: #27ae60; border-radius: 50%; display: inline-block;"></span>
                                    <?php echo $siparis['durum']; ?>
                                </div>
                                <div style="text-align: right;">
                                    <span style="color: #94a3b8; display: block; font-size: 11px;">Toplam Tutar</span>
                                    <strong style="font-size: 16px; color: #ff6000;"><?php echo number_format($siparis['toplam_tutar'], 0, ',', '.'); ?> TL</strong>
                                </div>
                                <span class="ok-donus-ikonu" style="font-size: 14px; color: #64748b; font-weight: bold;">▼</span>
                            </div>
                        </div>

                        <div class="detay-paneli">
                            <div style="padding: 24px; border-top: 1px solid #f1f5f9; cursor: default;" onclick="event.stopPropagation();">
                                
                                <div style="background: #fff8e6; border: 1px solid #ffe8cc; color: #664d03; padding: 12px 16px; border-radius: 6px; font-size: 13px; margin-bottom: 24px; display: flex; align-items: center; gap: 8px;">
                                    ⚠️ <strong>Güvenli alışveriş için:</strong> Ödemelerinizi yalnızca HepsiOrada üzerinden gerçekleştirin. Satıcılara doğrudan IBAN ile ödeme yapmayın.
                                </div>

                                <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 24px;">
                                    <?php 
                                    $urun_listesi = explode(',', $siparis['urunler_ozeti']);
                                    foreach ($urun_listesi as $urun_item):
                                        $urun_item = trim($urun_item);
                                        if(empty($urun_item)) continue;

                                        $temiz_urun_adi = preg_replace('/^[0-9]+x\s+/', '', $urun_item); 
                                        
                                        $urun_bul = $db->prepare("SELECT id, urun_gorsel, fiyat FROM urunler WHERE urun_adi LIKE ? LIMIT 1");
                                        $urun_bul->execute(['%' . $temiz_urun_adi . '%']);
                                        $urun_veri = $urun_bul->fetch(PDO::FETCH_ASSOC);

                                        $urun_id = $urun_veri ? $urun_veri['id'] : 0;
                                        $gorsel_adi = $urun_veri ? $urun_veri['urun_gorsel'] : '';
                                        $urun_fiyat = $urun_veri ? $urun_veri['fiyat'] : 0;
                                        $urun_resim_yolu = (!empty($gorsel_adi)) ? "Gorseller/" . $gorsel_adi : "";
                                    ?>
                                    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; display: flex; align-items: center; justify-content: space-between;">
                                        <div style="display: flex; align-items: center; gap: 20px;">
                                            <div style="width: 70px; height: 70px; border: 1px solid #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #fff;">
                                                <?php if(!empty($gorsel_adi) && file_exists($urun_resim_yolu)): ?>
                                                    <img src="<?php echo htmlspecialchars($urun_resim_yolu); ?>" alt="Ürün Görseli" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                <?php else: ?>
                                                    <div style="font-size: 24px; color: #cbd5e1; user-select: none;">📦</div>
                                                <?php endif; ?>
                                            </div>

                                            <div>
                                                <h4 style="margin: 0 0 6px 0; font-size: 14px; color: #1e293b; font-weight: 600; line-height: 1.4;">
                                                    <?php echo htmlspecialchars($urun_item); ?>
                                                </h4>
                                                <span style="font-size: 13px; color: #27ae60; font-weight: bold;">
                                                    <?php echo number_format($urun_fiyat, 0, ',', '.'); ?> TL
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div style="display: flex; gap: 10px;">
                                            <a href="sepetim.php?islem=ekle&id=<?php echo $urun_id; ?>" style="text-decoration: none; background: #fff; border: 1px solid #cbd5e1; color: #475569; padding: 8px 14px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-block; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">Tekrar Al</a>
                                            <a href="urun-detay.php?id=<?php echo $urun_id; ?>" style="text-decoration: none; background: #fff; border: 1px solid #cbd5e1; color: #475569; padding: 8px 14px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-block; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">Ürünü Değerlendir</a>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 25px; align-items: flex-start;">
                                    <div style="background: #fff; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px;">
                                        <h3 style="margin: 0 0 15px 0; font-size: 15px; color: #1e293b; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">📌 Teslimat Bilgileri</h3>
                                        <div style="font-size: 13px; color: #475569; line-height: 1.6;">
                                            <p style="margin: 5px 0;"><strong style="color:#0f172a;">Adres Başlığı:</strong> <?php echo !empty($siparis['adres_baslik']) ? htmlspecialchars($siparis['adres_baslik']) : 'Ev'; ?></p>
                                            <p style="margin: 5px 0;"><strong style="color:#0f172a;">Açık Adres:</strong> <?php echo !empty($siparis['acik_adres']) ? htmlspecialchars($siparis['acik_adres']) : 'Adres bilgisi bulunamadı.'; ?></p>
                                            <p style="margin: 5px 0;"><strong style="color:#0f172a;">Şehir / Bölge:</strong> <?php echo !empty($siparis['sehir']) ? htmlspecialchars($siparis['sehir']) : '-'; ?></p>
                                            <p style="margin: 5px 0;"><strong style="color:#0f172a;">Alıcı Telefon:</strong> <?php echo !empty($siparis['telefon']) ? htmlspecialchars($siparis['telefon']) : '-'; ?></p>
                                        </div>
                                    </div>

                                    <div style="background: #fff; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px;">
                                        <h3 style="margin: 0 0 15px 0; font-size: 15px; color: #1e293b; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">💳 Ödeme Detayı</h3>
                                        <div style="font-size: 13px; color: #475569; display: flex; flex-direction: column; gap: 10px;">
                                            <div style="display: flex; justify-content: space-between;">
                                                <span>Ürünler Toplamı:</span>
                                                <strong><?php echo number_format($siparis['toplam_tutar'], 0, ',', '.'); ?> TL</strong>
                                            </div>
                                            <div style="display: flex; justify-content: space-between;">
                                                <span>Kargo:</span>
                                                <span style="color: #27ae60; font-weight: 600;">Bedava 🚚</span>
                                            </div>
                                            <div style="display: flex; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 10px; font-size: 14px; color: #0f172a;">
                                                <span><strong>Genel Toplam:</strong></span>
                                                <strong style="color: #ff6000; font-size: 16px;"><?php echo number_format($siparis['toplam_tutar'], 0, ',', '.'); ?> TL</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <?php endforeach; ?>

                </div>
            <?php else: ?>
                <div style="background: #fff; padding: 50px; text-align: center; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <p style="color: #64748b; font-size: 15px;">Henüz geçmiş bir siparişiniz bulunmuyor.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        document.querySelectorAll('.kart-baslik').forEach(baslik => {
            baslik.addEventListener('click', function() {
                const kart = this.parentElement;
                const panel = this.nextElementSibling;
                
                if (kart.classList.contains('aktif')) {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                    setTimeout(() => {
                        panel.style.maxHeight = "0";
                    }, 10);
                    kart.classList.remove('aktif');
                } else {
                    kart.classList.add('aktif');
                    panel.style.maxHeight = panel.scrollHeight + "px";
                    
                    setTimeout(() => {
                        if(kart.classList.contains('aktif')) {
                            panel.style.maxHeight = "none";
                        }
                    }, 350);
                }
            });
        });
    </script>
</body>
</html>