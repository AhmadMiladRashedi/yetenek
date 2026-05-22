<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad = trim($_POST['ad']);
    $soyad = trim($_POST['soyad']);
    $email = trim($_POST['email']);
    $kullanici_adi = trim($_POST['kullanici_adi']);
    $sifre = $_POST['sifre'];
    $sifre_tekrar = $_POST['sifre_tekrar'];

    // بررسی خالی نبودن فیلدها
    if (empty($ad) || empty($soyad) || empty($email) || empty($kullanici_adi) || empty($sifre)) {
        echo "<script>alert('همه فیلدها را پر کنید!'); window.history.back();</script>";
        exit;
    }

    // بررسی یکسان بودن رمز
    if ($sifre !== $sifre_tekrar) {
        echo "<script>alert('Şifreler aynı değildir.!'); window.history.back();</script>";
        exit;
    }

     $conn = new mysqli("localhost", "root", "2087", "yetenek");

    if ($conn->connect_error) {
        die("اتصال برقرار نشد: " . $conn->connect_error);
    }

    // چک کردن ایمیل و یوزرنیم تکراری
    $stmt = $conn->prepare("SELECT id FROM kullanicilar WHERE email = ? OR kullanici_adi = ?");
    $stmt->bind_param("ss", $email, $kullanici_adi);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Bu e-posta adresi veya kullanıcı adı zaten kayıtlı!'); window.history.back();</script>";
    } else {
        // رمز را هش کن
        $sifre_hash = password_hash($sifre, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO kullanicilar (ad, soyad, email, kullanici_adi, sifre) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $ad, $soyad, $email, $kullanici_adi, $sifre_hash);

        if ($stmt->execute()) {
            echo "<script>alert('Kayıt başarılı!'); window.location.href='login.html';</script>";
        } else {
            echo "<script>alert('Kayıt hatası!');</script>";
        }
    }

    $stmt->close();
    $conn->close();
}
?>