<?php
include 'baglan.php';

// Cevap Gönderme İşlemi
if(isset($_POST['cevap_ver'])) {
    $soru_id = $_POST['soru_id'];
    $cevap = trim($_POST['cevap_metni']);
    
    if(!empty($cevap)) {
        $guncelle = $db->prepare("UPDATE urun_sorulari SET cevap_metni = ? WHERE id = ?");
        $guncelle->execute([$cevap, $soru_id]);
        header("Location: soru-cevap.php?durum=ok");
        exit();
    }
}

// Sadece cevap bekleyen (NULL) soruları çekiyoruz
$sorular = $db->query("SELECT s.*, u.urun_adi, m.ad_soyad FROM urun_sorulari s 
                       JOIN urunler u ON s.urun_id = u.id 
                       JOIN uyeler m ON s.uye_id = m.id 
                       WHERE s.cevap_metni IS NULL ORDER BY s.tarih DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Soru Yönetimi - Admin Paneli</title>
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .admin-container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; }
        .card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02); overflow: hidden; }
        
        .q-table { width: 100%; border-collapse: collapse; background: #fff; }
        .q-table th { background: #f8fafc; padding: 15px; text-align: left; font-size: 13px; color: #64748b; border-bottom: 2px solid #edf2f7; }
        .q-table td { padding: 20px 15px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        
        .product-info { font-weight: 700; color: #1e293b; font-size: 14px; margin-bottom: 4px; }
        .user-info { font-size: 12px; color: #94a3b8; display: flex; align-items: center; gap: 5px; }
        
        .question-text { background: #f1f5f9; padding: 12px; border-radius: 8px; border-left: 4px solid #cbd5e1; font-size: 14px; color: #475569; line-height: 1.5; margin: 0; }
        
        .reply-area textarea { width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; font-size: 13px; outline: none; transition: border-color 0.2s; min-height: 80px; resize: vertical; }
        .reply-area textarea:focus { border-color: #ff6000; }
        
        .btn-reply { background: #ff6000; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 10px; font-size: 13px; transition: background 0.2s; float: right; }
        .btn-reply:hover { background: #e65600; }
        
        .badge-count { background: #ff6000; color: white; padding: 2px 8px; border-radius: 20px; font-size: 12px; }
        .status-ok { background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0; font-weight: 500; }
    </style>
</head>
<body>

    <?php include 'admin-nav.php'; ?>

    <div class="admin-container">
        
        <div class="page-header">
            <h2 style="margin:0; color: #1e293b;">💬 Gelen Ürün Soruları <span class="badge-count"><?php echo count($sorular); ?></span></h2>
            <a href="admin.php" style="text-decoration: none; color: #64748b; font-size: 14px;">← Panele Dön</a>
        </div>

        <?php if(isset($_GET['durum']) && $_GET['durum'] == 'ok'): ?>
            <div class="status-ok">✅ Cevap başarıyla gönderildi ve ürün detay sayfasında yayına alındı.</div>
        <?php endif; ?>

        <div class="card">
            <table class="q-table">
                <thead>
                    <tr>
                        <th width="25%">Ürün / Müşteri</th>
                        <th width="40%">Müşteri Sorusu</th>
                        <th width="35%">Cevabınız</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($sorular) > 0): ?>
                        <?php foreach($sorular as $s): ?>
                        <tr>
                            <td>
                                <div class="product-info"><?php echo htmlspecialchars($s['urun_adi']); ?></div>
                                <div class="user-info">
                                    <span>👤</span> <?php echo htmlspecialchars($s['ad_soyad']); ?>
                                </div>
                                <div style="font-size: 11px; color: #cbd5e1; margin-top: 8px;">
                                    📅 <?php echo date("d.m.Y H:i", strtotime($s['tarih'])); ?>
                                </div>
                            </td>
                            <td>
                                <p class="question-text"><?php echo htmlspecialchars($s['soru_metni']); ?></p>
                            </td>
                            <td class="reply-area">
                                <form method="POST">
                                    <input type="hidden" name="soru_id" value="<?php echo $s['id']; ?>">
                                    <textarea name="cevap_metni" placeholder="Müşteriye yanıtınızı buraya yazın..." required></textarea>
                                    <button type="submit" name="cevap_ver" class="btn-reply">Yanıtla ve Yayınla</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 60px; color: #94a3b8;">
                                <div style="font-size: 50px; margin-bottom: 15px;">☕</div>
                                <p style="font-weight: 500;">Tebrikler! Cevaplanmamış soru kalmadı.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>