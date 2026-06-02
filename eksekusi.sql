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