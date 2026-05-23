<?php 
include 'admin-nav.php'; 
include 'baglan.php'; 

// Kategori Ekleme İşlemi
if (isset($_POST['kategori_ekle'])) {
    $ad = trim($_POST['kat_adi']);
    if(!empty($ad)){
        $sorgu = $db->prepare("INSERT INTO kategoriler (kategori_adi) VALUES (?)");
        $sorgu->execute([$ad]);
        header("Location: kategoriler.php?durum=eklendi");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kategoriler | Admin</title>
    <style>
        .admin-content { padding: 20px; font-family: sans-serif; }
        .btn-sil { color: #e74c3c; text-decoration: none; font-weight: bold; font-size: 13px; }
        .btn-sil:hover { text-decoration: underline; }
        table tr:hover { background-color: #f9f9f9; }
    </style>
</head>
<body>
<div class="admin-layout">
    <div class="admin-content">
        <h2 style="margin-bottom: 20px;">Kategori Yönetimi</h2>
        
        <div style="display:flex; gap:30px;">
            <div style="flex:1; background:#fff; padding:20px; border-radius:8px; height:fit-content; border: 1px solid #e2e8f0;">
                <h3 style="margin-top:0;">Yeni Kategori</h3>
                <form action="" method="POST">
                    <input type="text" name="kat_adi" placeholder="Örn: Elektronik" required style="width:100%; padding:12px; margin-bottom:15px; border: 1px solid #cbd5e1; border-radius:4px; box-sizing: border-box;">
                    <button name="kategori_ekle" style="width:100%; background:#ff6000; color:#fff; border:none; padding:15px; border-radius:8px; cursor:pointer; font-weight:bold; font-size:16px;">Kategoriyi Ekle</button>
                </form>
            </div>

            <div style="flex:2; background:#fff; padding:20px; border-radius:8px; border: 1px solid #e2e8f0;">
                <h3 style="margin-top:0;">Mevcut Kategoriler</h3>
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc; text-align:left;">
                            <th style="padding:12px; border-bottom:2px solid #e2e8f0;">Kategori Adı</th>
                            <th style="padding:12px; border-bottom:2px solid #e2e8f0; text-align:right;">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $kategoriler = $db->query("SELECT * FROM kategoriler ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
                        foreach($kategoriler as $k):
                        ?>
                        <tr>
                            <td style="padding:12px; border-bottom:1px solid #eee;"><?php echo htmlspecialchars($k['kategori_adi']); ?></td>
                            <td style="text-align:right; border-bottom:1px solid #eee; padding:12px;">
                                <a href="kategori-sil.php?id=<?php echo $k['id']; ?>" class="btn-sil" onclick="return confirm('Bu kategoriyi silerseniz buna bağlı ürünlerde sorun çıkabilir. Emin misiniz?')">Kategoriyi Sil</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>