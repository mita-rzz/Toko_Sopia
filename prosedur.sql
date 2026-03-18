DELIMITER //
CREATE TRIGGER  pengurangan_stok_otomatis
BEFORE INSERT ON detail_transaksi
FOR EACH ROW 
BEGIN 
    DECLARE stok_sekarang INT;
    DECLARE pesan_error VARCHAR(50);
    SELECT stok INTO stok_sekarang FROM BARANG  
    WHERE id_barang=NEW.id_barang;

    IF NEW.jumlah > stok_sekarang THEN
        SET pesan_error = CONCAT ("PROSES GAGAL !!, STOK YANG ADA TERSISA ",stok_sekarang);
         SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = pesan_error;
   
    ELSE 
        UPDATE barang 
        SET stok = stok - NEW.jumlah
        WHERE id_barang = NEW.id_barang;
    END IF; 
END //
DELIMITER ;

--  dibawah ini pencobaan gagal
INSERT INTO detail_transaksi(id_transaksi,id_barang,jumlah) VALUES
(1,4,1000000);
