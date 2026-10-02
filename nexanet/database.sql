CREATE DATABASE IF NOT EXISTS nexanet_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nexanet_db;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    telepon VARCHAR(20),
    alamat TEXT,
    wilayah_id INT,
    role ENUM('user','admin') DEFAULT 'user',
    avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_wilayah (wilayah_id)
) ENGINE=InnoDB;

CREATE TABLE regions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kota VARCHAR(100) NOT NULL,
    status ENUM('Normal','Pemeliharaan','Gangguan') DEFAULT 'Normal',
    latensi VARCHAR(20),
    pengguna INT DEFAULT 0,
    estimasi_perbaikan VARCHAR(100),
    uptime VARCHAR(10),
    paket VARCHAR(50),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE technicians (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    kode_teknisi VARCHAR(20) UNIQUE NOT NULL,
    rating DECIMAL(2,1) DEFAULT 0.0,
    jumlah_ulasan INT DEFAULT 0,
    telepon VARCHAR(20),
    kendaraan VARCHAR(50),
    INDEX idx_kode (kode_teknisi)
) ENGINE=InnoDB;

CREATE TABLE tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_tiket VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    judul VARCHAR(255) NOT NULL,
    kategori VARCHAR(100),
    prioritas ENUM('Rendah','Sedang','Tinggi') DEFAULT 'Sedang',
    status ENUM('Baru','Dalam Perbaikan','Selesai','Dibatalkan') DEFAULT 'Baru',
    progress INT DEFAULT 0,
    deskripsi TEXT,
    teknisi_id INT,
    eta_tiba VARCHAR(100),
    tanggal_lapor TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (teknisi_id) REFERENCES technicians(id),
    INDEX idx_user (user_id),
    INDEX idx_kode (kode_tiket),
    INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE ticket_steps (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_id INT NOT NULL,
    label VARCHAR(100) NOT NULL,
    status ENUM('pending','aktif','selesai') DEFAULT 'pending',
    waktu VARCHAR(50),
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    INDEX idx_ticket (ticket_id)
) ENGINE=InnoDB;

CREATE TABLE bandwidth_packages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_paket VARCHAR(100) NOT NULL,
    kecepatan VARCHAR(20) NOT NULL,
    harga INT NOT NULL,
    deskripsi TEXT,
    aktif BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE billing_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    bulan VARCHAR(50) NOT NULL,
    paket VARCHAR(100),
    nominal INT NOT NULL,
    status ENUM('Lunas','Belum Tagih','Overdue') DEFAULT 'Belum Tagih',
    tanggal_bayar DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_bulan (user_id, bulan)
) ENGINE=InnoDB;

CREATE TABLE data_usage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    tanggal DATE NOT NULL,
    pemakaian_gb DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_tanggal (user_id, tanggal)
) ENGINE=InnoDB;

CREATE TABLE addons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_addon VARCHAR(20) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    kategori VARCHAR(50),
    harga INT DEFAULT 0,
    aktif BOOLEAN DEFAULT FALSE,
    INDEX idx_aktif (aktif)
) ENGINE=InnoDB;

CREATE TABLE user_addons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    addon_id INT NOT NULL,
    tanggal_aktif DATE,
    tanggal_nonaktif DATE,
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (addon_id) REFERENCES addons(id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

CREATE TABLE maintenance_schedule (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tanggal VARCHAR(50) NOT NULL,
    waktu VARCHAR(100) NOT NULL,
    wilayah VARCHAR(200) NOT NULL,
    jenis_pekerjaan VARCHAR(200),
    status ENUM('Terjadwal','Selesai','Dibatalkan') DEFAULT 'Terjadwal',
    INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    kategori VARCHAR(50),
    ringkasan TEXT,
    konten LONGTEXT,
    tanggal DATE,
    aktif BOOLEAN DEFAULT TRUE,
    views INT DEFAULT 0,
    INDEX idx_slug (slug),
    INDEX idx_aktif (aktif)
) ENGINE=InnoDB;

INSERT INTO users (nama, email, password, telepon, alamat, wilayah_id, role) VALUES
('Budi Santoso', 'budi@nexanet.id', '$2y$12$LK3xJ5xJ5xJ5xJ5xJ5xJ5uGqH3xJ5xJ5xJ5xJ5xJ5xJ5xJ5xJ5xJ5', '081234567890', 'Jl. Sudirman No. 123, Jakarta Selatan', 1, 'user'),
('Admin NexaNet', 'admin@nexanet.id', '$2y$12$LK3xJ5xJ5xJ5xJ5xJ5xJ5uGqH3xJ5xJ5xJ5xJ5xJ5xJ5xJ5xJ5xJ5', '081234567899', 'Kantor Pusat', 1, 'admin');

INSERT INTO regions (kota, status, latensi, pengguna, estimasi_perbaikan, uptime, paket) VALUES
('Jakarta Selatan', 'Normal', '18ms', 12480, '-', '99.8%', '100 Mbps'),
('Bogor', 'Pemeliharaan', '45ms', 3210, '02 Okt 2026, 08:00 WIB', '97.2%', '50 Mbps'),
('Depok', 'Gangguan', '120ms', 8740, '01 Okt 2026, 20:00 WIB', '89.1%', '100 Mbps'),
('Tangerang', 'Normal', '22ms', 6530, '-', '99.5%', '75 Mbps');

INSERT INTO technicians (nama, kode_teknisi, rating, jumlah_ulasan, telepon, kendaraan) VALUES
('Ahmad Suryadi', 'TEK-0421', 4.8, 128, '081234567891', 'Motor - B 1234 XYZ'),
('Siti Rahayu', 'TEK-0422', 4.9, 156, '081234567892', 'Motor - B 5678 ABC'),
('Dedi Kurniawan', 'TEK-0423', 4.7, 98, '081234567893', 'Motor - B 9012 DEF');

INSERT INTO tickets (kode_tiket, user_id, judul, kategori, prioritas, status, progress, deskripsi, teknisi_id, eta_tiba) VALUES
('TCK-88291', 1, 'Gangguan koneksi intermiten di wilayah Depok', 'Gangguan Jaringan', 'Tinggi', 'Dalam Perbaikan', 70, 'Koneksi sering putus-putus sejak kemarin malam, terutama pada jam sibuk.', 1, '01 Okt 2026, 16:30 WIB');

INSERT INTO ticket_steps (ticket_id, label, status, waktu) VALUES
(1, 'Laporan Diterima', 'selesai', '30 Sep, 09:15'),
(1, 'Verifikasi Lapangan', 'selesai', '30 Sep, 11:40'),
(1, 'Teknisi Menuju Lokasi', 'aktif', '01 Okt, 14:00'),
(1, 'Selesai', 'pending', '-');

INSERT INTO bandwidth_packages (nama_paket, kecepatan, harga, deskripsi) VALUES
('Home Starter', '30 Mbps', 299000, 'Cocok untuk 1-3 perangkat, browsing & streaming ringan'),
('Home Business', '100 Mbps', 599000, 'Cocok untuk 4-8 perangkat, WFH & streaming HD'),
('Ultra Home', '200 Mbps', 899000, 'Cocok untuk 9-12 perangkat, gaming & 4K streaming'),
('Enterprise', '500 Mbps', 1499000, 'Cocok untuk 13+ perangkat, kebutuhan bisnis');

INSERT INTO billing_history (user_id, bulan, paket, nominal, status, tanggal_bayar) VALUES
(1, 'Oktober 2026', 'Home Business 100 Mbps', 599000, 'Belum Tagih', NULL),
(1, 'September 2026', 'Home Business 100 Mbps', 599000, 'Lunas', '2026-09-05'),
(1, 'Agustus 2026', 'Home Business 100 Mbps', 599000, 'Lunas', '2026-08-03'),
(1, 'Juli 2026', 'Home Starter 50 Mbps', 399000, 'Lunas', '2026-07-02');

INSERT INTO data_usage (user_id, tanggal, pemakaian_gb) VALUES
(1, '2026-09-25', 4.2), (1, '2026-09-26', 5.8), (1, '2026-09-27', 3.1),
(1, '2026-09-28', 6.4), (1, '2026-09-29', 7.2), (1, '2026-09-30', 9.5), (1, '2026-10-01', 8.1);

INSERT INTO addons (kode_addon, nama, deskripsi, kategori, harga, aktif) VALUES
('ADD-001', 'Speed Boost 24 Jam', 'Tingkatkan kecepatan hingga 2x lipat selama 24 jam untuk kebutuhan mendesak.', 'Performa', 25000, TRUE),
('ADD-002', 'Ganti Password WiFi', 'Ubah password WiFi secara mandiri tanpa perlu menunggu teknisi.', 'Keamanan', 0, TRUE),
('ADD-003', 'Pindah Router', 'Layanan relokasi router ke posisi baru dalam rumah Anda.', 'Instalasi', 150000, FALSE),
('ADD-004', 'Static IP Address', 'Dapatkan IP statis untuk server atau kebutuhan bisnis Anda.', 'Bisnis', 100000, TRUE),
('ADD-005', 'Parental Control', 'Kontrol akses internet untuk perangkat anak-anak di rumah.', 'Keamanan', 35000, FALSE),
('ADD-006', 'Mesh WiFi Extender', 'Tambah jangkauan WiFi dengan perangkat extender mesh.', 'Perangkat', 250000, FALSE);

INSERT INTO user_addons (user_id, addon_id, tanggal_aktif, status) VALUES
(1, 1, '2026-09-15', 'aktif'),
(1, 2, '2026-09-01', 'aktif'),
(1, 4, '2026-08-20', 'aktif');

INSERT INTO maintenance_schedule (tanggal, waktu, wilayah, jenis_pekerjaan, status) VALUES
('02 Okt 2026', '01:00 - 05:00 WIB', 'Bogor - Cimanggis', 'Upgrade Backbone', 'Terjadwal'),
('04 Okt 2026', '23:00 - 03:00 WIB', 'Jakarta Selatan - Kemang', 'Pemeliharaan Rutin', 'Terjadwal'),
('07 Okt 2026', '00:00 - 04:00 WIB', 'Tangerang - BSD', 'Migrasi Fiber', 'Terjadwal');

INSERT INTO announcements (judul, slug, kategori, ringkasan, konten, tanggal, aktif) VALUES
('Jadwal Pemeliharaan Jaringan Berkala - Wilayah DKI Jakarta & Sekitarnya', 'jadwal-pemeliharaan-jaringan-dki', 'Pemeliharaan', 'Pemeliharaan jaringan pada 02 Oktober 2026 pukul 01:00 - 05:00 WIB. Beberapa wilayah mungkin mengalami gangguan sementara.', '<h2>Tentang Pemeliharaan</h2><p>Dalam rangka meningkatkan kualitas layanan, kami akan melakukan pemeliharaan jaringan pada wilayah DKI Jakarta dan sekitarnya.</p><h3>Jadwal Detail</h3><ul><li>Tanggal: 02 Oktober 2026</li><li>Waktu: 01:00 - 05:00 WIB</li><li>Wilayah: Bogor - Cimanggis</li></ul><h3>Dampak</h3><p>Pelanggan di wilayah terdampak mungkin mengalami gangguan koneksi selama periode pemeliharaan. Kami mohon maaf atas ketidaknyamanan ini.</p><h3>Rekomendasi</h3><p>Simpan pekerjaan penting Anda sebelum periode pemeliharaan dimulai. Layanan akan kembali normal setelah pemeliharaan selesai.</p>', '2026-09-29', TRUE),
('Peluncuran Fitur Diagnosis Mandiri Terbaru', 'fitur-diagnosis-mandiri-terbaru', 'Fitur Baru', 'Fitur diagnosis mandiri 3 langkah membantu menyelesaikan masalah koneksi tanpa menunggu teknisi.', '<h2>Fitur Baru: Diagnosis Mandiri</h2><p>Kami dengan bangga mengumumkan peluncuran fitur Diagnosis Mandiri yang memungkinkan Anda menyelesaikan masalah koneksi internet sendiri dalam 3 langkah sederhana.</p><h3>Cara Kerja</h3><ol><li>Pilih jenis kendala yang Anda alami</li><li>Pilih durasi kendala</li><li>Dapatkan rekomendasi otomatis atau buat tiket</li></ol><h3>Keuntungan</h3><ul><li>Hemat waktu tanpa menunggu teknisi</li><li>Tingkat keberhasilan 99.4%</li><li>Rata-rata selesai dalam 3.2 menit</li></ul>', '2026-09-27', TRUE),
('Tips Mengoptimalkan Kecepatan WiFi di Rumah', 'tips-optimalkan-wifi-rumah', 'Tips & Trik', 'Pelajari cara sederhana untuk meningkatkan kecepatan WiFi di rumah Anda tanpa biaya tambahan.', '<h2>5 Tips Optimalkan WiFi</h2><p>Kecepatan WiFi bisa dipengaruhi oleh banyak faktor. Berikut tips sederhana yang bisa Anda terapkan:</p><ol><li><strong>Posisi Router:</strong> Letakkan di tengah rumah, hindari sudut ruangan</li><li><strong>Hindari Interferensi:</strong> Jauhkan dari microwave, telepon nirkabel</li><li><strong>Update Firmware:</strong> Pastikan firmware router selalu terbaru</li><li><strong>Gunakan 5GHz:</strong> Untuk perangkat yang mendukung, gunakan frekuensi 5GHz</li><li><strong>Restart Rutin:</strong> Restart router seminggu sekali untuk performa optimal</li></ol>', '2026-09-25', TRUE);