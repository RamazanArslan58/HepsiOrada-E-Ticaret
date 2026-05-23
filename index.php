<?php
session_start();
include 'baglan.php'; 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hepsiorada - Anasayfa</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .slider-wrapper {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
        }
        .slider-container {
            display: flex;
            gap: 20px;
            overflow-x: hidden;
            scroll-behavior: smooth;
            padding: 15px 5px;
            width: 100%;
        }
        .slider-container .card {
            flex: 0 0 calc(25% - 15px); /* Ekranda yan yana 4 kart gösterir */
            min-width: 250px;
        }
        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: #ffffff;
            border: 1px solid #e6e6e6;
            color: #ff6000;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            z-index: 100;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .slider-btn:hover {
            background: #ff6000;
            color: #fff;
            border-color: #ff6000;
        }
        .slider-btn.prev { left: -20px; }
        .slider-btn.next { right: -20px; }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

<main>

    <div style="background: #f1f3f5; padding: 20px 0; font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif;">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; gap: 15px; padding: 0 15px;">
        
        <aside style="flex: 0 0 250px; background: #ffffff; border-radius: 4px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); display: flex; flex-direction: column; height: 380px; overflow: hidden; border: 1px solid #e2e8f0;">
            <div style="padding: 15px 18px; font-weight: 700; font-size: 14px; color: #1e293b; border-bottom: 2px solid #ff6000; background: #fafafa;">
                📦 Tüm Kategoriler
            </div>
            <div style="display: flex; flex-direction: column; flex: 1; justify-content: flex-start;">
                <?php
                $ana_kat_sorgu = $db->query("SELECT * FROM kategoriler ORDER BY kategori_adi ASC");
                $ana_kategoriler = $ana_kat_sorgu->fetchAll(PDO::FETCH_ASSOC);

                if (count($ana_kategoriler) > 0):
                    foreach ($ana_kategoriler as $kat): ?>
                        <a href="urunler.php?kategori=<?php echo $kat['id']; ?>" 
                           style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; color: #334155; text-decoration: none; font-size: 13.5px; font-weight: 600; border-bottom: 1px solid #f1f5f9; transition: all 0.15s; background: #ffffff;"
                           onmouseover="this.style.background='#fff5ed'; this.style.color='#ff6000';" 
                           onmouseout="this.style.background='#ffffff'; this.style.color='#334155';">
                            <span><?php echo htmlspecialchars($kat['kategori_adi']); ?></span>
                            <span style="font-size: 10px; color: #94a3b8;">❯</span>
                        </a>
                    <?php endforeach; 
                else: ?>
                    <div style="padding: 20px; text-align: center; color: #94a3b8; font-size: 13px;">Kategori bulunamadı.</div>
                <?php endif; ?>
            </div>
        </aside>

        <div style="flex: 1; border-radius: 4px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.05); background: #ffffff; height: 380px;">
            <img src="Gorseller/reklam.jpg" alt="Büyük Bahar Kampanyası" style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>

    </div>
</div>

<div style="max-width: 1200px; margin: 35px auto 15px auto; display: flex; justify-content: space-between; align-items: center; padding: 0 15px;">
        <h2 style="font-size: 22px; color: #333; font-weight: 700;">🔥 Günün Fırsatları</h2>
        <a href="urunler.php" style="color: #ff6000; text-decoration: none; font-weight: bold; font-size: 14px;">Tümünü Gör ></a>
    </div>
    <div class="slider-wrapper">
        <button class="slider-btn prev" onclick="slide('prev')">❮</button>
        
        <div class="slider-container" id="urunSlider">
            
            <?php
            $urunSorgu = $db->query("SELECT * FROM urunler ORDER BY id DESC LIMIT 8");
            while ($urun = $urunSorgu->fetch(PDO::FETCH_ASSOC)) {
                // Veritabanında geçerli bir indirim var mı kontrol ediyoruz
                $indirimVarMi = (!empty($urun['eski_fiyat']) && $urun['eski_fiyat'] > $urun['fiyat']);
                
                // Çoklu resim ihtimaline karşı resmi virgülden bölüp ilk elemanı seçiyoruz
                $gorselParcala = explode(',', $urun['urun_gorsel']);
                $ilkGorsel = trim($gorselParcala[0]);
                ?>
                <section class="card">
                    <?php if ($indirimVarMi): ?>
                        <span class="rozet-firsat" style="background:#27ae60;">İNDİRİMLİ FİYAT</span>
                        <span class="rozet-cok-satan">🔥 Çok Satan</span>
                    <?php endif; ?>
                    
                    <a href="urun-detay.php?id=<?php echo $urun['id']; ?>" style="text-decoration: none; color: inherit;">
                        <img src="Gorseller/<?php echo $ilkGorsel; ?>" alt="<?php echo $urun['urun_adi']; ?>">
                        <h2><?php echo $urun['urun_adi']; ?></h2>
                        <p style="font-size: 12px; color: #666; margin: 5px 0; height: 32px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 1.3; word-break: break-all;">
                            <?php echo $urun['urun_aciklama']; ?>
                        </p>
                        
                        <div style="margin-top: 10px;">
                            <?php if ($indirimVarMi): 
                                $indirim_orani = round((($urun['eski_fiyat'] - $urun['fiyat']) / $urun['eski_fiyat']) * 100);
                            ?>
                                <div class="eski-fiyat"><?php echo number_format($urun['eski_fiyat'], 0, ',', '.'); ?> TL</div>
                                
                                <div style="display: flex; align-items: center;">
                                    <span class="fiyat" style="color: #27ae60; font-size: 20px; font-weight: 800;">
                                        <?php echo number_format($urun['fiyat'], 0, ',', '.'); ?> TL
                                    </span>
                                    <span class="indirim-oran" style="background:#27ae60;">%<?php echo $indirim_orani; ?></span>
                                </div>
                            <?php else: ?>
                                <div class="eski-fiyat" style="visibility: hidden;">0 TL</div> 
                                <div style="display: flex; align-items: center;">
                                    <span class="fiyat" style="color: #333; font-size: 20px; font-weight: 800;">
                                        <?php echo number_format($urun['fiyat'], 0, ',', '.'); ?> TL
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>

                    <form action="sepet-islem.php?islem=ekle" method="POST">
                        <input type="hidden" name="urun_id" value="<?php echo $urun['id']; ?>">
                        <button type="submit" class="hizli-sepet-btn" title="Hızlı Ekle">🛒</button>
                    </form>
                </section>
                <?php
            }
            ?>
        </div>

        <button class="slider-btn next" onclick="slide('next')">❯</button>
    </div>
</main>

<?php include 'footer.php'; ?>

<script>
    function slide(direction) {
        const container = document.getElementById('urunSlider');
        if (!container) return; 
        const scrollAmount = container.clientWidth / 2; 
        
        if (direction === 'prev') {
            container.scrollLeft -= scrollAmount;
        } else {
            container.scrollLeft += scrollAmount;
        }
    }
</script>
    
</body>
</html>