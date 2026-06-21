


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

CREATE TABLE cart (
    id_cart INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_produk INT NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_user)
    REFERENCES users(id_user)
    ON DELETE CASCADE,

    FOREIGN KEY (id_produk)
    REFERENCES produk(id_produk)
    ON DELETE CASCADE
);

DELETE FROM users
WHERE email='admin@gmail.com';

INSERT INTO users (
    nama,
    email,
    password,
    role
)
VALUES (
    'Administrator',
    'admin@gmail.com',
    'admin123',
    'admin'
);

UPDATE users 
SET password = '$2y$10$TSC3hKziVtCwaD.PUlCNiOYB1XGjImZ.N1lQ0jJFBEz1USaELzIvS'
WHERE email = 'admin@gmail.com';

ALTER TABLE produk
ADD spesifikasi TEXT AFTER deskripsi;

ALTER TABLE produk
ADD brand VARCHAR(100) AFTER id_kategori,

ADD kondisi ENUM(
'Baru',
'Bekas'
) DEFAULT 'Baru' AFTER harga,

ADD garansi VARCHAR(100)
AFTER kondisi,

ADD berat INT
AFTER garansi,

ADD status_produk ENUM(
'tersedia',
'habiss'
) DEFAULT 'tersedia'
AFTER stok;

ALTER TABLE users
ADD foto VARCHAR(255) NULL,
ADD no_hp VARCHAR(20) NULL,
ADD alamat TEXT NULL;

DESC users;

ALTER TABLE pesanan 
ADD batas_bayar DATETIME NULL,
ADD payment_ref VARCHAR(100),
ADD paid_at DATETIME NULL;

ALTER TABLE pesanan ADD COLUMN bukti_pembayaran VARCHAR(255) DEFAULT NULL;
ALTER TABLE produk ADD COLUMN file_pdf VARCHAR(255) NULL;

DESC cart;
SELECT * FROM cart;