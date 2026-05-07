SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;

START TRANSACTION;

SELECT harga, stok 
FROM barang 
WHERE id_barang = 3;

INSERT INTO transaksi(id_pelanggan, id_kasir, total_pembayaran)
VALUES (1, 1, 7000);

SET @id_transaksi = LAST_INSERT_ID();

INSERT INTO detail_transaksi(id_transaksi, id_barang, jumlah)
VALUES (@id_transaksi, 3, 2);

UPDATE barang
SET stok = stok - 2
WHERE id_barang = 3
AND stok >= 2;

SELECT ROW_COUNT();

INSERT INTO pembayaran(id_transaksi, jumlah_bayar, metode_pembayaran)
VALUES (@id_transaksi, 7000, 'TUNAI');

COMMIT;
