CREATE TABLE `user` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(100) UNIQUE NOT NULL,
  `email` varchar(150) UNIQUE NOT NULL,
  `password` varchar(255) COMMENT 'Hashed Password',
  `role` ENUM ('gudang', 'dapur'),
  `created_at` datetime COMMENT 'Waktu Dibuat'
);

CREATE TABLE `bahan_baku` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `nama` varchar(120) COMMENT 'Nama Bahan',
  `kategori` varchar(60) COMMENT 'Kategori Bahan',
  `jumlah` int(4) COMMENT 'Stok Tersedia',
  `satuan` varchar(20) COMMENT 'Satuan Bahan',
  `tanggal_masuk` date,
  `tanggal_kadaluarsa` date,
  `status` ENUM ('tersedia', 'segera_kadaluarsa', 'kadaluarsa', 'habis'),
  `created_at` datetime COMMENT 'Waktu Dibuat'
);

CREATE TABLE `permintaan` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `pemohon_id` int(11) COMMENT 'Relasi ke tabel user (role = dapur)',
  `tgl_masak` date COMMENT 'Tanggal rencana memasak',
  `menu_makan` varchar(255) COMMENT 'Deskripsi Menu',
  `jumlah_porsi` int(4),
  `status` ENUM ('menunggu', 'disetujui', 'ditolak') COMMENT 'Status Permintaan',
  `created_at` datetime COMMENT 'Waktu Dibuat'
);

CREATE TABLE `permintaan_detail` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `permintaan_id` int(11) COMMENT 'Relasi ke tabel permintaan',
  `bahan_id` int(11) COMMENT 'Relasi ke tabel bahan_baku',
  `jumlah_diminta` int(4) COMMENT 'Jumlah bahan diminta'
);

ALTER TABLE `permintaan` ADD FOREIGN KEY (`pemohon_id`) REFERENCES `user` (`id`);

ALTER TABLE `permintaan_detail` ADD FOREIGN KEY (`permintaan_id`) REFERENCES `permintaan` (`id`);

ALTER TABLE `permintaan_detail` ADD FOREIGN KEY (`bahan_id`) REFERENCES `bahan_baku` (`id`);
