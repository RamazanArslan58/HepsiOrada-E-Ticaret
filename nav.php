<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'baglan.php';

// --- YENİ YETKİ KONTROLÜ ---
if (isset($_SESSION['uye_id'])) {
    // Veritabanından kullanıcının en güncel yetki bilgisini çekelim
    $yetki_sorgu = $db->prepare("SELECT uye_yetki FROM uyeler WHERE id = ?");
    $yetki_sorgu->execute([$_SESSION['uye_id']]);
    $kullanici_verisi = $yetki_sorgu->fetch(PDO::FETCH_ASSOC);

    if ($kullanici_verisi && $kullanici_verisi['uye_yetki'] == 1) {
        $_SESSION['admin_giris'] = true; // Yetkisi 1 ise admin
    } else {
        $_SESSION['admin_giris'] = false; // Yetkisi 0 ise admin değil
    }
}

$kategori_sorgu = $db->query("SELECT * FROM kategoriler ORDER BY kategori_adi ASC");
$kategoriler = $kategori_sorgu->fetchAll(PDO::FETCH_ASSOC);

// Sepetteki ürün sayısını dinamik hesapla
$sepet_adet = 0;
if (isset($_SESSION['sepet']) && !empty($_SESSION['sepet'])) {
    foreach ($_SESSION['sepet'] as $urun_id => $detay) {
        if (is_array($detay) && isset($detay['adet'])) {
            $sepet_adet += $detay['adet'];
        } else if (is_numeric($detay)) {
            $sepet_adet += $detay;
        } else {
            $sepet_adet++;
        }
    }
}

$su_anki_sayfa = basename($_SERVER['PHP_SELF']);
$anasayfada_mi = ($su_anki_sayfa == 'index.php');
?>

<div style="background: #6A0DAD; color: white; text-align: center; padding: 10px 0; font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif; font-size: 12px; font-weight: bold; letter-spacing: 0.5px;">
    ✨ <span style="color: #FFD700;">PREMIUM</span> ile kargo bedava ve her alışverişte %3 nakit iade kazan! 
    <a href="#" style="color: white; text-decoration: underline; margin-left: 8px;">Hemen Katıl ></a>
</div>

<header style="background: #ffffff; padding: 20px 0; font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 15px; display: flex; align-items: center; justify-content: space-between; gap: 40px;">
        
        <div style="flex: 0 0 auto;">
            <a href="index.php" style="text-decoration: none; display: flex; align-items: center;" title="Anasayfaya Git">
                <img src="logo.png" alt="HepsiOrada Logo" style="height: 42px; max-width: 180px; object-fit: contain;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <span style="display: none; font-size: 26px; font-weight: 800; color: #ff6000; letter-spacing: -1px;">Hepsi<span style="color:#0f172a">Orada</span></span>
            </a>
        </div>

        <div style="flex: 1; max-width: 650px;">
            <form action="urunler.php" method="GET" style="display: flex; width: 100%; position: relative;">
                <div style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 16px; pointer-events: none;">🔍</div>
                <input type="text" name="ara" placeholder="Ürün, marka veya kategori ara..." style="width: 100%; padding: 14px 20px 14px 45px; border: 2px solid #ff6000; border-radius: 8px; font-size: 14px; outline: none; font-weight: 500; background: #f8fafc; transition: background 0.2s;" onfocus="this.style.background='#ffffff'" onblur="this.style.background='#f8fafc'">
                <button type="submit" style="background: #ff6000; color: white; border: none; padding: 0 30px; font-size: 15px; font-weight: 700; border-radius: 0 6px 6px 0; cursor: pointer; position: absolute; right: 2px; top: 2px; bottom: 2px; transition: background 0.15s;" onmouseover="this.style.background='#e65600';" onmouseout="this.style.background='#ff6000';">Ara</button>
            </form>
        </div>

        <div style="display: flex; align-items: center; gap: 20px; flex: 0 0 auto;">
            
            <?php if (isset($_SESSION['uye_id'])): ?>
                <div style="position: relative; display: inline-block;" id="kullaniciMenuKonteyner">
                    <button onclick="kullaniciMenuAcKapat()" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 0 18px; border-radius: 8px; color: #334155; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; height: 48px; min-width: 140px;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                        👤 <span style="max-width: 90px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($_SESSION['uye_adi'] ?? 'Hesabım'); ?></span> <span style="font-size: 9px; color: #94a3b8;">▼</span>
                    </button>
                    <div id="kullaniciDropdown" style="display: none; position: absolute; top: 115%; right: 0; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); padding: 5px 0; z-index: 1000; width: 190px;">
                        <?php if (isset($_SESSION['admin_giris']) && $_SESSION['admin_giris'] === true): ?>
                            <a href="admin.php" style="display: block; padding: 11px 18px; color: #ff6000; text-decoration: none; font-size: 13.5px; font-weight: 800; background: #fff5ed;" onmouseover="this.style.background='#ffe0cc'" onmouseout="this.style.background='#fff5ed'">
                                ⚙️ Admin Paneli
                            </a>
                            <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 5px 0;">
                        <?php endif; ?>

                        <a href="profil.php?sayfa=bilgilerim" style="display: block; padding: 11px 18px; color: #475569; text-decoration: none; font-size: 13.5px; font-weight: 600;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">Profil Bilgilerim</a>
                        
                        <a href="siparislerim.php" style="display: block; padding: 11px 18px; color: #475569; text-decoration: none; font-size: 13.5px; font-weight: 600;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">Siparişlerim</a>
                        
                        <a href="profil.php?sayfa=begendiklerim" style="display: block; padding: 11px 18px; color: #475569; text-decoration: none; font-size: 13.5px; font-weight: 600;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">Beğendiklerim</a>

                        <a href="profil.php?sayfa=sorularim" style="display: block; padding: 11px 18px; color: #475569; text-decoration: none; font-size: 13.5px; font-weight: 600;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">Sorularım</a>
                        
                        <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 5px 0;">
                        <a href="cikis.php" style="display: block; padding: 11px 18px; color: #dc3545; text-decoration: none; font-size: 13.5px; font-weight: 600;" onmouseover="this.style.background='#fdf2f2'" onmouseout="this.style.background='transparent'">Güvenli Çıkış</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="giris.php" style="text-decoration: none; background: #ffffff; border: 1px solid #cbd5e1; padding: 0 20px; border-radius: 8px; color: #334155; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; height: 48px; box-sizing: border-box;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                    👤 Giriş Yap
                </a>
            <?php endif; ?>

            <a href="sepetim.php" style="text-decoration: none; background: #fff0e6; border: 1px solid #ffd0b3; padding: 0 22px; border-radius: 8px; color: #ff6000; font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 10px; transition: background 0.15s; height: 48px; box-sizing: border-box;" onmouseover="this.style.background='#ffe0cc'" onmouseout="this.style.background='#fff0e6'">
                🛒 Sepetim 
                <span style="background: #ff6000; color: white; font-size: 12px; padding: 3px 9px; border-radius: 20px; font-weight: bold;"><?php echo $sepet_adet; ?></span>
            </a>

        </div>
    </div>
</header>

<div style="background: #ffffff; border-top: 1px solid #f1f3f5; border-bottom: 1px solid #e2e8f0; font-family: -apple-system, BlinkMacSystemFont, Arial, sans-serif;">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; height: 46px; padding: 0 15px; gap: 20px;">
        
        <?php if (!$anasayfada_mi): ?>
            <div style="position: relative; display: inline-block; margin-right: 10px;" id="kategoriMenuKonteyner">
                <button onclick="kategoriMenuAcKapat()" style="background: transparent; color: #ff6000; border: none; padding: 0; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; height: 46px;">
                    <span style="font-size: 16px;">☰</span> Kategoriler
                </button>
                
                <div id="kategoriDropdown" style="display: none; position: absolute; top: 100%; left: 0; width: 250px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 0 0 6px 6px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); padding: 5px 0; z-index: 1000;">
                    <?php if (count($kategoriler) > 0): ?>
                        <?php foreach ($kategoriler as $kat): ?>
                            <a href="urunler.php?kategori=<?php echo $kat['id']; ?>" style="display: block; padding: 12px 18px; color: #334155; text-decoration: none; font-size: 13.5px; font-weight: 600; border-bottom: 1px solid #f8fafc; transition: all 0.1s;" onmouseover="this.style.background='#fff5ed'; this.style.color='#ff6000';" onmouseout="this.style.background='transparent'; this.style.color='#334155';">
                                <?php echo htmlspecialchars($kat['kategori_adi']); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span style="display: block; padding: 12px 20px; color: #94a3b8; font-size: 13px;">Henüz kategori eklenmedi.</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 30px; align-items: center;">
            <a href="urunler.php" style="color: #ff6000; text-decoration: none; font-size: 13.5px; font-weight: 700;">Günün Fırsatları</a>
            <a href="urunler.php?kategori=2" style="color: #475569; text-decoration: none; font-size: 13.5px; font-weight: 500; transition: color 0.15s;" onmouseover="this.style.color='#ff6000'" onmouseout="this.style.color='#475569'">Bilgisayar / Donanım</a>
            <a href="urunler.php?kategori=1" style="color: #475569; text-decoration: none; font-size: 13.5px; font-weight: 500; transition: color 0.15s;" onmouseover="this.style.color='#ff6000'" onmouseout="this.style.color='#475569'">Akıllı Telefonlar</a>
            <a href="#" style="color: #475569; text-decoration: none; font-size: 13.5px; font-weight: 500; transition: color 0.15s;" onmouseover="this.style.color='#ff6000'" onmouseout="this.style.color='#475569'">Kampanyalar</a>
        </div>
        
        <div style="margin-left: auto; display: flex; gap: 20px; font-size: 12.5px; color: #64748b; font-weight: 500;">
            <span style="color: #ff6000; font-weight: 700; cursor: pointer;">HepsiOrada Premium</span>
            <a href="#" style="color: inherit; text-decoration: none;">Müşteri Hizmetleri</a>
        </div>
    </div>
</div>

<script>
function kategoriMenuAcKapat() {
    var dp = document.getElementById("kategoriDropdown");
    if(!dp) return;
    dp.style.display = (dp.style.display === "none" || dp.style.display === "") ? "block" : "none";
    var userDp = document.getElementById("kullaniciDropdown");
    if(userDp) userDp.style.display = "none";
}

function kullaniciMenuAcKapat() {
    var dp = document.getElementById("kullaniciDropdown");
    if(dp) {
        dp.style.display = (dp.style.display === "none" || dp.style.display === "") ? "block" : "none";
    }
    var katDp = document.getElementById("kategoriDropdown");
    if(katDp) katDp.style.display = "none";
}

window.onclick = function(event) {
    var katKonteyner = document.getElementById('kategoriMenuKonteyner');
    var katDropdown = document.getElementById("kategoriDropdown");
    if (katKonteyner && katDropdown && !katKonteyner.contains(event.target)) {
        katDropdown.style.display = "none";
    }
    var userKonteyner = document.getElementById('kullaniciMenuKonteyner');
    var userDropdown = document.getElementById("kullaniciDropdown");
    if (userKonteyner && userDropdown && !userKonteyner.contains(event.target)) {
        userDropdown.style.display = "none";
    }
}
</script>