<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Girişi - Yetenek Takası</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-container">
            <h2>Yönetici Girişi</h2>
            <p>Bu alan sadece yöneticiler içindir.</p>
            
            <form action="login-admin-process.php" method="POST">
                <input type="text" name="kullanici_adi" placeholder="Kullanıcı adı veya Email" required>
                <input type="password" name="sifre" placeholder="Şifre" required>
                <button type="submit" class="btn btn-primary full-width">Giriş Yap</button>
            </form>
            
            <p><a href="../login.html">← Kullanıcı Girişine Dön</a></p>
        </div>
    </div>
</body>
</html>