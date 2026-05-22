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

// Son mesajlaşılan kişileri getir
$stmt = $conn->prepare("
    SELECT DISTINCT 
        k.id, k.ad, k.soyad, k.profil_foto, k.kullanici_adi,
        m.mesaj, m.olusturma_tarihi,
        (SELECT COUNT(*) FROM mesajlar WHERE alici_id = ? AND gonderen_id = k.id AND okundu = 0) as unread
    FROM mesajlar m
    JOIN kullanicilar k ON (m.gonderen_id = k.id OR m.alici_id = k.id)
    WHERE (m.gonderen_id = ? OR m.alici_id = ?) 
      AND k.id != ?
    ORDER BY m.olusturma_tarihi DESC
    LIMIT 20
");
$stmt->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$contacts = $stmt->get_result();
?>

<div class="chat-container">
    <!-- Sol Sidebar - Kişiler -->
    <div class="chat-sidebar">
        <h3><i class="fas fa-comments"></i> Mesajlar</h3>
        <div class="chat-users" id="chat-users">
            <?php if ($contacts->num_rows > 0): ?>
                <?php while($c = $contacts->fetch_assoc()): ?>
                <div class="chat-user" onclick="openChat(<?= $c['id'] ?>, '<?= htmlspecialchars($c['ad'].' '.$c['soyad']) ?>')">
                    <img src="<?= htmlspecialchars($c['profil_foto'] ?? 'https://ui-avatars.com/api/?name='.urlencode($c['ad'].' '.$c['soyad']).'&background=3498db&color=fff') ?>" alt="">
                    <div>
                        <h4><?= htmlspecialchars($c['ad'] . ' ' . $c['soyad']) ?></h4>
                        <small><?= htmlspecialchars(substr($c['mesaj'] ?? '', 0, 40)) ?>...</small>
                    </div>
                    <?php if($c['unread'] > 0): ?>
                        <span class="unread"><?= $c['unread'] ?></span>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Henüz mesajlaşma yok.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sağ Chat Alanı -->
    <div class="chat-main">
        <div class="chat-header" id="chat-header">
            <h4>Bir sohbet seçin</h4>
        </div>
        
        <div class="chat-messages" id="chat-messages">
            <!-- Mesajlar buraya JavaScript ile yüklenecek -->
            <p class="empty-chat">Sohbet başlamak için sol taraftan bir kişi seçin.</p>
        </div>
        
        <div class="chat-input">
            <input type="text" id="message-input" placeholder="Mesaj yazın..." onkeypress="if(event.key === 'Enter') sendMessage()">
            <button class="btn btn-primary" onclick="sendMessage()">Gönder</button>
        </div>
    </div>
</div>

<script>
// Global değişkenler
let currentChatId = null;
let currentChatName = "";

function openChat(userId, userName) {
    currentChatId = userId;
    currentChatName = userName;
    
    document.getElementById('chat-header').innerHTML = `
        <h4>${userName}</h4>
    `;
    
    loadMessages(userId);
}

function loadMessages(userId) {
    fetch(`mesaj-getir.php?alici_id=${userId}`)
    .then(r => r.text())
    .then(html => {
        document.getElementById('chat-messages').innerHTML = html;
        document.getElementById('chat-messages').scrollTop = 9999;
    });
}

function sendMessage() {
    const input = document.getElementById('message-input');
    const mesaj = input.value.trim();
    
    if (!mesaj || !currentChatId) return;

    fetch('mesaj-gonder.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            alici_id: currentChatId,
            mesaj: mesaj
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            loadMessages(currentChatId);
        }
    });
}

// Her 5 saniyede yeni mesaj kontrolü
setInterval(() => {
    if (currentChatId) {
        loadMessages(currentChatId);
    }
}, 5000);
</script>