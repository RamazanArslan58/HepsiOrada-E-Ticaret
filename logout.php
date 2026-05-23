<?php
session_start();
session_destroy(); // Sunucudaki tüm session verilerini tamamen yok ediyoruz
header("Location: login.php");
exit();
?>