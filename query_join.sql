SELECT
    CONCAT (k.nama_depan, ' ', k.nama_belakang) AS nama_kasir,
    CONCAT (p.nama_depan,' ', p.nama_belakang) AS nama_pelangga,
    t.tanggal  AS tanggal_transaksi,
    b.nama_barang,  
    b.harga AS harga_satuan,
    dt.jumlah AS jumlah_persatuan,
    (b.harga*dt.jumlah) AS sub_total
FROM pelanggan p
JOIN transaksi t ON p.id_pelanggan=t.id_pelanggan
JOIN detail_transaksi dt ON t.id_transaksi =dt.id_transaksi
JOIN kasir k ON t.id_kasir = k.id_kasir
JOIN barang b ON dt.id_barang=b.id_barang
WHERE t.id_transaksi=1
ORDER BY dt.id_transaksi  ASC;