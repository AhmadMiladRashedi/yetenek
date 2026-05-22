<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $input = trim($_POST['kullanici_adi']);
    $sifre = $_POST['sifre'];

    $conn = new mysqli("localhost", "root", "", "yetenek");

    if ($conn->connect_error) {
        die("Bağlantı hatası: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, ad, soyad, sifre FROM kullanicilar WHERE (email = ? OR kullanici_adi = ?) AND rol = 'admin'");
    $stmt->bind_param("ss", $input, $input);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $ad, $soyad, $hashed_sifre);
        $stmt->fetch();

        if (password_verify($sifre, $hashed_sifre)) {
            $_SESSION['admin_id'] = $id;
            $_SESSION['admin_ad'] = $ad . " " . $soyad;
            
            header("Location: index.php");
            exit();
        }
    }
    
    echo "<script>alert('Kullanıcı adı veya şifre yanlış!');</script>";
    echo "<script>window.history.back();</script>";
}
?>