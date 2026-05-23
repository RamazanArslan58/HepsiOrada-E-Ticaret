<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_giris']) || $_SESSION['admin_giris'] !== true) {
    header("Location: login.php");
    exit();
}

include 'baglan.php'; 

// KATEGORİLERİ ÇEK
$kategoriler = $db->query("SELECT * FROM kategoriler ORDER BY kategori_adi ASC")->fetchAll(PDO::FETCH_ASSOC);

// Ürün Kaydetme İşlemi
if (isset($_POST['urun_kaydet'])) {
    $adi = $_POST['urun_adi'];
    $aciklama = $_POST['urun_aciklama'];
    $gorsel = $_POST['urun_gorsel'];
    $fiyat = $_POST['fiyat'];
    $kat_id = $_POST['kategori_id']; 
    $eski_fiyat = !empty($_POST['eski_fiyat']) ? $_POST['eski_fiyat'] : 0;

    // Sorguya kategori_id eklendi
    $sorgu = $db->prepare("INSERT INTO urunler (urun_adi, urun_aciklama, urun_gorsel, fiyat, eski_fiyat, kategori_id) VALUES (?, ?, ?, ?, ?, ?)");
    $ekle = $sorgu->execute([$adi, $aciklama, $gorsel, $fiyat, $eski_fiyat, $kat_id]);

    if ($ekle) {
        echo "<script>alert('Ürün başarıyla eklendi!'); window.location.href='urun-ekle.php';</script>";
        exit;
    }
}

// Ürünleri Çek
$urunler = $db->query("SELECT urunler.*, kategoriler.kategori_adi 
                       FROM urunler 
                       LEFT JOIN kategoriler ON urunler.kategori_id = kategoriler.id 
                       ORDER BY urunler.id DESC")->fetchAll(PDO::FETCH_ASSOC);

include 'admin-nav.php'; 
?>

<div style="max-width: 1100px;">
    <h2 style="margin-bottom: 25px; color: #1e293b;">📦 Ürün Yönetimi</h2>
    
    <div style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 40px;">
        <h3 style="margin-bottom: 15px; font-size: 16px; color: #64748b;">Yeni Ürün Ekle</h3>
        <form action="" method="POST">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display:block; margin-bottom:8px; font-weight:600;">Ürün Adı</label>
                    <input type="text" name="urun_adi" required style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:8px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:8px; font-weight:600;">Kategori Seçin</label>
                    <select name="kategori_id" required style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:8px; background: white;">
                        <option value="">Seçiniz...</option>
                        <?php foreach($kategoriler as $kat): ?>
                            <option value="<?php echo $kat['id']; ?>"><?php echo $kat['kategori_adi']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display:block; margin-bottom:8px; font-weight:600;">Görsel Dosya Adı</label>
                    <input type="text" name="urun_gorsel" required placeholder="orn.jpg" style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:8px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:8px; font-weight:600;">Satış Fiyatı (TL)</label>
                    <input type="number" name="fiyat" required style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:8px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:8px; font-weight:600;">Eski Fiyat (TL)</label>
                    <input type="number" name="eski_fiyat" style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:8px;">
                </div>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Ürün Açıklaması</label>
                <textarea name="urun_aciklama" required style="width:100%; height:80px; padding:12px; border:1px solid #e2e8f0; border-radius:8px; resize: none;"></textarea>
            </div>

            <button type="submit" name="urun_kaydet" style="width:100%; background:#ff6000; color:#fff; border:none; padding:15px; border-radius:8px; cursor:pointer; font-weight:bold; font-size:16px;">
                ÜRÜNÜ SİSTEME KAYDET
            </button>
        </form>
    </div>

    <h2 style="margin-bottom: 20px;">📋 Kayıtlı Ürünler</h2>
    <div style="background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: #1e293b; color: #fff;">
                    <th style="padding: 15px;">Görsel</th>
                    <th style="padding: 15px;">Ürün Bilgisi</th>
                    <th style="padding: 15px;">Kategori</th>
                    <th style="padding: 15px;">Fiyat</th>
                    <th style="padding: 15px; text-align: center;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($urunler as $u):
                    $resimler = explode(',', $u['urun_gorsel']);
                    $ilkResim = trim($resimler[0]); 
                ?>
                <tr style="border-bottom: 1px solid #eee; transition: 0.3s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 10px;">
                        <img src="Gorseller/<?php echo $ilkResim; ?>" width="60" height="60" style="object-fit:cover; border-radius:8px; border:1px solid #eee;">
                    </td>
                    <td style="padding: 10px;">
                        <div style="font-weight: bold; color: #334155;"><?php echo $u['urun_adi']; ?></div>
                    </td>
                    <td style="padding: 10px;">
                        <span style="background: #f1f5f9; padding: 5px 10px; border-radius: 15px; font-size: 12px; color: #475569;">
                            <?php echo $u['kategori_adi'] ? $u['kategori_adi'] : 'Kategorisiz'; ?>
                        </span>
                    </td>
                    <td style="padding: 10px; color: #ff6000; font-weight: bold;"><?php echo number_format($u['fiyat'], 2, ',', '.'); ?> TL</td>
                    <td style="padding: 10px; text-align: center;">
                        <a href="urun-duzenle.php?id=<?php echo $u['id']; ?>" style="display: inline-block; padding: 8px 15px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-size: 13px;">Düzenle</a>
                        <a href="urun-sil.php?id=<?php echo $u['id']; ?>" onclick="return confirm('Silinsin mi?')" style="display: inline-block; padding: 8px 15px; background: #ef4444; color: white; text-decoration: none; border-radius: 6px; font-size: 13px;">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>