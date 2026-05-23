<?php
$host = "localhost";
$veritabani = "eticaret";
$kullanici = "root";
$sifre = "";
try 
{
    $db = new PDO("mysql:host=$host;dbname=$veritabani;charset=utf8", $kullanici, $sifre);
} 
catch (PDOException $e) 
{
    echo "Veritabanı bağlantı hatası: " . $e->getMessage();
    die();
}
?>