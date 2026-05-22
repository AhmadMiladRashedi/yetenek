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

// Kullanıcı bilgilerini çek
$stmt = $conn->prepare("SELECT * FROM kullanicilar WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Öğretebileceği ve öğrenmek istediği becerileri ayır
$stmt = $conn->prepare("SELECT * FROM beceriler WHERE kullanici_id = ? ORDER BY olusturma_tarihi DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$beceriler = $stmt->get_result();

$teach_skills = [];
$learn_skills = [];

while ($row = $beceriler->fetch_assoc()) {
    if ($row['tur'] == 'ogret') {
        $teach_skills[] = $row;
    } else {
        $learn_skills[] = $row;
    }
}
?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-pic-container">
            <img src="<?= htmlspecialchars($user['profil_foto'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($user['ad'].' '.$user['soyad']) . '&background=3498db&color=fff&size=150') ?>" 
                 alt="Profil Fotoğrafı" class="profile-pic" id="profileImage">
            <label class="photo-edit-btn" for="photoUpload">
                <i class="fas fa-camera"></i>
            </label>
            <input type="file" id="photoUpload" accept="image/*" style="display:none;">
        </div>
        
        <div class="profile-info">
            <h1><?= htmlspecialchars($user['ad'] . ' ' . $user['soyad']) ?></h1>
            <p>@<?= htmlspecialchars($user['kullanici_adi']) ?> • <?= htmlspecialchars($user['sehir'] ?? 'İstanbul') ?></p>
            <?php if (!empty($user['bio'])): ?>
                <p class="bio-text"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
            <?php endif; ?>
        </div>

        <button class="btn btn-primary" onclick="openEditModal()">
            <i class="fas fa-edit"></i> Profili Düzenle
        </button>
    </div>

    <div class="profile-content">
        <!-- Öğretebileceğim Beceriler -->
        <div class="skills-section">
            <h2><i class="fas fa-arrow-up"></i> Öğretebileceğim Beceriler (<?= count($teach_skills) ?>)</h2>
            <div class="skills-grid" id="teachSkills">
                <?php if (empty($teach_skills)): ?>
                    <p>Henüz öğretebileceğin beceri eklemedin.</p>
                <?php else: ?>
                    <?php foreach($teach_skills as $skill): ?>
                    <div class="skill-card" id="skill-<?= $skill['id'] ?>">
                        <strong><?= htmlspecialchars($skill['beceri_adi']) ?></strong>
                        <span class="skill-level"><?= $skill['seviye'] ?></span>
                        <button class="delete-btn" onclick="deleteSkill(<?= $skill['id'] ?>, this)">✕</button>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button class="btn btn-secondary" onclick="addSkill('ogret')">
                <i class="fas fa-plus"></i> Yeni Beceri Ekle
            </button>
        </div>

        <!-- Öğrenmek İstediğim Beceriler -->
        <div class="skills-section">
            <h2><i class="fas fa-arrow-down"></i> Öğrenmek İstediğim Beceriler (<?= count($learn_skills) ?>)</h2>
            <div class="skills-grid" id="learnSkills">
                <?php if (empty($learn_skills)): ?>
                    <p>Henüz öğrenmek istediğin beceri eklemedin.</p>
                <?php else: ?>
                    <?php foreach($learn_skills as $skill): ?>
                    <div class="skill-card" id="skill-<?= $skill['id'] ?>">
                        <strong><?= htmlspecialchars($skill['beceri_adi']) ?></strong>
                        <span class="skill-level"><?= $skill['seviye'] ?></span>
                        <button class="delete-btn" onclick="deleteSkill(<?= $skill['id'] ?>, this)">✕</button>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button class="btn btn-secondary" onclick="addSkill('ogren')">
                <i class="fas fa-plus"></i> Yeni Beceri Ekle
            </button>
        </div>
    </div>
</div>

<!-- Profil Düzenleme Modal -->
<div class="modal" id="editModal" style="display:none;">
    <div class="modal-content">
        <h2>Profili Düzenle</h2>
        <input type="text" id="editAd" value="<?= htmlspecialchars($user['ad']) ?>" placeholder="Ad">
        <input type="text" id="editSoyad" value="<?= htmlspecialchars($user['soyad']) ?>" placeholder="Soyad">
        <input type="text" id="editSehir" value="<?= htmlspecialchars($user['sehir'] ?? '') ?>" placeholder="Şehir">
        <textarea id="editBio" rows="5" placeholder="Kendin hakkında kısa bilgi..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
        
        <div class="modal-buttons">
            <button class="btn btn-primary" onclick="saveProfile()">Kaydet</button>
            <button class="btn btn-secondary" onclick="closeModal()">İptal</button>
        </div>
    </div>
</div>

<style>
.profile-container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
.profile-header { 
    display: flex; align-items: center; gap: 30px; 
    background: white; padding: 30px; border-radius: 16px; 
    box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-bottom: 30px; 
}
.profile-pic-container { position: relative; }
.profile-pic { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 5px solid #3498db; }
.photo-edit-btn {
    position: absolute; bottom: 10px; right: 10px;
    background: #3498db; color: white; width: 45px; height: 45px;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 1.2rem;
}
.skills-section { background: white; padding: 25px; border-radius: 16px; margin-bottom: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
.skills-grid { display: flex; flex-wrap: wrap; gap: 12px; margin: 15px 0; }
.skill-card {
    background: #f8f9fa; padding: 12px 18px; border-radius: 12px;
    display: flex; align-items: center; gap: 12px; font-size: 1.05rem;
}
.skill-level { background: #3498db; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.9rem; }
.delete-btn { margin-left: auto; color: #e74c3c; background: none; border: none; font-size: 1.3rem; cursor: pointer; }
.modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 2000; align-items: center; justify-content: center; }
.modal-content { background: white; padding: 35px; border-radius: 16px; width: 90%; max-width: 500px; }
</style>

<script>
// Beceri Ekle
function addSkill(tur) {
    const name = prompt("Beceri adını girin:");
    if (!name) return;

    fetch('beceri-ekle.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `beceri_adi=${encodeURIComponent(name)}&tur=${tur}&seviye=Orta`
    })
    .then(() => location.reload());
}

// Beceri Sil
function deleteSkill(id, el) {
    if (!confirm("Bu beceriyi silmek istediğinden emin misin?")) return;

    fetch('beceri-sil.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `delete_id=${id}`
    })
    .then(() => {
        el.parentElement.remove();
    });
}

// Modal
function openEditModal() {
    document.getElementById('editModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

async function saveProfile() {
    const ad = document.getElementById('editAd').value.trim();
    const soyad = document.getElementById('editSoyad').value.trim();
    const sehir = document.getElementById('editSehir').value.trim();
    const bio = document.getElementById('editBio').value.trim();

    const res = await fetch('update_profile.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ad, soyad, sehir, bio})
    });

    const data = await res.json();
    if (data.success) {
        alert('✅ Profil başarıyla güncellendi!');
        location.reload();
    } else {
        alert('❌ Hata: ' + (data.message || 'Bir sorun oluştu'));
    }
}

// Profil Fotoğrafı Yükleme
document.getElementById('photoUpload').addEventListener('change', async function(e) {
    if (!e.target.files[0]) return;

    const formData = new FormData();
    formData.append('photo', e.target.files[0]);

    const res = await fetch('upload_photo.php', {
        method: 'POST',
        body: formData
    });

    const data = await res.json();
    if (data.success) {
        document.getElementById('profileImage').src = data.url + '?t=' + new Date().getTime();
        alert('✅ Profil fotoğrafı güncellendi!');
    } else {
        alert('❌ ' + (data.message || 'Fotoğraf yüklenemedi'));
    }
});
</script>