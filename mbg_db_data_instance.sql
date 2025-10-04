-- ==============================
-- DATA USER (10 orang, 5 gudang, 5 dapur)
-- ==============================
INSERT INTO `user` (id, name, email, password, role, created_at) VALUES
-- Password: pass123; Hashed Password (md5): 32250170a0dca92d53ec9624f336ca24
(1, 'Budi Santoso', 'budi.gudang@mbg.id', '32250170a0dca92d53ec9624f336ca24', 'gudang', '2025-09-01 08:00:00'),
(2, 'Siti Aminah', 'siti.gudang@mbg.id', '32250170a0dca92d53ec9624f336ca24', 'gudang', '2025-09-01 08:05:00'),
(3, 'Rahmat Hidayat', 'rahmat.gudang@mbg.id', '32250170a0dca92d53ec9624f336ca24', 'gudang', '2025-09-01 08:10:00'),
(4, 'Lina Marlina', 'lina.gudang@mbg.id', '32250170a0dca92d53ec9624f336ca24', 'gudang', '2025-09-01 08:15:00'),
(5, 'Anton Saputra', 'anton.gudang@mbg.id', '32250170a0dca92d53ec9624f336ca24', 'gudang', '2025-09-01 08:20:00'),
(6, 'Dewi Lestari', 'dewi.dapur@mbg.id', '32250170a0dca92d53ec9624f336ca24', 'dapur', '2025-09-01 08:30:00'),
(7, 'Andi Pratama', 'andi.dapur@mbg.id', '32250170a0dca92d53ec9624f336ca24', 'dapur', '2025-09-01 08:35:00'),
(8, 'Maria Ulfa', 'maria.dapur@mbg.id', '32250170a0dca92d53ec9624f336ca24', 'dapur', '2025-09-01 08:40:00'),
(9, 'Surya Kurnia', 'surya.dapur@mbg.id', '32250170a0dca92d53ec9624f336ca24', 'dapur', '2025-09-01 08:45:00'),
(10, 'Yanti Fitri', 'yanti.dapur@mbg.id', '32250170a0dca92d53ec9624f336ca24', 'dapur', '2025-09-01 08:50:00');

-- ==============================
-- DATA BAHAN BAKU (10 item stok aktual MBG)
-- ==============================
INSERT INTO `bahan_baku` (id, nama, kategori, jumlah, satuan, tanggal_masuk, tanggal_kadaluarsa, status, created_at) VALUES
(1, 'Beras Medium', 'Karbohidrat', 500, 'kg', '2025-09-01', '2026-03-01', 'tersedia', '2025-09-01 09:00:00'),
(2, 'Telur Ayam', 'Protein Hewani', 2000, 'butir', '2025-09-20', '2025-10-10', 'tersedia', '2025-09-20 09:05:00'),
(3, 'Daging Ayam Broiler', 'Protein Hewani', 300, 'kg', '2025-09-22', '2025-10-02', 'segera_kadaluarsa', '2025-09-22 09:10:00'),
(4, 'Tahu Putih', 'Protein Nabati', 400, 'potong', '2025-09-28', '2025-10-01', 'kadaluarsa', '2025-09-28 09:15:00'),
(5, 'Tempe', 'Protein Nabati', 350, 'potong', '2025-09-27', '2025-10-03', 'segera_kadaluarsa', '2025-09-27 09:20:00'),
(6, 'Sayur Bayam', 'Sayuran', 150, 'ikat', '2025-09-29', '2025-10-01', 'segera_kadaluarsa', '2025-09-29 09:25:00'),
(7, 'Wortel', 'Sayuran', 100, 'kg', '2025-09-21', '2025-10-15', 'tersedia', '2025-09-21 09:30:00'),
(8, 'Kentang', 'Karbohidrat', 120, 'kg', '2025-09-23', '2025-11-20', 'tersedia', '2025-09-23 09:35:00'),
(9, 'Minyak Goreng Sawit', 'Bahan Masak', 80, 'liter', '2025-09-15', '2026-01-01', 'tersedia', '2025-09-15 09:40:00'),
(10, 'Susu Bubuk Fortifikasi', 'Protein Hewani', 50, 'kg', '2025-09-10', '2025-12-10', 'tersedia', '2025-09-10 09:45:00');

-- ==============================
-- DATA PERMINTAAN (10 permintaan dapur)
-- ==============================
INSERT INTO `permintaan` (id, pemohon_id, tgl_masak, menu_makan, jumlah_porsi, status, created_at) VALUES
(1, 6, '2025-09-30', 'Nasi ayam goreng + sayur bayam', 200, 'disetujui', '2025-09-28 10:00:00'),
(2, 7, '2025-09-30', 'Tempe goreng + sayur wortel', 150, 'disetujui', '2025-09-28 10:05:00'),
(3, 8, '2025-10-01', 'Nasi + ayam rebus + bayam bening', 180, 'menunggu', '2025-09-29 10:10:00'),
(4, 9, '2025-10-01', 'Kentang balado + telur rebus', 120, 'disetujui', '2025-09-29 10:15:00'),
(5, 10, '2025-10-02', 'Nasi tempe orek + sayur sop', 200, 'menunggu', '2025-09-29 10:20:00'),
(6, 6, '2025-10-02', 'Ayam goreng tepung + wortel kukus', 220, 'ditolak', '2025-09-29 10:25:00'),
(7, 7, '2025-10-03', 'Nasi telur dadar + bayam bening', 180, 'menunggu', '2025-09-30 10:30:00'),
(8, 8, '2025-10-03', 'Kentang rebus + ayam suwir', 160, 'menunggu', '2025-09-30 10:35:00'),
(9, 9, '2025-10-04', 'Nasi + tempe goreng + sayur bening', 190, 'menunggu', '2025-09-30 10:40:00'),
(10, 10, '2025-10-04', 'Sup ayam + susu fortifikasi', 210, 'menunggu', '2025-09-30 10:45:00');

-- ==============================
-- DATA PERMINTAAN DETAIL (contoh relasi bahan & jumlah)
-- ==============================
INSERT INTO `permintaan_detail` (id, permintaan_id, bahan_id, jumlah_diminta) VALUES
(1, 1, 1, 50),   -- Beras
(2, 1, 3, 40),   -- Ayam
(3, 1, 6, 50),   -- Bayam
(4, 2, 1, 40),
(5, 2, 5, 30),
(6, 2, 7, 20),
(7, 3, 1, 45),
(8, 3, 3, 30),
(9, 3, 6, 40),
(10, 4, 1, 30),
(11, 4, 8, 20),
(12, 4, 2, 60),
(13, 5, 1, 60),
(14, 5, 5, 30),
(15, 5, 7, 20),
(16, 6, 1, 50),
(17, 6, 3, 50),
(18, 7, 1, 40),
(19, 7, 2, 40),
(20, 7, 6, 30),
(21, 8, 1, 35),
(22, 8, 8, 25),
(23, 8, 3, 20),
(24, 9, 1, 45),
(25, 9, 5, 25),
(26, 9, 6, 30),
(27, 10, 1, 60),
(28, 10, 3, 50),
(29, 10, 10, 10);
