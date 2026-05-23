<?php 
session_start(); 
include 'baglan.php'; 

if (isset($_GET['id'])) {
    $urun_id = intval($_GET['id']);
    $sorgu = $db->prepare("SELECT * FROM urunler WHERE id = ?");
    $sorgu->execute([$urun_id]);
    $urun = $sorgu->fetch(PDO::FETCH_ASSOC);

    if (!$urun) {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

// --- YORUM KAYDETME ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['yorum_gonder'])) {
    $uye_adi = isset($_SESSION['uye_adi']) ? $_SESSION['uye_adi'] : (isset($_SESSION['uye_eposta']) ? $_SESSION['uye_eposta'] : 'Anonim Kullanıcı');
    $yildiz = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
    $yorum_metni = trim($_POST['yorum_metni']);

    if (!empty($yorum_metni)) {
        $yorum_ekle = $db->prepare("INSERT INTO yorumlar (urun_id, uye_adi, yildiz, yorum_metni) VALUES (?, ?, ?, ?)");
        $yorum_ekle->execute([$urun_id, $uye_adi, $yildiz, $yorum_metni]);
        
        header("Location: urun-detay.php?id=" . $urun_id . "#yorumlar-alani");
        exit();
    }
}

// --- YORUM SAYFALAMA VE İSTATİSTİK ---
$yorum_sayfa = isset($_GET['y_sayfa']) ? intval($_GET['y_sayfa']) : 1;
$yorum_limit = 5; 
$yorum_offset = ($yorum_sayfa - 1) * $yorum_limit;

// Toplam yorum sayısı ve sayfalamasız tüm veriler (İstatistik için)
$istatistik_sorgu = $db->prepare("SELECT yildiz FROM yorumlar WHERE urun_id = ?");
$istatistik_sorgu->execute([$urun_id]);
$tüm_yorumlar_istatistik = $istatistik_sorgu->fetchAll(PDO::FETCH_ASSOC);

$toplam_yorum = count($tüm_yorumlar_istatistik);
$toplam_yorum_sayfasi = ceil($toplam_yorum / $yorum_limit);

// Sayfalı yorumları çek
$yorum_sorgu = $db->prepare("SELECT * FROM yorumlar WHERE urun_id = ? ORDER BY tarih DESC LIMIT $yorum_limit OFFSET $yorum_offset");
$yorum_sorgu->execute([$urun_id]);
$yorumlar = $yorum_sorgu->fetchAll(PDO::FETCH_ASSOC);

$ortalama_yildiz = 0;
$yildiz_sayilari = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

if ($toplam_yorum > 0) {
    $toplam_yildiz_puani = 0;
    foreach ($tüm_yorumlar_istatistik as $y) {
        $toplam_yildiz_puani += $y['yildiz'];
        if (isset($yildiz_sayilari[$y['yildiz']])) {
            $yildiz_sayilari[$y['yildiz']]++;
        }
    }
    $ortalama_yildiz = round($toplam_yildiz_puani / $toplam_yorum, 1);
}

// --- SORU SAYFALAMA ---
$soru_sayfa = isset($_GET['s_sayfa']) ? intval($_GET['s_sayfa']) : 1;
$soru_limit = 5;
$soru_offset = ($soru_sayfa - 1) * $soru_limit;

$soru_count = $db->prepare("SELECT COUNT(*) FROM urun_sorulari WHERE urun_id = ? AND cevap_metni IS NOT NULL");
$soru_count->execute([$urun_id]);
$toplam_soru_sayisi = $soru_count->fetchColumn();
$toplam_soru_sayfasi = ceil($toplam_soru_sayisi / $soru_limit);

$soru_listesi = $db->prepare("SELECT s.*, u.ad_soyad FROM urun_sorulari s 
                                JOIN uyeler u ON s.uye_id = u.id 
                                WHERE s.urun_id = ? AND s.cevap_metni IS NOT NULL 
                                ORDER BY s.tarih DESC LIMIT $soru_limit OFFSET $soru_offset");
$soru_listesi->execute([$urun_id]);
$sorular = $soru_listesi->fetchAll(PDO::FETCH_ASSOC);

$gorseller = explode(',', $urun['urun_gorsel']); 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($urun['urun_adi']); ?> - Detay</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .btn-sepet { background: #ff6000; color: white; padding: 18px 60px; border: none; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; width: 100%; max-width: 400px; transition: all 0.3s ease; position: relative; overflow: hidden; }
        .btn-sepet:hover { background: #ff7900; transform: translateY(-3px); box-shadow: 0 6px 15px rgba(255, 96, 0, 0.4); }
        .indirim-rozet { position: absolute; top: 15px; left: 15px; background: #27ae60; color: white; padding: 5px 12px; border-radius: 4px; font-weight: bold; font-size: 14px; z-index: 10; }
        .rating-input { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 4px; }
        .rating-input input { display: none; }
        .rating-input label { font-size: 30px; color: #ddd; cursor: pointer; transition: color 0.2s; }
        .rating-input input:checked ~ label, .rating-input label:hover, .rating-input label:hover ~ label { color: #ff9800; }
        .progress-bar { background: #eee; border-radius: 4px; overflow: hidden; height: 8px; width: 150px; }
        .progress-fill { background: #ff9800; height: 100%; }
        
        /* Sayfalama Stilleri */
        .sayfalama { display: flex; justify-content: center; gap: 5px; margin-top: 20px; }
        .sayfalama a { padding: 8px 12px; background: #fff; border: 1px solid #ddd; color: #333; text-decoration: none; border-radius: 4px; font-size: 13px; }
        .sayfalama a.aktif { background: #ff6000; color: white; border-color: #ff6000; }

        @keyframes modalGelis { from { transform: translate(-50%, -80%); opacity: 0; } to { transform: translate(-50%, -50%); opacity: 1; } }
        #sepetModalContent { animation: modalGelis 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .badge-container { display: flex; align-items: center; }
        .badge-question { background: #334155; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 800; }
        .badge-answer { background: #ea580c; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 800; }
    </style>
</head>
<body style="background: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif;">
    <?php include 'nav.php'; ?>

<main style="max-width: 1200px; margin: 0 auto; padding: 0 15px;">
    
    <div class="detay-container" style="display: flex; gap: 40px; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin: 30px auto; align-items: flex-start; flex-wrap: wrap;">
        
        <div class="detay-sol" style="flex: 0 0 450px; position: relative; min-width: 300px;">
            <div style="position: relative; width: 100%; height: 450px; background: #fff; border-radius: 8px; padding: 20px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                
                <?php if (!empty($urun['eski_fiyat']) && $urun['eski_fiyat'] > $urun['fiyat']): ?>
                    <?php $detay_indirim_orani = round((($urun['eski_fiyat'] - $urun['fiyat']) / $urun['eski_fiyat']) * 100); ?>
                    <div class="indirim-rozet">%<?php echo $detay_indirim_orani; ?> İndirim</div>
                <?php endif; ?>

                <button type="button" onclick="galeriKaydir('sol')" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.9); border: 1px solid #ddd; color: #ff6000; font-weight: bold; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; z-index: 5; display: flex; align-items: center; justify-content: center;">❮</button>
                <img id="anaGorsel" src="Gorseller/<?php echo trim($gorseller[0]); ?>" alt="<?php echo htmlspecialchars($urun['urun_adi']); ?>" style="width: 100%; height: 100%; object-fit: contain; max-height: 400px; display: block; transition: all 0.2s ease;">
                <button type="button" onclick="galeriKaydir('sag')" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.9); border: 1px solid #ddd; color: #ff6000; font-weight: bold; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; z-index: 5; display: flex; align-items: center; justify-content: center;">❯</button>
            </div>

            <div style="max-width: 500px; margin-top: 15px;">
                <div id="kucukResimSlider" style="display: flex; gap: 10px; overflow-x: auto; scroll-behavior: smooth; width: 100%; padding: 5px 0;">
                    <?php 
                    foreach ($gorseller as $index => $gorsel): 
                        $gorselAdi = trim($gorsel);
                        if(!empty($gorselAdi)):
                    ?>
                        <div class="kucuk-resim-kutu" 
                            onclick="resimDegistir('Gorseller/<?php echo $gorselAdi; ?>', this)"
                            data-index="<?php echo $index; ?>"
                            style="flex: 0 0 70px; width: 70px; height: 70px; border: 2px solid <?php echo $index === 0 ? '#ff6000' : '#e2e8f0'; ?>; border-radius: 6px; padding: 5px; cursor: pointer; background: #fff; display: flex; align-items: center; justify-content: center;">
                            <img src="Gorseller/<?php echo $gorselAdi; ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>

        <div class="detay-sag" style="flex: 1; min-width: 300px;">
            <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px;">
                 <h1 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 10px; line-height: 1.3;">
                    <?php echo htmlspecialchars($urun['urun_adi']); ?>
                 </h1>

                 <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <span style="color: #ff9800; font-size: 18px;">
                        <?php echo $toplam_yorum > 0 ? str_repeat('★', round($ortalama_yildiz)) . str_repeat('☆', 5 - round($ortalama_yildiz)) : '★★★★★'; ?>
                    </span>
                    <a href="#yorumlar-alani" style="color: #ff6000; text-decoration: none; font-size: 13px; font-weight: 600;">
                        <?php echo $toplam_yorum; ?> Değerlendirme
                    </a>
                    <span style="color: #cbd5e1;">|</span>
                    <span style="font-size: 13px; color: #64748b;">Satıcı: <strong style="color: #27ae60;">HepsiOrada</strong></span>
                 </div>
            </div>

            <div style="margin-bottom: 25px;">
                <?php if (!empty($urun['eski_fiyat']) && $urun['eski_fiyat'] > $urun['fiyat']): 
                    $oran = round((($urun['eski_fiyat'] - $urun['fiyat']) / $urun['eski_fiyat']) * 100);
                ?>
                    <div style="text-decoration: line-through; color: #94a3b8; font-size: 18px;">
                        <?php echo number_format($urun['eski_fiyat'], 0, ',', '.'); ?> TL
                    </div>
                    <div style="font-size: 36px; color: #ff6000; font-weight: 800; display: flex; align-items: center; gap: 10px;">
                        <?php echo number_format($urun['fiyat'], 0, ',', '.'); ?> TL
                        <span style="background: #27ae60; color: white; font-size: 14px; padding: 3px 8px; border-radius: 4px; font-weight: bold;">
                            %<?php echo $oran; ?> İndirim
                        </span>
                    </div>
                <?php else: ?>
                    <div style="font-size: 36px; color: #1e293b; font-weight: 800;">
                        <?php echo number_format($urun['fiyat'], 0, ',', '.'); ?> TL
                    </div>
                <?php endif; ?>
            </div>

            <div style="margin-bottom: 30px; display: flex; gap: 15px; align-items: center;">
                <input type="hidden" id="eklenecek_urun_id" value="<?php echo $urun['id']; ?>">
                <button type="button" class="btn-sepet" onclick="sepeteEkleAjax()" style="flex: 1;">
                    🛒 Sepete Ekle
                </button>

                <?php 
                $favoride_mi = false;
                if (isset($_SESSION['uye_id'])) {
                    $fav_kontrol = $db->prepare("SELECT * FROM favoriler WHERE uye_id = ? AND urun_id = ?");
                    $fav_kontrol->execute([$_SESSION['uye_id'], $urun['id']]);
                    if ($fav_kontrol->rowCount() > 0) { $favoride_mi = true; }
                }
                ?>

                <a href="favori-ekle.php?id=<?php echo $urun['id']; ?>" 
                class="fav-btn-action"
                title="<?php echo $favoride_mi ? 'Favorilerden Çıkar' : 'Favorilere Ekle'; ?>"
                style="display: flex; align-items: center; justify-content: center; width: 58px; height: 58px; border: 2px solid #f1f5f9; border-radius: 12px; background: white; text-decoration: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <span style="font-size: 28px; transition: all 0.3s ease;">
                        <?php echo $favoride_mi ? '❤️' : '🤍'; ?>
                    </span>
                </a>
            </div>

            <div style="background: #fff9f5; border: 1px solid #ffede1; padding: 20px; border-radius: 8px; margin-bottom: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: white; padding: 8px; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.02); font-size: 18px;">🚚</div>
                    <div>
                        <p style="font-size: 13px; color: #27ae60; font-weight: bold; margin: 0;">Ücretsiz Kargo</p>
                        <p style="font-size: 12px; color: #64748b; margin: 0;">Yarın kapında!</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: white; padding: 8px; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.02); font-size: 18px;">🛡️</div>
                    <div>
                        <p style="font-size: 13px; color: #1e293b; font-weight: bold; margin: 0;">HepsiOrada Garantisi</p>
                        <p style="font-size: 12px; color: #64748b; margin: 0;">14 Gün Kolay İade</p>
                    </div>
                </div>
            </div>

            <div style="background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <h3 style="font-size: 15px; color: #1e293b; margin-bottom: 12px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">📋 Ürün Açıklaması</h3>
                <p style="font-size: 14px; color: #475569; line-height: 1.7; word-wrap: break-word; white-space: pre-line; margin: 0;">
                    <?php echo htmlspecialchars($urun['urun_aciklama']); ?>
                </p>
            </div>
        </div>
    </div>

    <div id="yorumlar-alani" style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin: 30px auto;">
        <h2 style="font-size: 18px; color: #1e293b; margin-top:0; margin-bottom: 25px; border-bottom: 2px solid #ff6000; padding-bottom: 8px; display: inline-block;">Ürün Değerlendirmeleri</h2>
        
        <div style="display: flex; gap: 40px; margin-bottom: 40px; flex-wrap: wrap;">
            <div style="flex: 0 0 280px; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center;">
                <h1 style="font-size: 44px; color: #ff6000; margin: 0 0 5px 0; font-weight: 800;"><?php echo $ortalama_yildiz; ?></h1>
                <div style="color: #ff9800; font-size: 20px; margin-bottom: 10px;">
                    <?php echo $toplam_yorum > 0 ? str_repeat('★', round($ortalama_yildiz)) . str_repeat('☆', 5 - round($ortalama_yildiz)) : '☆☆☆☆☆'; ?>
                </div>
                <p style="font-size: 13px; color: #64748b; margin: 0; font-weight: 500;"><?php echo $toplam_yorum; ?> ürün değerlendirmesi</p>
                
                <div style="margin-top: 20px; text-align: left; display: flex; flex-direction: column; gap: 6px; font-size: 12px; color: #475569;">
                    <?php for($i=5; $i>=1; $i--): 
                        $yuzde = $toplam_yorum > 0 ? round(($yildiz_sayilari[$i] / $toplam_yorum) * 100) : 0;
                    ?>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="width: 45px; text-align: right;"><?php echo $i; ?> Yıldız</span>
                        <div class="progress-bar"><div class="progress-fill" style="width: <?php echo $yuzde; ?>%;"></div></div>
                        <span style="color:#94a3b8; width: 30px;"><?php echo $yuzde; ?>%</span>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div style="flex: 1; min-width: 300px; border: 1px solid #e2e8f0; padding: 24px; border-radius: 8px;">
                <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 15px; color: #1e293b;">Bu ürünü değerlendirin</h3>
                
                <?php if (isset($_SESSION['uye_id'])): ?>
                    <form action="" method="POST">
                        <div style="margin-bottom: 15px;">
                            <label style="display:block; font-size: 13px; color: #475569; margin-bottom: 5px; font-weight: 600;">Ürüne Puanınız:</label>
                            <div class="rating-input">
                                <input type="radio" id="star5" name="rating" value="5" checked><label for="star5">★</label>
                                <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
                                <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
                                <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
                                <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
                            </div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <textarea id="yorum_metni" name="yorum_metni" rows="4" required placeholder="Deneyimlerinizi paylaşın..." style="width:100%; border:1px solid #cbd5e1; border-radius:6px; padding:12px; font-size:14px; outline:none; resize: vertical; box-sizing: border-box; font-family: inherit;"></textarea>
                        </div>
                        <button type="submit" name="yorum_gonder" style="background: #1e293b; color: white; border: none; padding: 10px 24px; font-size: 14px; font-weight: bold; border-radius: 6px; cursor: pointer;">Yorumu Gönder</button>
                    </form>
                <?php else: ?>
                    <div style="background: #f8fafc; border: 1px dashed #cbd5e1; padding: 30px; text-align: center; border-radius: 6px;">
                        <p style="color: #64748b; font-size: 14px; margin: 0 0 15px 0;">Yorum yazabilmek için üye girişi yapmalısınız.</p>
                        <a href="giris.php" style="background: #ff6000; color: white; text-decoration: none; padding: 8px 18px; font-size: 13px; font-weight: bold; border-radius: 4px;">Giriş Yap</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php if ($toplam_yorum > 0): ?>
                <?php foreach ($yorumlar as $yorum): ?>
                    <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; display: flex; gap: 15px; align-items: flex-start; background: #fff;">
                        <div style="width: 40px; height: 40px; background: #e2e8f0; color: #475569; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">
                            <?php echo strtoupper(mb_substr($yorum['uye_adi'], 0, 1, 'UTF-8')); ?>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <div>
                                    <strong style="font-size: 14px; color: #334155;"><?php echo htmlspecialchars($yorum['uye_adi']); ?></strong>
                                    <span style="color: #ff9800; font-size: 14px; margin-left: 8px;">
                                        <?php echo str_repeat('★', $yorum['yildiz']) . str_repeat('☆', 5 - $yorum['yildiz']); ?>
                                    </span>
                                </div>
                                <span style="font-size: 12px; color: #94a3b8;"><?php echo date('d.m.Y H:i', strtotime($yorum['tarih'])); ?></span>
                            </div>
                            <p style="font-size: 14px; color: #475569; line-height: 1.6; margin: 0;">
                                <?php echo nl2br(htmlspecialchars($yorum['yorum_metni'])); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if($toplam_yorum_sayfasi > 1): ?>
                    <div class="sayfalama">
                        <?php for($i=1; $i<=$toplam_yorum_sayfasi; $i++): ?>
                            <a href="urun-detay.php?id=<?php echo $urun_id; ?>&y_sayfa=<?php echo $i; ?>&s_sayfa=<?php echo $soru_sayfa; ?>#yorumlar-alani" class="<?php echo ($i == $yorum_sayfa) ? 'aktif' : ''; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div style="text-align: center; padding: 30px; border: 1px dashed #e2e8f0; border-radius: 8px; color: #94a3b8;">
                    Henüz yorum yapılmamış. İlk yorumu siz yapın!
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="soru-sor-alani" style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-top: 20px;">
        <h3 style="font-size: 18px; color: #1e293b; margin-bottom: 20px; border-bottom: 2px solid #ff6000; padding-bottom: 8px; display: inline-block;">Ürün Hakkında Soru Sor</h3>

        <?php if (isset($_SESSION['uye_id'])): ?>
            <form action="soru-gonder.php" method="POST">
                <input type="hidden" name="urun_id" value="<?php echo $urun['id']; ?>">
                <textarea name="soru_metni" rows="3" required placeholder="Satıcıya ürünle ilgili merak ettiklerinizi sorun..." 
                    style="width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:15px; font-size:14px; outline:none; resize: none; box-sizing: border-box;"></textarea>
                <button type="submit" style="margin-top: 10px; background: #ff6000; color: white; border: none; padding: 12px 25px; font-weight: bold; border-radius: 8px; cursor: pointer; transition: 0.3s;">
                    Soruyu Gönder
                </button>
            </form>
        <?php else: ?>
            <div style="background: #f8fafc; padding: 20px; text-align: center; border-radius: 8px; border: 1px dashed #cbd5e1;">
                <p style="font-size: 14px; color: #64748b;">Soru sorabilmek için lütfen giriş yapın.</p>
            </div>
        <?php endif; ?>
    </div>

    <div id="soru-cevap-alani" style="background: #fff; padding: 30px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <h3 style="font-size: 18px; margin-bottom: 25px; color: #1e293b;">Ürün Soruları ve Cevapları</h3>

        <div class="soru-cevap-wrapper">
            <?php foreach ($sorular as $s): ?>
                <div class="soru-kart">
                    <div class="soru-ust-baslik">
                        <span class="badge-modern badge-soru">Soru</span>
                        <div style="flex: 1;">
                            <p class="soru-metni"><?php echo htmlspecialchars($s['soru_metni']); ?></p>
                            <div class="kullanici-meta">
                                <span>👤 <?php echo htmlspecialchars($s['ad_soyad']); ?></span>
                                <span>•</span>
                                <span>📅 <?php echo date("d.m.Y", strtotime($s['tarih'])); ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if ($s['cevap_metni']): ?>
                        <div class="cevap-konteyner">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                                <span class="badge-modern badge-cevap">Cevap</span>
                            </div>
                            <p class="cevap-metni"><?php echo htmlspecialchars($s['cevap_metni']); ?></p>
                            <div class="satici-etiket">
                                <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Satıcı Yanıtı
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<div id="sepetModal" style="display: none; position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100% !important; height: 100% !important; background-color: rgba(0,0,0,0.7) !important; z-index: 999999999 !important; align-items: center !important; justify-content: center !important; backdrop-filter: blur(8px);">
    <div id="sepetModalContent" style="background: white !important; padding: 40px !important; border-radius: 20px !important; width: 350px !important; text-align: center !important; box-shadow: 0 0 30px rgba(0,0,0,0.5) !important; position: fixed !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important;">
        <div style="font-size: 60px; margin-bottom: 15px;">🎉</div>
        <h2 style="margin: 0 0 10px 0; color: #1e293b; font-family: sans-serif; font-weight: 800;">Sepete Eklendi!</h2>
        <p style="color: #64748b; font-size: 15px; margin-bottom: 25px;">Alışverişe devam mı, sepetine mi gitmek istersin?</p>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <a href="sepetim.php" style="background: #ff6000; color: white; text-decoration: none; padding: 15px; border-radius: 12px; font-weight: bold; display: block;">Sepete Git</a>
            <button onclick="modalKapat()" style="background: #f1f5f9; color: #475569; border: none; padding: 15px; border-radius: 12px; font-weight: bold; cursor: pointer; width: 100%;">Alışverişe Devam Et</button>
        </div>
    </div>
</div>

<script>
let aktifIndex = 0;
const toplamResimSayisi = <?php echo count(array_filter($gorseller)); ?>;

function resimDegistir(yeniResimYolu, element) {
    const anaGorsel = document.getElementById('anaGorsel');
    anaGorsel.style.opacity = '0.5';
    setTimeout(() => {
        anaGorsel.src = yeniResimYolu;
        anaGorsel.style.opacity = '1';
    }, 150);
    const kutular = document.querySelectorAll('.kucuk-resim-kutu');
    kutular.forEach(kutu => kutu.style.borderColor = '#e2e8f0');
    element.style.borderColor = '#ff6000';
    aktifIndex = parseInt(element.getAttribute('data-index'));
}

function galeriKaydir(yon) {
    const slider = document.getElementById('kucukResimSlider');
    const kutular = document.querySelectorAll('.kucuk-resim-kutu');
    if (yon === 'sag') {
        aktifIndex = (aktifIndex < toplamResimSayisi - 1) ? aktifIndex + 1 : 0;
    } else {
        aktifIndex = (aktifIndex > 0) ? aktifIndex - 1 : toplamResimSayisi - 1;
    }
    const hedefKutu = kutular[aktifIndex];
    if (hedefKutu) {
        resimDegistir(hedefKutu.querySelector('img').src, hedefKutu);
        slider.scrollLeft = hedefKutu.offsetLeft - slider.offsetLeft - 80;
    }
}

function sepeteEkleAjax() {
    const urunId = document.getElementById('eklenecek_urun_id').value;
    fetch('sepetim.php?islem=ekle&id=' + urunId)
        .then(() => {
            document.getElementById('sepetModal').style.setProperty('display', 'flex', 'important');
            document.body.style.overflow = 'hidden';
        });
}

function modalKapat() {
    document.getElementById('sepetModal').style.setProperty('display', 'none', 'important');
    document.body.style.overflow = 'auto';
}

window.onclick = function(event) {
    const modal = document.getElementById('sepetModal');
    if (event.target == modal) { modalKapat(); }
}
</script>

<?php include 'footer.php'; ?>
</body>
</html>