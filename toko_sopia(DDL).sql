CREATE DATABASE toko_sopia;

USE toko_sopia;

CREATE TABLE pelanggan (
    id_pelanggan  INT AUTO_INCREMENT PRIMARY KEY,
    nama_depan VARCHAR(50) NOT NULL ,
    nama_belakang VARCHAR (50)

);

CREATE TABLE kasir (
    id_kasir  INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL ,
    nama_depan VARCHAR(50) NOT NULL ,
    nama_belakang VARCHAR(50)  

);

CREATE TABLE kategori (
    id_kategori  INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR (100) NOT NULL
);

CREATE TABLE barang (
    id_barang  INT AUTO_INCREMENT PRIMARY KEY,
    id_kategori INT NOT NULL ,
    nama_barang VARCHAR(100) NOT NULL,
    harga INT NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    
    CONSTRAINT fk_id_kategori FOREIGN KEY (id_kategori) 
    REFERENCES kategori(id_kategori)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE transaksi (
    id_transaksi  INT AUTO_INCREMENT PRIMARY KEY,
    id_pelanggan INT NOT NULL ,
    id_kasir INT NOT NULL ,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_pembayaran INT NOT NULL,    

    CONSTRAINT fk_id_pelanggan FOREIGN KEY (id_pelanggan) 
    REFERENCES pelanggan(id_pelanggan)
    ON DELETE RESTRICT ON UPDATE CASCADE,


    CONSTRAINT fk_id_kasir FOREIGN KEY (id_kasir) 
    REFERENCES kasir(id_kasir)
    ON DELETE RESTRICT ON UPDATE CASCADE

);

CREATE TABLE pembayaran (
    id_pembayaran INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT NOT NULL ,
    jumlah_bayar INT NOT NULL, 
    metode_pembayaran VARCHAR(50) NOT NULL,

    CONSTRAINT fk_id_transaksi FOREIGN KEY (id_transaksi) 
    REFERENCES transaksi(id_transaksi)
    ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE detail_transaksi (
    id_detail_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT NOT NULL ,
    id_barang INT NOT NULL,
    jumlah INT NOT NULL, 


    CONSTRAINT fk_id_transaksi_pembayaran FOREIGN KEY (id_transaksi) 
    REFERENCES transaksi(id_transaksi)
    ON DELETE RESTRICT ON UPDATE CASCADE,


    CONSTRAINT fk_id_barang_pembayaran FOREIGN KEY (id_barang) 
    REFERENCES barang(id_barang)
    ON DELETE RESTRICT ON UPDATE CASCADE
);







