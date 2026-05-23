<?php 
session_start(); 
include 'baglan.php'; 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'nav.php'; ?>

    <main style="max-width: 600px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
        <h2 style="color: #333; margin-bottom: 20px; text-align: center;">Bizimle İletişime Geçin</h2>
        
        <form action="mesaj-gonder.php" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
            <input type="text" name="ad_soyad" placeholder="Adınızı ve soyadınızı girin" required style="padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
            <input type="email" name="email" placeholder="E-posta adresinizi girin" required style="padding: 12px; border: 1px solid #ddd; border-radius: 6px;">
            <textarea name="mesaj" placeholder="Mesajınızı buraya yazın..." rows="5" style="padding: 12px; border: 1px solid #ddd; border-radius: 6px; resize: vertical;"></textarea>
            
            <button type="submit" style="background: #ff6000; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                Mesajı Gönder
            </button>
        </form>
    </main>

<?php include 'footer.php'; ?>
    
</body>
</html>