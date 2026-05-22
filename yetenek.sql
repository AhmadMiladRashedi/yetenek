
--
-- `beceriler`
--

CREATE TABLE `beceriler` (
  `id` int NOT NULL,
  `kullanici_id` int NOT NULL,
  `beceri_adi` varchar(100) NOT NULL,
  `tur` enum('ogret','ogren') NOT NULL,
  `seviye` enum('Baslangic','Orta','Ileri','Uzman') DEFAULT 'Baslangic',
  `aciklama` text,
  `olusturma_tarihi` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
--  `beceriler`
--

INSERT INTO `beceriler` (`id`, `kullanici_id`, `beceri_adi`, `tur`, `seviye`, `aciklama`, `olusturma_tarihi`) VALUES
(1, 2, 'Python Programlama', 'ogret', 'Ileri', 'Django, Pandas ve Veri Analizi konusunda deneyimli', '2026-05-14 21:12:22'),
(2, 2, 'Web Geliştirme', 'ogret', 'Ileri', 'PHP, Laravel ve MySQL ile full-stack geliştirme', '2026-05-14 21:12:22'),
(3, 2, 'İngilizce', 'ogren', 'Orta', 'Konuşma İngilizcemi geliştirmek istiyorum', '2026-05-14 21:12:22'),
(4, 2, 'Flutter & Dart', 'ogren', 'Baslangic', 'Mobil uygulama geliştirmeyi öğrenmek istiyorum', '2026-05-14 21:12:22'),
(5, 2, 'Fotoğrafçılık', 'ogret', 'Orta', 'Temel fotoğrafçılık ve Lightroom kullanımı', '2026-05-14 21:12:22'),
(6, 3, 'Web Tasarımı', 'ogret', 'Ileri', 'HTML, CSS, Bootstrap ve Tailwind ile modern arayüz', '2026-05-14 21:12:22'),
(7, 3, 'Gitar Çalma', 'ogret', 'Orta', 'Akustik gitar eğitimi verebilirim', '2026-05-14 21:12:22'),
(8, 3, 'JavaScript', 'ogren', 'Orta', 'React ve modern JavaScript öğrenmek istiyorum', '2026-05-14 21:12:22'),
(9, 3, 'Grafik Tasarım', 'ogren', 'Baslangic', 'Photoshop ve Illustrator öğrenmek istiyorum', '2026-05-14 21:12:22'),
(10, 3, 'Veri Bilimi', 'ogren', 'Baslangic', 'Python ile veri bilimi ve makine öğrenmesi', '2026-05-14 21:12:22'),
(11, 2, 'C++ Programlama', 'ogret', 'Ileri', 'Algoritma ve veri yapıları', '2026-05-14 21:12:22'),
(12, 3, 'Türkçe Öğretimi', 'ogret', 'Uzman', 'Yabancılara Türkçe öğretebilirim', '2026-05-14 21:12:22'),
(13, 2, 'Spor ve Fitness', 'ogret', 'Orta', 'Kişisel antrenman ve beslenme', '2026-05-14 21:12:22'),
(14, 3, 'Müzik Prodüksiyonu', 'ogren', 'Baslangic', 'Ableton Live öğrenmek istiyorum', '2026-05-14 21:12:22'),
(15, 2, 'UI/UX Tasarım', 'ogren', 'Orta', 'Figma ile arayüz tasarımı öğrenmek istiyorum', '2026-05-14 21:12:22'),
(16, 4, 'payton', 'ogren', 'Orta', 'lndsflaj', '2026-05-14 21:13:49');

-- --------------------------------------------------------

--
-- `degerlendirmeler`
--

CREATE TABLE `degerlendirmeler` (
  `id` int NOT NULL,
  `takas_id` int NOT NULL,
  `veren_id` int NOT NULL,
  `alan_id` int NOT NULL,
  `puan` tinyint NOT NULL,
  `yorum` text,
  `olusturma_tarihi` datetime DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
--  `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `id` int NOT NULL,
  `ad` varchar(50) NOT NULL,
  `soyad` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `kullanici_adi` varchar(50) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `sehir` varchar(100) DEFAULT NULL,
  `bio` text,
  `profil_foto` varchar(255) DEFAULT NULL,
  `rol` enum('kullanici','admin') DEFAULT 'kullanici',
  `olusturma_tarihi` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
--  `kullanicilar`
--

INSERT INTO `kullanicilar` (`id`, `ad`, `soyad`, `email`, `kullanici_adi`, `sifre`, `sehir`, `bio`, `profil_foto`, `rol`, `olusturma_tarihi`) VALUES
(1, 'Admin', 'Yönetici', 'admin@yetenek.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'İstanbul', 'Sistem Yöneticisi', NULL, 'admin', '2026-05-14 19:51:31'),
(2, 'Ayşe', 'Yılmaz', 'ayse@test.com', 'ayse123', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ankara', 'Grafik tasarımcı', NULL, 'kullanici', '2026-05-14 19:51:31'),
(3, 'Mehmet', 'Kaya', 'mehmet@test.com', 'mehmet456', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'İzmir', 'Web geliştirici', NULL, 'kullanici', '2026-05-14 19:51:31'),
(4, 'Milad', 'Rashedi', 'milladrashedi@gmail.com', 'Ahmad', '$2y$10$bF6hsvRD0ChfCBDTypdQ7el2jv7xNgNYsg8LFMOAVzmwZNpnK5JEm', NULL, NULL, NULL, 'kullanici', '2026-05-14 20:44:40');

-- --------------------------------------------------------

--
--  `mesajlar`
--

CREATE TABLE `mesajlar` (
  `id` int NOT NULL,
  `gonderen_id` int NOT NULL,
  `alici_id` int NOT NULL,
  `mesaj` text NOT NULL,
  `okundu` tinyint(1) DEFAULT '0',
  `olusturma_tarihi` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- `takas_talepleri`
--

CREATE TABLE `takas_talepleri` (
  `id` int NOT NULL,
  `gonderen_id` int NOT NULL,
  `alici_id` int NOT NULL,
  `gonderen_beceri_id` int NOT NULL,
  `alici_beceri_id` int NOT NULL,
  `durum` enum('beklemede','kabul','red') DEFAULT 'beklemede',
  `mesaj` text,
  `olusturma_tarihi` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beceriler`
--
ALTER TABLE `beceriler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kullanici_id` (`kullanici_id`);

--
-- Indexes for table `degerlendirmeler`
--
ALTER TABLE `degerlendirmeler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `takas_id` (`takas_id`),
  ADD KEY `veren_id` (`veren_id`),
  ADD KEY `alan_id` (`alan_id`);

--
-- Indexes for table `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `kullanici_adi` (`kullanici_adi`);

--
-- Indexes for table `mesajlar`
--
ALTER TABLE `mesajlar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gonderen_id` (`gonderen_id`),
  ADD KEY `alici_id` (`alici_id`);

--
-- Indexes for table `takas_talepleri`
--
ALTER TABLE `takas_talepleri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gonderen_id` (`gonderen_id`),
  ADD KEY `alici_id` (`alici_id`),
  ADD KEY `gonderen_beceri_id` (`gonderen_beceri_id`),
  ADD KEY `alici_beceri_id` (`alici_beceri_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beceriler`
--
ALTER TABLE `beceriler`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `degerlendirmeler`
--
ALTER TABLE `degerlendirmeler`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mesajlar`
--
ALTER TABLE `mesajlar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `takas_talepleri`
--
ALTER TABLE `takas_talepleri`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `beceriler`
--
ALTER TABLE `beceriler`
  ADD CONSTRAINT `beceriler_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `degerlendirmeler`
--
ALTER TABLE `degerlendirmeler`
  ADD CONSTRAINT `degerlendirmeler_ibfk_1` FOREIGN KEY (`takas_id`) REFERENCES `takas_talepleri` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `degerlendirmeler_ibfk_2` FOREIGN KEY (`veren_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `degerlendirmeler_ibfk_3` FOREIGN KEY (`alan_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mesajlar`
--
ALTER TABLE `mesajlar`
  ADD CONSTRAINT `mesajlar_ibfk_1` FOREIGN KEY (`gonderen_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mesajlar_ibfk_2` FOREIGN KEY (`alici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `takas_talepleri`
--
ALTER TABLE `takas_talepleri`
  ADD CONSTRAINT `takas_talepleri_ibfk_1` FOREIGN KEY (`gonderen_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `takas_talepleri_ibfk_2` FOREIGN KEY (`alici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `takas_talepleri_ibfk_3` FOREIGN KEY (`gonderen_beceri_id`) REFERENCES `beceriler` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `takas_talepleri_ibfk_4` FOREIGN KEY (`alici_beceri_id`) REFERENCES `beceriler` (`id`) ON DELETE CASCADE;
COMMIT;



