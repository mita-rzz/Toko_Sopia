<?php
include '../config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_produk'];
    $stok_baru = $_POST['stok_baru'];

    if(is_numeric($stok_baru) && $stok_baru >= 0) {
        $sql = "UPDATE tb_produk SET stok = :stok_baru WHERE id_produk = :id";
        $stmt = $conn->prepare($sql);
        
        if($stmt->execute(['stok_baru' => $stok_baru, 'id' => $id])) {
            $_SESSION['pesan'] = "Stok berhasil diupdate!";
        } else {
            $_SESSION['error'] = "Gagal update stok.";
        }
    } else {
        $_SESSION['error'] = "Format stok tidak valid!";
    }
}

header("Location: ../public/index.php");
exit();
?>