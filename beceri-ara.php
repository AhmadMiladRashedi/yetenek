<?php
session_start();
include 'header.php';

$conn = new mysqli("localhost", "root", "2087", "yetenek");

$search = "";
$beceriler = [];

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search = trim($_GET['q']);
    $stmt = $conn->prepare("SELECT b.*, k.ad, k.soyad FROM beceriler b 
                            JOIN kullanicilar k ON b.kullanici_id = k.id 
                            WHERE b.beceri_adi LIKE ? 
                            ORDER BY b.olusturma_tarihi DESC");
    $like = "%$search%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $beceriler = $stmt->get_result();
}
?>

<div class="page-container">
    <h1>Beceri Ara</h1>
    
    <div class="search-bar">
        <form method="GET">
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" 
                   placeholder="Örn: Python, İngilizce, Gitar, Fotoğrafçılık..." required>
            <button type="submit" class="btn btn-primary">Ara</button>
        </form>
    </div>

    <?php if (!empty($search)): ?>
        <h2>"<?= htmlspecialchars($search) ?>" için Sonuçlar</h2>
        <div class="search-results">
            <?php if ($beceriler->num_rows > 0): ?>
                <?php while($row = $beceriler->fetch_assoc()): ?>
                    <div class="result-card">
                        <h4><?= htmlspecialchars($row['beceri_adi']) ?></h4>
                        <p><strong><?= $row['ad'] . ' ' . $row['soyad'] ?></strong></p>
                        <span class="badge"><?= $row['tur']=='ogret' ? 'Öğretebilir' : 'Öğrenmek İstiyor' ?></span>
                        <span class="skill-level"><?= $row['seviye'] ?></span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Bu aramaya uygun beceri bulunamadı.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>