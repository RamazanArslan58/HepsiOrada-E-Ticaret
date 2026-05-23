<?php 
session_start(); 
include 'baglan.php'; 

// Dinamik Kategoriler
$kategori_sorgu = $db->query("SELECT * FROM kategoriler ORDER BY kategori_adi ASC");
$kategoriler = $kategori_sorgu->fetchAll(PDO::FETCH_ASSOC);

// Filtre Parametrelerini Yakalama
$aktif_kategori = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;
$arama_kelimesi = isset($_GET['ara']) ? trim($_GET['ara']) : '';
$min_fiyat = isset($_GET['min']) && is_numeric($_GET['min']) ? floatval($_GET['min']) : 0;
$max_fiyat = isset($_GET['max']) && is_numeric($_GET['max']) ? floatval($_GET['max']) : 0;

// Dinamik SQL Sorgusu Oluşturma
$sql = "SELECT * FROM urunler WHERE 1=1"; // WHERE 1=1 teknik bir hiledir, sonrasına kolayca AND eklememizi sağlar
$params = [];

// Kategori Filtresi
if ($aktif_kategori > 0) {
    $sql .= " AND kategori_id = ?";
    $params[] = $aktif_kategori;
}

// Arama Filtresi
if (!empty($arama_kelimesi)) {
    $sql .= " AND (urun_adi LIKE ? OR urun_aciklama LIKE ?)";
    $arama_param = "%$arama_kelimesi%";
    $params[] = $arama_param;
    $params[] = $arama_param;
}

// Fiyat Filtresi (İstediğin ana kısım burası)
if ($min_fiyat > 0) {
    $sql .= " AND fiyat >= ?";
    $params[] = $min_fiyat;
}
if ($max_fiyat > 0) {
    $sql .= " AND fiyat <= ?";
    $params[] = $max_fiyat;
}

$sql .= " ORDER BY id DESC";
$sorgu = $db->prepare($sql);
$sorgu->execute($params);
$urunler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürünler | HepsiOrada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #ffffff; margin: 0; padding: 0;">
    <?php include 'nav.php'; ?>

    <div style="background: #f1f3f5; padding: 25px 0; font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; gap: 20px; padding: 0 15px; align-items: flex-start;">
            
            <aside style="flex: 0 0 250px; background: #ffffff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e2e8f0; position: sticky; top: 110px;">
                <div style="padding: 15px 18px; font-weight: 700; font-size: 14px; color: #1e293b; border-bottom: 2px solid #ff6000; background: #fafafa;">
                    Kategoriler
                </div>
                <div style="display: flex; flex-direction: column;">
                    <a href="urunler.php" 
                       style="padding: 13px 18px; color: <?php echo $aktif_kategori == 0 ? '#ff6000' : '#484848'; ?>; text-decoration: none; font-size: 13.5px; font-weight: 600; border-bottom: 1px solid #f1f5f9; background: <?php echo $aktif_kategori == 0 ? '#fff5ed' : '#ffffff'; ?>;">
                       Tüm Ürünler
                    </a>
                    <?php foreach ($kategoriler as $kat): $secili_mi = ($aktif_kategori == $kat['id']); ?>
                        <a href="urunler.php?kategori=<?php echo $kat['id']; ?>" 
                           style="padding: 13px 18px; color: <?php echo $secili_mi ? '#ff6000' : '#484848'; ?>; text-decoration: none; font-size: 13.5px; font-weight: 600; border-bottom: 1px solid #f1f5f9; transition: all 0.1s; background: <?php echo $secili_mi ? '#fff5ed' : '#ffffff'; ?>;"
                           onmouseover="if(<?php echo $secili_mi ? 'false' : 'true'; ?>) this.style.background='#fdf1e8';" 
                           onmouseout="if(<?php echo $secili_mi ? 'false' : 'true'; ?>) this.style.background='#ffffff';">
                            <?php echo htmlspecialchars($kat['kategori_adi']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div style="padding: 20px 18px; background: #fafafa; border-top: 1px solid #e2e8f0;">
                    <span style="font-size: 13px; font-weight: 700; color: #1e293b; display: block; margin-bottom: 12px;">Fiyat Aralığı</span>
                    <form action="urunler.php" method="GET" style="display: flex; flex-direction: column; gap: 8px;">
                        <input type="hidden" name="kategori" value="<?php echo $aktif_kategori; ?>">
                        <?php if(!empty($arama_kelimesi)): ?>
                            <input type="hidden" name="ara" value="<?php echo htmlspecialchars($arama_kelimesi); ?>">
                        <?php endif; ?>

                        <div style="display: flex; gap: 5px;">
                            <input type="number" name="min" value="<?php echo $min_fiyat > 0 ? $min_fiyat : ''; ?>" placeholder="En Az" style="width: 50%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">
                            <input type="number" name="max" value="<?php echo $max_fiyat > 0 ? $max_fiyat : ''; ?>" placeholder="En Çok" style="width: 50%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">
                        </div>
                        <button type="submit" style="background: #ff6000; color: white; border: none; padding: 8px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 12px;">UYGULA</button>
                        
                        <?php if($min_fiyat > 0 || $max_fiyat > 0): ?>
                            <a href="urunler.php?kategori=<?php echo $aktif_kategori; ?>" style="text-align:center; font-size:11px; color:#666; text-decoration:none;">Filtreyi Temizle</a>
                        <?php endif; ?>
                    </form>
                </div>
            </aside>

            <div style="flex: 1;">
                <?php if (!empty($arama_kelimesi)): ?>
                    <div style="margin-bottom: 15px; font-size: 16px; color: #1e293b; font-weight: 600;">
                        "<?php echo htmlspecialchars($arama_kelimesi); ?>" için sonuçlar:
                    </div>
                <?php endif; ?>

                <div style="flex: 1;">
                    <?php if (!empty($arama_kelimesi)): ?>
                        <div style="margin-bottom: 15px; font-size: 16px; color: #1e293b; font-weight: 600;">
                            "<?php echo htmlspecialchars($arama_kelimesi); ?>" için sonuçlar:
                        </div>
                    <?php endif; ?>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px;">
                        <?php
                        // ESKİ SORGULARI SİLDİK, EN ÜSTTE HAZIRLADIĞIMIZ $urunler DEĞİŞKENİNİ KONTROL EDİYORUZ
                        if (count($urunler) == 0) {
                            echo "<div style='grid-column: 1 / -1; background: #fff; padding: 50px; text-align: center; border-radius: 4px; color: #666;'>Aradığınız kriterlere uygun ürün bulunamadı.</div>";
                        }

                        foreach ($urunler as $urun) {
                            $indirimVarMi = (!empty($urun['eski_fiyat']) && $urun['eski_fiyat'] > $urun['fiyat']);
                            $gorselParcala = explode(',', $urun['urun_gorsel']);
                            $ilkGorsel = trim($gorselParcala[0]);
                            ?>
                        <section class="card" style="position: relative; display:flex; flex-direction:column; justify-content:space-between; background: #ffffff;">
                            
                            <?php if ($indirimVarMi): ?>
                                <span class="rozet" style="position:absolute; top:10px; left:10px; background:#27ae60; color:#fff; font-size:11px; padding:3px 8px; border-radius:4px; font-weight:bold; z-index: 2;">İNDİRİMLİ FİYAT</span>
                                <span class="rozet-cok-satan">🔥 Çok Satan</span>
                            <?php endif; ?>
                            
                            <a href="urun-detay.php?id=<?php echo $urun['id']; ?>" style="text-decoration: none; color: inherit;">
                                <img src="Gorseller/<?php echo $ilkGorsel; ?>" style="width:100%; height:180px; margin-bottom:10px; object-fit: cover;">
                                <h2 style="font-size:14px; height:40px; overflow:hidden; margin-bottom:5px;"><?php echo $urun['urun_adi']; ?></h2>
                                <p style="font-size: 12px; color: #666; margin: 5px 0; height: 32px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 1.3; word-break: break-all;">
                                    <?php echo $urun['urun_aciklama']; ?>
                                </p>
                                
                                <div style="margin-top:10px;">
                                    <?php if ($indirimVarMi): 
                                        $indirim_orani = round((($urun['eski_fiyat'] - $urun['fiyat']) / $urun['eski_fiyat']) * 100);
                                    ?>
                                        <div style="text-decoration:line-through; color:#999; font-size:12px;">
                                            <?php echo number_format($urun['eski_fiyat'], 0, ',', '.'); ?> TL
                                        </div>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <span style="font-size:20px; font-weight:800; color:#27ae60;"><?php echo number_format($urun['fiyat'], 0, ',', '.'); ?> TL</span>
                                            <span style="background:#27ae60; color:#fff; font-size:11px; font-weight:bold; padding:2px 5px; border-radius:4px;">%<?php echo $indirim_orani; ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div style="height: 18px;"></div> 
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <span style="font-size:20px; font-weight:800; color:#333;"><?php echo number_format($urun['fiyat'], 0, ',', '.'); ?> TL</span>
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
            </div>

        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>