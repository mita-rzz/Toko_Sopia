DELIMITER //
-- dibawah ini trigger nya
CREATE TRIGGER validasi_pembayaran
BEFORE INSERT ON pembayaran
FOR EACH ROW
BEGIN
    DECLARE total INT;
    DECLARE error_message VARCHAR(50);
    SELECT total_pembayaran INTO total
    FROM transaksi
    WHERE id_transaksi = NEW.id_transaksi;

    IF NEW.jumlah_bayar < total THEN
        SET error_message = 'Pembayaran kurang dari total Belanja';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT =error_message;
    END IF;
END ;  //

DELIMITER ;

-- dibawah ini percobaan gagal 
INSERT INTO transaksi(id_pelanggan, id_kasir,total_pembayaran) 
VALUES  (1,2,50000);
INSERT INTO pembayaran (id_transaksi,jumlah_bayar,  metode_pembayaran) 
VALUES (8,911,'TUNAI');
