CREATE DATABASE toko_mini;
USE toko_mini;


CREATE TABLE users (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE kategori (
    id_kategori INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL
);


CREATE TABLE produk (
    id_produk INT PRIMARY KEY AUTO_INCREMENT,
    id_kategori INT,
    nama_produk VARCHAR(255) NOT NULL,
    harga INT NOT NULL,
    stok INT NOT NULL,
    deskripsi TEXT,
    foto VARCHAR(255),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_kategori)
    REFERENCES kategori(id_kategori)
    ON DELETE SET NULL
);


CREATE TABLE pesanan (
    id_pesanan INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NULL,

    nama_pembeli VARCHAR(100),
    alamat TEXT,
    no_hp VARCHAR(20),

    total_bayar INT NOT NULL,

    status_pesanan ENUM(
        'pending',
        'diproses',
        'dikirim',
        'selesai'
    ) DEFAULT 'pending',

    tanggal_pesan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_user)
    REFERENCES users(id_user)
    ON DELETE SET NULL
);

ALTER TABLE pesanan
ADD metode_pembayaran VARCHAR(100) AFTER total_bayar,

ADD status_pembayaran ENUM(
    'belum_bayar',
    'menunggu_verifikasi',
    'dibayar'
) DEFAULT 'belum_bayar' AFTER metode_pembayaran;


CREATE TABLE detail_pesanan (
    id_detail INT PRIMARY KEY AUTO_INCREMENT,
    id_pesanan INT,
    id_produk INT,

    jumlah INT NOT NULL,
    subtotal INT NOT NULL,

    FOREIGN KEY (id_pesanan)
    REFERENCES pesanan(id_pesanan)
    ON DELETE CASCADE,

    FOREIGN KEY (id_produk)
    REFERENCES produk(id_produk)
    ON DELETE CASCADE
);


INSERT INTO users (
    nama,
    email,
    password,
    role
)
VALUES (
    'Administrator',
    'admin@gmail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin'
);


INSERT INTO kategori (nama_kategori)
VALUES
('Elektronik'),
('Fashion'),
('Kesehatan'),
('Aksesoris');