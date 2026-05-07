USE toko_sopia;

INSERT INTO kategori (nama_kategori)
VALUES ("Kebutuhan Pokok"),
 ("Makanan Ringan "),
 ("Minuman"),
 ("Personal care"),
 ("Home Care"),
 ("Health Care"),
 ("Baby Care"),
 ("Fresh Food");



INSERT INTO pelanggan(nama_depan, nama_belakang) VALUES 
("Daffa", "A’bari"),
("Muhammad", "Zulkifli"),
("Gideon", "Chandra"),
("Reze","Panjaitan"),
("Letdark","Hytam"),
("Jeffrey","Epsten"),
("P","Diddy"),
("Dreamy", "Bull"),
("Si","Imut"),
("Kucing", "Bandung");


INSERT INTO kasir(username,password, nama_depan,nama_belakang) VALUES
("admin","admin123","Budi","Ari"),
("username","password","Ryan","Gosling"),
("mita","PencintaOrangImutBandung","mita",""),
("ohim","BurungPerkutut", "Muchammad","Abdurohim "),
("Chev", "GideonGanteng123","Cheval","Grand");


INSERT INTO barang(id_kategori, nama_barang,harga,stok) VALUES
(3,"kopikap",1000,200),
(4,"shampo batman",16900,50),
(3,"milku", 3500,100),
(6, "medkit",25000, 40),
(8, "burger",20000,10),
(1,"akua",5000,200);

INSERT INTO transaksi(id_pelanggan, id_kasir,total_pembayaran) VALUES 
(6,4,28500),
(5,4,2000),
(8,4,10000),
(10,4,16900),
(4,4,7000);


INSERT INTO pembayaran (id_transaksi,jumlah_bayar,  metode_pembayaran) VALUES
(1, 28500,"TUNAI"),
(2, 2000, "TUNAI"),
(3, 10000, "TUNAI"),
(4,16900,"TUNAI"),
(5,7000,"TUNAI");


INSERT INTO detail_transaksi(id_transaksi,id_barang,jumlah) VALUES
(1,4,1),
(1,3,1),
(2,1,2),
(3,6,2),
(4,2,1),
(5,3,2);



