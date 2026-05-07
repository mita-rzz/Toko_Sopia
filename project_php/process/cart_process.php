<?php
require_once '../config/database.php';

if(isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    if($action == 'add') {
        // Query dengan PDO Prepared Statement
        $sql = "SELECT * FROM barang WHERE id_barang = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Periksa apakah stok cukup
            $qty_sekarang = isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id]['qty'] : 0;
            if($row['stok'] > $qty_sekarang) {
                if(isset($_SESSION['cart'][$id])) {
                    $_SESSION['cart'][$id]['qty'] += 1;
                } else {
                    $_SESSION['cart'][$id] = [
                        'nama' => $row['nama_barang'],
                        'harga' => $row['harga'],
                        'qty' => 1
                    ];
                }
            } else {
                $_SESSION['error'] = "Stok barang '{$row['nama_barang']}' tidak mencukupi!";
            }
        }
    } 
    elseif ($action == 'delete') {
        if(isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
    }
}

header("Location: ../public/index.php");
exit();
?>