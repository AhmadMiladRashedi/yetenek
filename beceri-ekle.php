<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'header.php';

$conn = new mysqli("localhost", "root", "2087", "yetenek");  // Şifreni buraya yazdın

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Beceri Ekleme
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $beceri_adi = trim($_POST['beceri_adi']);
    $tur        = $_POST['tur'];
    $seviye     = $_POST['seviye'];
    $aciklama   = trim($_POST['aciklama']);
    $kullanici_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO beceriler (kullanici_id, beceri_adi, tur, seviye, aciklama) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $kullanici_id, $beceri_adi, $tur, $seviye, $aciklama);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Beceri başarıyla eklendi!');</script>";
    } else {
        echo "<script>alert('❌ Hata oluştu!');</script>";
    }
    $stmt->close();
}

// Kullanıcının becerilerini çek
$stmt = $conn->prepare("SELECT * FROM beceriler WHERE kullanici_id = ? ORDER BY olusturma_tarihi DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="page-container">
    <h1>Beceri Ekle</h1>
    
    <div class="form-container">
        <form action="beceri-ekle.php" method="POST">
            <label>Beceri Türü</label>
            <select name="tur" required>
                <option value="">Seçiniz...</option>
                <option value="ogret">Öğretebileceğim Beceri</option>
                <option value="ogren">Öğrenmek İstediğim Beceri</option>
            </select>

            <label>Beceri Adı</label>
            <input type="text" name="beceri_adi" placeholder="Örn: Python, İngilizce, Gitar" required>

            <label>Seviye</label>
            <select name="seviye" required>
                <option value="Baslangic">Başlangıç</option>
                <option value="Orta">Orta</option>
                <option value="Ileri">İleri</option>
                <option value="Uzman">Uzman</option>
            </select>

            <label>Açıklama</label>
            <textarea name="aciklama" rows="4" placeholder="Bu beceri hakkında kısa bilgi..."></textarea>

            <button type="submit" class="btn btn-primary full-width">Beceri Ekle</button>
        </form>
    </div>

    <h2>Eklediğim Beceriler (<?= $result->num_rows ?>)</h2>
    <div class="skills-list">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="skill-item">
                <strong><?= htmlspecialchars($row['beceri_adi']) ?></strong>
                <span class="badge <?= $row['tur']=='ogret' ? 'ogret-badge' : 'ogren-badge' ?>">
                    <?= $row['tur']=='ogret' ? 'Öğreteceğim' : 'Öğrenmek İstiyorum' ?>
                </span>
                <span class="skill-level"><?= $row['seviye'] ?></span>
            </div>
        <?php endwhile; ?>
    </div>
</div>