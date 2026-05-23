<?php
try {
     $db = new PDO("mysql:host=localhost;dbname=veritabani_adi;charset=utf8", "kullanici_adi", "sifre");
} catch (PDOException $e) {
     echo "Veritabanı bağlantı hatası: " . $e->getMessage();
}
?>