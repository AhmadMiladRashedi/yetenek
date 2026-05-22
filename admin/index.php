<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login-admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Paneli - Yetenek Takası</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h2><a href="../index.html">Yetenek Takası</a></h2>
            </div>
            <ul class="menu">
                <li><a href="index.php" class="active">Genel Bakış</a></li>
                <li><a href="kullanicilar.php">Kullanıcılar</a></li>
                <li><a href="takas-talepleri.php">Takas Talepleri</a></li>
                <li><a href="sikayetler.php">Şikayetler</a></li>
               <li><a href="logout.php" class="btn-login">Çıkış Yap</a></li>
            </ul>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-content">
            <h1>Hoş geldin, Admin Yönetici!</h1>
            <p>Bu alan sadece yöneticiler içindir. Sistem yönetimi burada yapılacaktır.</p>

            <div class="admin-stats">
                <div class="stat-card">
                    <h3>248</h3>
                    <p>Toplam Kullanıcı</p>
                </div>
                <div class="stat-card">
                    <h3>67</h3>
                    <p>Aktif Takas</p>
                </div>
                <div class="stat-card">
                    <h3>142</h3>
                    <p>Bugünkü Mesaj</p>
                </div>
                <div class="stat-card">
                    <h3>5</h3>
                    <p>Bekleyen Şikayet</p>
                </div>
            </div>

            <h2>Son Kayıt Olan Kullanıcılar</h2>
            <p>Bu kısım yakında veritabanından dinamik olarak çekilecek...</p>
        </div>
    </div>
</body>
</html>