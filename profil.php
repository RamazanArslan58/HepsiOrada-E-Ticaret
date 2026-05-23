<?php
session_start();
include 'baglan.php';

// Kullanıcı giriş yapmadıysa direkt giriş sayfasına fırlat
if (!isset($_SESSION['uye_id'])) {
    header("Location: giris.php");
    exit();
}

$uye_id = $_SESSION['uye_id'];

// Güncel kullanıcı bilgilerini çekiyoruz
$uye_bul = $db->prepare("SELECT * FROM uyeler WHERE id = ?");
$uye_bul->execute([$uye_id]);
$uye = $uye_bul->fetch(PDO::FETCH_ASSOC);

// Aktif sekmeyi URL'den al
$aktif_sayfa = isset($_GET['sayfa']) ? $_GET['sayfa'] : 'bilgilerim';

$mesaj = "";

// --- ŞİFRE GÜNCELLEME ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sifre_guncelle'])) {
    $eskisifre = $_POST['eski_sifre'];
    $yenisifre = $_POST['yeni_sifre'];
    $yenisifre2 = $_POST['yeni_sifre_tekrar'];

    if ($eskisifre != $uye['sifre']) {
        $mesaj = "<div style='background:#f8d7da; color:#721c24; padding:12px; border-radius:6px; margin-bottom:15px; font-size:14px;'>Mevcut şifreniz hatalı.</div>";
    } elseif ($yenisifre != $yenisifre2) {
        $mesaj = "<div style='background:#f8d7da; color:#721c24; padding:12px; border-radius:6px; margin-bottom:15px; font-size:14px;'>Yeni şifreler uyuşmuyor.</div>";
    } else {
        $sifre_update = $db->prepare("UPDATE uyeler SET sifre = ? WHERE id = ?");
        $sonuc = $sifre_update->execute([$yenisifre, $uye_id]);
        if ($sonuc) {
            $mesaj = "<div style='background:#d4edda; color:#155724; padding:12px; border-radius:6px; margin-bottom:15px; font-size:14px;'>Şifreniz başarıyla güncellendi.</div>";
        }
    }
}

// --- PROFİL BİLGİLERİNİ GÜNCELLEME ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bilgileri_guncelle'])) {
    $yeni_ad = trim($_POST['ad_soyad']);
    $yeni_tel = trim($_POST['telefon']);
    $yeni_adres = trim($_POST['adres']);
    
    if (!empty($yeni_ad)) {
        $guncelle = $db->prepare("UPDATE uyeler SET ad_soyad = ?, telefon = ?, adres = ? WHERE id = ?");
        $durum = $guncelle->execute([$yeni_ad, $yeni_tel, $yeni_adres, $uye_id]);
        
        if ($durum) {
            $_SESSION['uye_adi'] = $yeni_ad;
            $mesaj = "<div style='background:#d4edda; color:#155724; padding:12px; border-radius:6px; margin-bottom:15px; font-size:14px; font-weight:500;'>Hesap bilgileriniz başarıyla güncellendi.</div>";
            $uye['ad_soyad'] = $yeni_ad;
            $uye['telefon'] = $yeni_tel;
            $uye['adres'] = $yeni_adres;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hesabım - HepsiOrada</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profil-sidebar { width: 280px; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 15px 0; flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); height: fit-content; }
        .profil-sidebar a { display: block; padding: 14px 25px; color: #475569; text-decoration: none; font-size: 14.5px; font-weight: 600; transition: all 0.2s; border-left: 4px solid transparent; }
        .profil-sidebar a:hover { background: #f8fafc; color: #ff6000; }
        .profil-sidebar a.aktif { background: #fff5ed; color: #ff6000; border-left-color: #ff6000; }

        .form-kontrol { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; outline: none; box-sizing: border-box; transition: border-color 0.2s; margin-bottom: 10px; }
        .form-kontrol:focus { border-color: #ff6000; }
        .btn-guncelle { background: #ff6000; color: #fff; border: none; padding: 12px 25px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px; width: 100%; }
        .btn-sifre { background: #1e293b; color: #fff; border: none; padding: 12px 25px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px; width: 100%; margin-top: 5px; }
    </style>
</head>
<body style="background: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif;">

    <?php include 'nav.php'; ?>

    <main style="max-width: 1200px; margin: 40px auto; padding: 0 15px; display: flex; gap: 30px; align-items: flex-start;">
        
        <aside class="profil-sidebar">
            <div style="padding: 0 25px 15px 25px; border-bottom: 1px solid #f1f5f9; margin-bottom: 10px;">
                <h3 style="margin: 0; font-size: 18px; color: #1e293b;">Hesabım</h3>
            </div>
            <a href="profil.php" class="<?php echo ($aktif_sayfa == 'bilgilerim') ? 'aktif' : ''; ?>">Profil Bilgilerim</a>
            <a href="siparislerim.php">Siparişlerim</a>
            <a href="profil.php?sayfa=begendiklerim" class="<?php echo ($aktif_sayfa == 'begendiklerim') ? 'aktif' : ''; ?>">Beğendiklerim</a>
            <a href="profil.php?sayfa=sorularim" class="<?php echo ($aktif_sayfa == 'sorularim') ? 'aktif' : ''; ?>">Sorularım</a>
            <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 10px 0;">
            <a href="cikis.php" style="color: #dc3545;">Güvenli Çıkış</a>
        </aside>

        <div style="flex: 1; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; min-height: 450px;">
            
            <?php echo $mesaj; ?>

            <?php if ($aktif_sayfa == 'bilgilerim'): ?>
                <div style="display: flex; gap: 40px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 300px;">
                        <h2 style="margin-top: 0; font-size: 18px; color: #1e293b; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">Üyelik Bilgilerim</h2>
                        <form action="" method="POST">
                            <label style="display:block; font-size:13px; font-weight:600; color:#64748b; margin-bottom:5px;">Ad Soyad</label>
                            <input type="text" name="ad_soyad" class="form-kontrol" value="<?php echo htmlspecialchars($uye['ad_soyad']); ?>" required>

                            <label style="display:block; font-size:13px; font-weight:600; color:#64748b; margin-bottom:5px;">E-Posta</label>
                            <input type="email" class="form-kontrol" value="<?php echo htmlspecialchars($uye['email']); ?>" disabled style="background:#f8fafc;">

                            <label style="display:block; font-size:13px; font-weight:600; color:#64748b; margin-bottom:5px;">Telefon</label>
                            <input type="text" name="telefon" class="form-kontrol" value="<?php echo htmlspecialchars($uye['telefon']); ?>">

                            <label style="display:block; font-size:13px; font-weight:600; color:#64748b; margin-bottom:5px;">Adres</label>
                            <textarea name="adres" class="form-kontrol" rows="4" style="resize:none;"><?php echo htmlspecialchars($uye['adres']); ?></textarea>

                            <button type="submit" name="bilgileri_guncelle" class="btn-guncelle">Bilgileri Güncelle</button>
                        </form>
                    </div>
                    <div style="flex: 0.8; min-width: 300px; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <h2 style="margin-top: 0; font-size: 18px; color: #1e293b; margin-bottom: 20px;">Şifre Değiştir</h2>
                        <form action="" method="POST">
                            <label style="display:block; font-size:13px; font-weight:600; color:#64748b; margin-bottom:5px;">Mevcut Şifre</label>
                            <input type="password" name="eski_sifre" class="form-kontrol" required>
                            <label style="display:block; font-size:13px; font-weight:600; color:#64748b; margin-bottom:5px;">Yeni Şifre</label>
                            <input type="password" name="yeni_sifre" class="form-kontrol" required>
                            <label style="display:block; font-size:13px; font-weight:600; color:#64748b; margin-bottom:5px;">Yeni Şifre (Tekrar)</label>
                            <input type="password" name="yeni_sifre_tekrar" class="form-kontrol" required>
                            <button type="submit" name="sifre_guncelle" class="btn-sifre">Şifreyi Yenile</button>
                        </form>
                    </div>
                </div>

            <?php elseif ($aktif_sayfa == 'begendiklerim'): ?>
                <h2 style="margin-top: 0; font-size: 18px; color: #1e293b; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">Beğendiğim Ürünler</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                    <?php
                    $favoriler = $db->prepare("SELECT urunler.* FROM favoriler INNER JOIN urunler ON favoriler.urun_id = urunler.id WHERE favoriler.uye_id = ?");
                    $favoriler->execute([$uye_id]);
                    $fav_listesi = $favoriler->fetchAll(PDO::FETCH_ASSOC);
                    if (count($fav_listesi) > 0):
                        foreach ($fav_listesi as $f):
                            $gorseller = explode(',', $f['urun_gorsel']); 
                            $ilk_gorsel = trim($gorseller[0]);
                    ?>
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; position: relative; background: #fff;">
                            <div style="width: 100%; height: 160px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                                <img src="Gorseller/<?php echo $ilk_gorsel; ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>
                            <h4 style="font-size: 14px; margin: 0 0 8px 0; height: 38px; overflow: hidden; color: #1e293b;"><?php echo htmlspecialchars($f['urun_adi']); ?></h4>
                            <p style="color: #ff6000; font-weight: 800; font-size: 18px; margin: 0 0 12px 0;"><?php echo number_format($f['fiyat'], 2, ',', '.'); ?> TL</p>
                            <a href="urun-detay.php?id=<?php echo $f['id']; ?>" class="btn-urune-git" style="display: block; text-align: center; padding: 10px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: bold; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569;">Ürüne Git</a>
                            <a href="favori-ekle.php?id=<?php echo $f['id']; ?>" style="position: absolute; top: 12px; right: 12px; text-decoration: none; color: #ef4444; font-weight: bold;">✕</a>
                        </div>
                    <?php endforeach; else: ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 40px 0; color: #94a3b8;">
                            <span style="font-size: 40px;">🧡</span>
                            <p style="font-size: 14px; margin-top: 10px;">Henüz hiçbir ürünü beğenmediniz.</p>
                        </div>
                    <?php endif; ?>
                </div>

            <?php elseif ($aktif_sayfa == 'sorularim'): ?>
                <h2 style="margin-top: 0; font-size: 18px; color: #1e293b; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">Satıcılara Sorduğum Sorular</h2>
                <?php
                $soru_cek = $db->prepare("SELECT s.*, u.urun_adi FROM urun_sorulari s JOIN urunler u ON s.urun_id = u.id WHERE s.uye_id = ? ORDER BY s.tarih DESC");
                $soru_cek->execute([$uye_id]);
                $sorular = $soru_cek->fetchAll(PDO::FETCH_ASSOC);
                if (count($sorular) > 0):
                    foreach ($sorular as $s): ?>
                        <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; margin-bottom: 15px; background: #fff;">
                            <div style="font-size: 12px; color: #94a3b8; margin-bottom: 5px; font-weight: 600; text-transform: uppercase;"><?php echo htmlspecialchars($s['urun_adi']); ?></div>
                            <div style="font-weight: 600; color: #1e293b; font-size: 15px;"><span style="color: #ff6000;">Soru:</span> <?php echo htmlspecialchars($s['soru_metni']); ?></div>
                            <?php if (!empty($s['cevap_metni'])): ?>
                                <div style="margin-top: 12px; padding: 12px; background: #f0fdf4; border-left: 4px solid #22c55e; border-radius: 6px;">
                                    <strong style="color: #166534; font-size: 13px; display: block; margin-bottom: 4px;">Satıcı Cevabı:</strong>
                                    <p style="margin: 0; font-size: 14px; color: #1e293b;"><?php echo htmlspecialchars($s['cevap_metni']); ?></p>
                                </div>
                            <?php else: ?>
                                <div style="margin-top: 12px; padding: 10px; background: #fff9f0; border-left: 4px solid #f59e0b; border-radius: 6px; font-size: 13px; color: #b45309;">⏳ Yanıt bekleniyor...</div>
                            <?php endif; ?>
                            <div style="margin-top: 8px; font-size: 11px; color: #94a3b8; text-align: right;"><?php echo date("d.m.Y H:i", strtotime($s['tarih'])); ?></div>
                        </div>
                <?php endforeach; else: ?>
                    <div style="text-align: center; padding: 60px 0; color: #94a3b8;"><p>Henüz bir sorunuz bulunmuyor.</p></div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </main>

    <?php include 'footer.php'; ?>

</body>
</html>