<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include 'header.php';

$conn = new mysqli("localhost", "root", "2087", "yetenek");
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Gelen Talepler
$stmt = $conn->prepare("
    SELECT t.*, k.ad, k.soyad, k.profil_foto, 
           bg.beceri_adi as gonderen_beceri,
           ba.beceri_adi as alici_beceri
    FROM takas_talepleri t
    JOIN kullanicilar k ON t.gonderen_id = k.id
    JOIN beceriler bg ON t.gonderen_beceri_id = bg.id
    JOIN beceriler ba ON t.alici_beceri_id = ba.id
    WHERE t.alici_id = ? 
    ORDER BY t.olusturma_tarihi DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$gelen_talepler = $stmt->get_result();

// Gönderdiğim Talepler
$stmt = $conn->prepare("
    SELECT t.*, k.ad, k.soyad, k.profil_foto,
           bg.beceri_adi as gonderen_beceri,
           ba.beceri_adi as alici_beceri
    FROM takas_talepleri t
    JOIN kullanicilar k ON t.alici_id = k.id
    JOIN beceriler bg ON t.gonderen_beceri_id = bg.id
    JOIN beceriler ba ON t.alici_beceri_id = ba.id
    WHERE t.gonderen_id = ? 
    ORDER BY t.olusturma_tarihi DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$gonderilen_talepler = $stmt->get_result();
?>

<div class="page-container">
    <h1><i class="fas fa-exchange-alt"></i> Takas Talepleri</h1>

    <h2><i class="fas fa-arrow-down"></i> Gelen Talepler</h2>
    <div class="requests-list" id="gelen-list">
        <?php if ($gelen_talepler->num_rows > 0): ?>
            <?php while($t = $gelen_talepler->fetch_assoc()): ?>
            <div class="request-card" id="talep-<?= $t['id'] ?>">
                <div class="request-info">
                    <img src="<?= htmlspecialchars($t['profil_foto'] ?? 'https://ui-avatars.com/api/?name='.urlencode($t['ad'].' '.$t['soyad']).'&background=3498db&color=fff') ?>" alt="">
                    <div>
                        <h4><?= htmlspecialchars($t['ad'] . ' ' . $t['soyad']) ?></h4>
                        <p><strong>Öğretmek İstiyor:</strong> <?= htmlspecialchars($t['gonderen_beceri']) ?></p>
                        <p><strong>Benden İstiyor:</strong> <?= htmlspecialchars($t['alici_beceri']) ?></p>
                        <?php if($t['mesaj']): ?>
                            <p><strong>Mesaj:</strong> <?= htmlspecialchars($t['mesaj']) ?></p>
                        <?php endif; ?>
                        <small><?= date('d.m.Y H:i', strtotime($t['olusturma_tarihi'])) ?></small>
                    </div>
                </div>
                <?php if($t['durum'] == 'beklemede'): ?>
                <div class="request-actions">
                    <button class="btn btn-success" onclick="cevapla(<?= $t['id'] ?>, 'kabul')">Kabul Et</button>
                    <button class="btn btn-danger" onclick="cevapla(<?= $t['id'] ?>, 'red')">Reddet</button>
                </div>
                <?php else: ?>
                <span class="status-badge <?= $t['durum'] == 'kabul' ? 'success' : 'danger' ?>">
                    <?= $t['durum'] == 'kabul' ? '✅ Kabul Edildi' : '❌ Reddedildi' ?>
                </span>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Henüz gelen takas talebi bulunmuyor.</p>
        <?php endif; ?>
    </div>

    <h2><i class="fas fa-arrow-up"></i> Gönderdiğim Talepler</h2>
    <div class="requests-list">
        <?php if ($gonderilen_talepler->num_rows > 0): ?>
            <?php while($t = $gonderilen_talepler->fetch_assoc()): ?>
            <div class="request-card">
                <div class="request-info">
                    <img src="<?= htmlspecialchars($t['profil_foto'] ?? 'https://ui-avatars.com/api/?name='.urlencode($t['ad'].' '.$t['soyad']).'&background=3498db&color=fff') ?>" alt="">
                    <div>
                        <h4><?= htmlspecialchars($t['ad'] . ' ' . $t['soyad']) ?></h4>
                        <p><strong>Öğreteceğim:</strong> <?= htmlspecialchars($t['gonderen_beceri']) ?></p>
                        <p><strong>Öğrenmek İstediğim:</strong> <?= htmlspecialchars($t['alici_beceri']) ?></p>
                        <span class="status-badge <?= $t['durum'] == 'kabul' ? 'success' : ($t['durum'] == 'red' ? 'danger' : 'waiting') ?>">
                            <?= strtoupper($t['durum']) ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Henüz herhangi bir takas talebi göndermedin.</p>
        <?php endif; ?>
    </div>
</div>

<script>
// Takas Cevaplama
function cevapla(id, durum) {
    if (!confirm(durum === 'kabul' ? 'Talebi kabul etmek istediğinden emin misin?' : 'Talebi reddetmek istediğinden emin misin?')) return;

    fetch('takas-cevap.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, durum: durum })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(durum === 'kabul' ? '✅ Talep kabul edildi!' : '❌ Talep reddedildi.');
            location.reload();
        } else {
            alert('Hata: ' + (data.message || 'İşlem başarısız'));
        }
    });
}
</script>