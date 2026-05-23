<?php 
include 'baglan.php'; 
include 'admin-nav.php'; 

// İstatistikleri Çek
$toplam_urun = $db->query("SELECT count(*) FROM urunler")->fetchColumn();
$toplam_kat = $db->query("SELECT count(*) FROM kategoriler")->fetchColumn();

// Son eklenen 5 ürünü KATEGORİ ADIYLA beraber çek
$son_urunler = $db->query("SELECT urunler.*, kategoriler.kategori_adi 
                           FROM urunler 
                           LEFT JOIN kategoriler ON urunler.kategori_id = kategoriler.id 
                           ORDER BY urunler.id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="max-width: 1100px;">
    <h2 style="margin-bottom: 25px; color: #1e293b; font-weight: 700;">📊 Panel Özeti</h2>
    
    <div style="display: flex; gap: 20px; margin-bottom: 40px;">
        <div style="flex: 1; background: #fff; padding: 25px; border-radius: 15px; border-left: 6px solid #ff6000; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <div style="color: #64748b; font-weight: bold; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Toplam Ürün</div>
            <h3 style="font-size: 32px; margin-top: 10px; color: #1e293b; font-family: sans-serif;"><?php echo $toplam_urun; ?></h3>
        </div>
        
        <div style="flex: 1; background: #fff; padding: 25px; border-radius: 15px; border-left: 6px solid #1e293b; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <div style="color: #64748b; font-weight: bold; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Kategoriler</div>
            <h3 style="font-size: 32px; margin-top: 10px; color: #1e293b; font-family: sans-serif;"><?php echo $toplam_kat; ?></h3>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #1e293b; font-weight: 700;">🕒 Son Eklenen 5 Ürün</h2>
        <a href="urun-ekle.php" style="color: #ff6000; text-decoration: none; font-weight: 600; font-size: 14px;">Tümünü Gör →</a>
    </div>

    <div style="background: #fff; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #1e293b; color: #fff;">
                    <th style="padding: 20px; font-size: 14px;">Görsel</th>
                    <th style="padding: 20px; font-size: 14px;">Ürün Adı</th>
                    <th style="padding: 20px; font-size: 14px;">Kategori</th>
                    <th style="padding: 20px; font-size: 14px;">Fiyat</th>
                    <th style="padding: 20px; font-size: 14px; text-align: center;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($son_urunler) > 0): ?>
                    <?php foreach($son_urunler as $u):
                        $resimler = explode(',', $u['urun_gorsel']);
                        $ilkResim = trim($resimler[0]); 
                    ?>
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: 0.3s;" onmouseover="this.style.background='#fcfcfc'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 15px;">
                            <img src="Gorseller/<?php echo $ilkResim; ?>" 
                                 width="55" height="55" 
                                 style="object-fit:cover; border-radius:10px; border: 1px solid #eee;"
                                 onerror="this.src='https://via.placeholder.com/60?text=Yok';">
                        </td>
                        <td style="padding: 15px; font-weight: 600; color: #334155;"><?php echo $u['urun_adi']; ?></td>
                        <td style="padding: 15px;">
                            <span style="background: #f1f5f9; padding: 5px 10px; border-radius: 15px; font-size: 12px; color: #475569; font-weight: 600;">
                                <?php echo $u['kategori_adi'] ? $u['kategori_adi'] : 'Kategorisiz'; ?>
                            </span>
                        </td>
                        <td style="padding: 15px; color: #ff6000; font-weight: 800;">
                            <?php echo number_format($u['fiyat'], 2, ',', '.'); ?> TL
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="urun-duzenle.php?id=<?php echo $u['id']; ?>" 
                               style="display: inline-block; padding: 8px 15px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 600; margin-right: 5px;">
                               Düzenle
                            </a>
                            <a href="urun-sil.php?id=<?php echo $u['id']; ?>" 
                               onclick="return confirm('Silmek istediğine emin misin?')"
                               style="display: inline-block; padding: 8px 15px; background: #ef4444; color: white; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 600;">
                               Sil
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="padding: 30px; text-align: center; color: #64748b;">Henüz hiç ürün eklenmemiş.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>