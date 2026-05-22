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

// Kullanıcı bilgisi (Session'dan + DB'den teyit)
$stmt = $conn->prepare("SELECT ad, soyad, profil_foto FROM kullanicilar WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// İSTATİSTİKLER
$stmt = $conn->prepare("SELECT COUNT(*) FROM beceriler WHERE kullanici_id = ? AND tur = 'ogret'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($teach_count);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM beceriler WHERE kullanici_id = ? AND tur = 'ogren'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($learn_count);
$stmt->fetch();
$stmt->close();

// Tamamlanan takas sayısı
$takas_count = 0;
$result = $conn->query("SHOW TABLES LIKE 'takas_talepleri'");
if ($result && $result->num_rows > 0) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM takas_talepleri WHERE (gonderen_id = ? OR alici_id = ?) AND durum = 'kabul'");
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($takas_count);
    $stmt->fetch();
    $stmt->close();
}

// Ortalama puan
$avg_puan = 0;
$result = $conn->query("SHOW TABLES LIKE 'degerlendirmeler'");
if ($result && $result->num_rows > 0) {
    $stmt = $conn->prepare("SELECT COALESCE(AVG(puan), 0) FROM degerlendirmeler WHERE alan_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($avg_puan);
    $stmt->fetch();
    $stmt->close();
}

// ==================== ÖNERİLEN EŞLEŞMELER ====================
$stmt = $conn->prepare("
    SELECT DISTINCT k.id, k.ad, k.soyad, k.kullanici_adi, k.profil_foto, 
           b2.beceri_adi, b2.seviye, b2.tur
    FROM beceriler b1
    JOIN beceriler b2 ON b1.beceri_adi = b2.beceri_adi 
    JOIN kullanicilar k ON b2.kullanici_id = k.id
    WHERE b1.kullanici_id = ? 
      AND b1.tur = 'ogren'
      AND b2.tur = 'ogret'
      AND k.id != ?
    ORDER BY RAND() LIMIT 6
");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$matches = $stmt->get_result();

// ==================== BEKLEYEN TAKAS TALEPLERİ ====================
$pending_requests = null;
$result = $conn->query("SHOW TABLES LIKE 'takas_talepleri'");
if ($result && $result->num_rows > 0) {
    $stmt = $conn->prepare("
        SELECT t.id, k.ad, k.soyad, k.profil_foto, t.mesaj, t.olusturma_tarihi
        FROM takas_talepleri t
        JOIN kullanicilar k ON t.gonderen_id = k.id
        WHERE t.alici_id = ? AND t.durum = 'beklemede'
        ORDER BY t.olusturma_tarihi DESC LIMIT 5
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $pending_requests = $stmt->get_result();
}
?>

<div class="dashboard-container">
    <div class="welcome">
        <h1>Hoş geldin, <span><?= htmlspecialchars($user['ad'] . " " . $user['soyad']) ?></span>! </h1>
        <p>Bugün yeni bir beceri takas et veya öğren.</p>
    </div>

    <!-- İstatistikler -->
    <div class="stats">
        <div class="stat-card">
            <i class="fas fa-graduation-cap"></i>
            <h3><?= $teach_count ?></h3>
            <p>Öğretebildiğim Beceri</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-book"></i>
            <h3><?= $learn_count ?></h3>
            <p>Öğrenmek İstediğim</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-exchange-alt"></i>
            <h3><?= $takas_count ?></h3>
            <p>Tamamlanan Takas</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-star"></i>
            <h3><?= $avg_puan > 0 ? number_format($avg_puan, 1) : '—' ?></h3>
            <p>Ortalama Puan</p>
        </div>
    </div>

    <!-- Bekleyen Talepler -->
    <?php if ($pending_requests && $pending_requests->num_rows > 0): ?>
    <div class="section-box">
        <h2><i class="fas fa-bell"></i> Bekleyen Takas Talepleri 
            <span class="badge-count"><?= $pending_requests->num_rows ?></span>
        </h2>
        <div class="requests-list">
            <?php while($req = $pending_requests->fetch_assoc()): ?>
            <div class="request-card" id="req-<?= $req['id'] ?>">
                <div class="request-info">
                    <img src="<?= htmlspecialchars($req['profil_foto'] ?? 'https://ui-avatars.com/api/?name='.urlencode($req['ad'].' '.$req['soyad']).'&background=3498db&color=fff') ?>" 
                         alt="" style="width:50px;height:50px;border-radius:50%;">
                    <div>
                        <h4><?= htmlspecialchars($req['ad'] . ' ' . $req['soyad']) ?></h4>
                        <p><?= htmlspecialchars($req['mesaj'] ?? 'Sana takas teklifi gönderdi.') ?></p>
                        <small><?= date('d.m.Y H:i', strtotime($req['olusturma_tarihi'])) ?></small>
                    </div>
                </div>
                <div class="request-actions">
                    <button class="btn btn-success small" onclick="cevapla(<?= $req['id'] ?>, 'kabul')">Kabul Et</button>
                    <button class="btn btn-danger small" onclick="cevapla(<?= $req['id'] ?>, 'red')">Reddet</button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Önerilen Eşleşmeler -->
    <div class="section-box">
        <h2><i class="fas fa-magic"></i> Önerilen Eşleşmeler</h2>
        <div class="match-grid">
            <?php if ($matches->num_rows > 0): ?>
                <?php while($match = $matches->fetch_assoc()): ?>
                <div class="match-card">
                    <img src="<?= htmlspecialchars($match['profil_foto'] ?? 'https://ui-avatars.com/api/?name='.urlencode($match['ad'].' '.$match['soyad']).'&background=3498db&color=fff') ?>" 
                         alt="" style="width:80px;height:80px;border-radius:50%;">
                    <h4><?= htmlspecialchars($match['ad'] . ' ' . $match['soyad']) ?></h4>
                    <p>@<?= htmlspecialchars($match['kullanici_adi']) ?></p>
                    <span class="badge ogret-badge"><?= htmlspecialchars($match['beceri_adi']) ?></span>
                    <span class="skill-level"><?= $match['seviye'] ?></span>
                    <button class="btn btn-primary small" onclick="takasTeklifi(<?= $match['id'] ?>)">
                        Takas Teklifi Gönder
                    </button>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Henüz eşleşme bulunamadı. Daha fazla beceri ekleyerek şansını artır!</p>
                <a href="beceri-ekle.php" class="btn btn-primary">Beceri Ekle</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Takas Teklifi Modal -->
<div class="modal" id="takasModal" style="display:none;">
    <div class="modal-content">
        <h2>Takas Teklifi Gönder</h2>
        <input type="hidden" id="takas_alici_id">
        <textarea id="takas_mesaj" rows="4" placeholder="Merhaba, ... ile takas yapmak istiyorum..."></textarea>
        <div class="modal-buttons">
            <button class="btn btn-primary" onclick="takasGonder()">Gönder</button>
            <button class="btn btn-secondary" onclick="document.getElementById('takasModal').style.display='none'">İptal</button>
        </div>
    </div>
</div>

<style>
/* Dashboard stilleri (mevcut style.css ile uyumlu) */
.section-box { background:white; border-radius:16px; padding:30px; margin-bottom:30px; box-shadow:0 5px 20px rgba(0,0,0,0.08); }
.match-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
.match-card { background:#f8f9fa; padding:20px; border-radius:12px; text-align:center; }
.badge-count { background:#e74c3c; color:white; padding:3px 9px; border-radius:50%; font-size:0.85rem; }
.modal { position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:2000; align-items:center; justify-content:center; }
.modal-content { background:white; padding:30px; border-radius:16px; width:90%; max-width:480px; }
</style>

<script>
function takasTeklifi(alici_id) {
    document.getElementById('takas_alici_id').value = alici_id;
    document.getElementById('takasModal').style.display = 'flex';
}

function takasGonder() {
    const alici_id = document.getElementById('takas_alici_id').value;
    const mesaj = document.getElementById('takas_mesaj').value.trim();

    if (!mesaj) {
        alert("Mesaj yazmanız gerekiyor!");
        return;
    }

    fetch('takas-gonder.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({alici_id, mesaj})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('✅ Takas teklifi gönderildi!');
            document.getElementById('takasModal').style.display = 'none';
            document.getElementById('takas_mesaj').value = '';
        } else {
            alert('❌ ' + (data.message || 'Bir hata oluştu'));
        }
    });
}

function cevapla(id, durum) {
    fetch('takas-cevap.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id, durum})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('req-' + id).remove();
            alert(durum === 'kabul' ? '✅ Talep kabul edildi!' : '❌ Talep reddedildi.');
        }
    });
}
</script>