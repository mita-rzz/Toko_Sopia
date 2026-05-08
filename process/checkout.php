<?php
include '../config/database.php';

if(!empty($_SESSION['cart'])) {
    $total_harga = 0;
    foreach($_SESSION['cart'] as $item) {
        $total_harga += ($item['harga'] * $item['qty']);
    }

    $tanggal = date('Y-m-d H:i:s');
    
    $sql_transaksi = "INSERT INTO tb_transaksi (total_harga, tanggal) VALUES (:total_harga, :tanggal)";
    $stmt_transaksi = $conn->prepare($sql_transaksi);
    
    if($stmt_transaksi->execute(['total_harga' => $total_harga, 'tanggal' => $tanggal])) {
        
        $sql_update_stok = "UPDATE tb_produk SET stok = stok - :qty WHERE id_produk = :id_produk";
        $stmt_update_stok = $conn->prepare($sql_update_stok);
 
        foreach($_SESSION['cart'] as $id_produk => $item) {
            $stmt_update_stok->execute([
                'qty' => $item['qty'],
                'id_produk' => $id_produk
            ]);
        }

        unset($_SESSION['cart']);
        $_SESSION['pesan'] = "Transaksi berhasil dicommit dan stok telah dikurangi otomatis!";
    } else {
        $_SESSION['error'] = "Gagal memproses transaksi.";
    }
}

header("Location: ../public/index.php");
exit();
?>