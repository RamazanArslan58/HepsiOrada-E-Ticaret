<?php
session_start();
include 'baglan.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['uye_id'])) {
    $urun_id = intval($_POST['urun_id']);
    $uye_id = $_SESSION['uye_id'];
    $soru = trim($_POST['soru_metni']);

    if (!empty($soru)) {
        $kaydet = $db->prepare("INSERT INTO urun_sorulari (urun_id, uye_id, soru_metni) VALUES (?, ?, ?)");
        $kaydet->execute([$urun_id, $uye_id, $soru]);
    }
    
    header("Location: urun-detay.php?id=$urun_id&mesaj=soru-alindi");
    exit();
}