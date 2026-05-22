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
    <title>Kullanıcılar - Yönetici Paneli</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h2><a href="../index.html">Yetenek Takası</a></h2>
            </div>
            <ul class="menu">
                <li><a href="index.php">Genel Bakış</a></li>
                <li><a href="kullanicilar.php" class="active">Kullanıcılar</a></li>
                <li><a href="takas-talepleri.php">Takas Talepleri</a></li>
                <li><a href="../logout.php">Çıkış Yap</a></li>
            </ul>
        </div>
    </nav>

    <div class="admin-content">
        <h1>Kullanıcılar</h1>
        <p>Bu sayfada tüm kullanıcıları görebilir ve yönetebilirsiniz.</p>
        <!-- اینجا بعدا جدول کاربران اضافه می‌شود -->
    </div>
</body>
</html>