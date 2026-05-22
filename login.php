<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $giris = trim($_POST['kullanici_adi_email']);
    $sifre = $_POST['sifre'];

    $conn = new mysqli("localhost", "root", "2087", "yetenek");

    if ($conn->connect_error) {
        die("Bağlantı hatası: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, ad, soyad, sifre FROM kullanicilar WHERE email = ? OR kullanici_adi = ?");
    $stmt->bind_param("ss", $giris, $giris);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $ad, $soyad, $hashed_sifre);
        $stmt->fetch();

        echo "<h3>Test Bilgileri:</h3>";
        echo "Girilen kullanıcı/e-posta: " . htmlspecialchars($giris) . "<br>";
        echo "Veritabanından alınan hash uzunluğu: " . strlen($hashed_sifre) . " karakter<br><br>";

        if (password_verify($sifre, $hashed_sifre)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['ad'] = $ad;
            $_SESSION['soyad'] = $soyad;

            echo "<script>alert('Giriş başarılı! Dashboard sayfasına yönlendiriliyorsunuz...');</script>";
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Şifre yanlış!');</script>";
            echo "Girilen şifre: " . htmlspecialchars($sifre) . "<br>";
            echo "Hash: " . htmlspecialchars($hashed_sifre);
        }
    } else {
        echo "<script>alert('Bu kullanıcı adı veya e-posta bulunamadı!');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>