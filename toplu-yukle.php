<?php
include 'admin-nav.php'; 
include 'baglan.php'; 

if (isset($_POST['veriyi_isle'])) {
    $json_veri = $_POST['urun_json'];
    $urunler = json_decode($json_veri, true);

    if ($urunler) {
        $basarili = 0;
        foreach ($urunler as $u) {
            $sorgu = $db->prepare("INSERT INTO urunler (urun_adi, urun_aciklama, urun_gorsel, fiyat, eski_fiyat, kategori_id) VALUES (?, ?, ?, ?, ?, ?)");
            $ekle = $sorgu->execute([
                $u['ad'], 
                $u['aciklama'], 
                $u['gorsel'], 
                $u['fiyat'], 
                $u['eski_fiyat'] ?? 0, 
                $u['kategori_id'] ?? 1 // Kategori yoksa varsayılan 1
            ]);
            if ($ekle) $basarili++;
        }
        echo "<script>alert('$basarili ürün başarıyla sisteme eklendi!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Veri formatı hatalı! Lütfen geçerli bir JSON yapıştırın.');</script>";
    }
}
?>

<div style="max-width: 900px;">
    <h2 style="margin-bottom: 10px;">📁 Toplu Ürün Aktarımı (JSON)</h2>
    <p style="color: #64748b; margin-bottom: 25px;">
        Yüzlerce ürünü tek seferde sisteme eklemek için JSON formatındaki veriyi aşağıdaki alana yapıştırın.
    </p>

    <div style="background:#fff; padding:30px; border-radius:12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <form action="" method="POST">
            <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #1e293b;">JSON Verisi:</label>
            <textarea name="urun_json" placeholder='[{"ad":"Ürün 1", "aciklama":"...", "fiyat":100, "gorsel":"1.jpg", "kategori_id":1}, ...]' 
            style="width:100%; height:350px; padding:15px; border:1px solid #e2e8f0; border-radius:8px; font-family: monospace; font-size: 13px; background: #f8fafc; resize: vertical;"></textarea>
            
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px;">
                <button type="submit" name="veriyi_isle" style="padding:15px 40px; background:#ff6000; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:bold; transition: 0.3s;">
                    🚀 VERİLERİ SİSTEME AKTAR
                </button>
                <a href="urun-ekle.php" style="color: #64748b; text-decoration: none; font-size: 14px;">← Manuel Ürün Eklemeye Dön</a>
            </div>
        </form>
    </div>

    <div style="margin-top: 30px; padding: 20px; background: #e0f2fe; border-radius: 8px; border-left: 5px solid #0ea5e9;">
        <h4 style="color: #0369a1; margin-bottom: 10px;">💡 İpucu: JSON Formatı Nasıl Olmalı?</h4>
        <code style="font-size: 12px; color: #0c4a6e;">
            [{"ad": "iPhone 15", "aciklama": "128 GB", "fiyat": 45000, "gorsel": "iphone.jpg", "kategori_id": 1}]
        </code>
    </div>
</div>