<?php
// Session sadece bir kez başlatılsın
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yetenek Takası</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h2><a href="index.html">Yetenek Takası</a></h2>
            </div>
            <ul class="menu">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Giriş yapmış kullanıcılar -->
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="profile.php"><i class="fas fa-user"></i> Profil</a></li>
                    <li><a href="beceri-ekle.php"><i class="fas fa-plus"></i> Beceri Ekle</a></li>
                    <li><a href="beceri-ara.php"><i class="fas fa-search"></i> Beceri Ara</a></li>
                    <li><a href="takas-talepleri.php">Takas Talepleri</a></li>
                    <li><a href="mesajlar.php">Mesajlar</a></li>
                    <li><a href="logout.php" class="btn-login">Çıkış Yap</a></li>
                <?php else: ?>
                    <!-- Giriş yapmamış kullanıcılar -->
                    <li><a href="index.html">Ana Sayfa</a></li>
                    <li><a href="nasil-calisir.html">Nasıl Çalışır?</a></li>
                    <li><a href="beceri-ara.php">Beceri Ara</a></li>
                    <li><a href="hakkimizda.html">Hakkımızda</a></li>
                    <li><a href="register.html">Kayıt Ol</a></li>
                    <li><a href="login.html" class="btn-login">Giriş Yap</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>