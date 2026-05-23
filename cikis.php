<?php
session_start();
// Üyeye ait tüm session verilerini siliyoruz
unset($_SESSION['uye_id']);
unset($_SESSION['uye_ad']);

// Kullanıcıyı anasayfaya geri gönderiyoruz
header("Location: index.php");
exit();
?>