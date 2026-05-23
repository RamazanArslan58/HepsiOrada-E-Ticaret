<?php 
session_start(); 
include 'baglan.php'; 

if (isset($_POST['giris_yap'])) {
    $kadi = $_POST['kadi'];
    $sifre = $_POST['sifre'];

    // Veritabanında bu kullanıcı adı ve şifreye ait biri var mı bakıyoruz
    $sorgu = $db->prepare("SELECT * FROM kullanicilar WHERE kullanici_adi = ? AND sifre = ?");
    $sorgu->execute([$kadi, $sifre]);
    $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

    // Eğer eşleşen bir kullanıcı bulunduysa tarayıcının hafızasına Bu kişi giriş yaptı diye not düşüyoruz
    if ($kullanici) {

        $_SESSION['admin_giris'] = true;
        $_SESSION['admin_kadi'] = $kullanici['kullanici_adi'];
        
        header("Location: admin.php");
        exit();
    } else {
        echo "<script>alert('Kullanıcı adı veya şifre hatalı!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Giriş</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main style="max-width: 400px; margin: 100px auto; padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 8px;">
        <h2>Admin Giriş Paneli</h2>
        <br>
        <form action="login.php" method="POST">
            <label>Kullanıcı Adı:</label><br>
            <input type="text" name="kadi" required style="width:100%; padding:8px; margin-bottom:15px;"><br>
            
            <label>Şifre:</label><br>
            <input type="password" name="sifre" required style="width:100%; padding:8px; margin-bottom:15px;"><br>
            
            <button type="submit" name="giris_yap" style="padding: 10px 20px; background: #ff6000; color: white; border: none; cursor: pointer; width: 100%;">Giriş Yap</button>
        </form>
    </main>
</body>
</html>