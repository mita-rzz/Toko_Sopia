<?php
include '../config/database.php';

$is_included = isset($checkout_cart);

if ($is_included) {
    $trx_cart   = $checkout_cart;
    $trx_bayar  = (int) $checkout_bayar;
    $trx_metode = $checkout_metode ?? 'Tunai';
} else {
    session_start();
    $trx_cart   = $_SESSION['cart']   ?? [];
    $trx_bayar  = $_SESSION['bayar']  ?? 0;
    $trx_metode = $_SESSION['metode'] ?? 'Tunai';
}

if (empty($trx_cart)) {
    if ($is_included) {
        $checkout_result = ['status' => 'error', 'message' => 'Keranjang kosong!'];
        return;
    }
    header('Location: ../public/index.php');
    exit;
}

$trx_total = 0;
foreach ($trx_cart as $item) {
    $trx_total += (int)$item['harga'] * (int)$item['qty'];
}
$trx_kembalian = $trx_bayar - $trx_total;

try {
    $conn->beginTransaction();

    $stmt_trx = $conn->prepare(
        "INSERT INTO transaksi (tanggal, total_pembayaran, id_pelanggan, id_kasir)
         VALUES (:tanggal, :total, :id_pelanggan, :id_kasir)"
    );
    $stmt_trx->execute([
        ':tanggal' => date('Y-m-d H:i:s', strtotime('+5 hours')),
        ':total'   => $trx_total,
        ':id_pelanggan' => 5,
        ':id_kasir' => 4
    ]);
    $id_transaksi = $conn->lastInsertId();

    $stmt_detail = $conn->prepare(
        "INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah)
         VALUES (:id_transaksi, :id_barang, :jumlah)"
    );
    $stmt_cek_stok = $conn->prepare(
        "SELECT stok, nama_barang FROM barang WHERE id_barang = ? FOR UPDATE"
    );
    $stmt_update_stok = $conn->prepare(
        "UPDATE barang SET stok = stok - :qty WHERE id_barang = :id_barang"
    );

    foreach ($trx_cart as $item) {
        $id_barang = (int)$item['id'];
        $jumlah    = (int)$item['qty'];
        $harga     = (int)$item['harga'];

        $stmt_cek_stok->execute([$id_barang]);
        $barang = $stmt_cek_stok->fetch(PDO::FETCH_ASSOC);

        if (!$barang) {
            throw new Exception("Barang tidak ditemukan: " . ($item['nama'] ?? $id_barang));
        }
        if ($barang['stok'] < $jumlah) {
            throw new Exception("Stok tidak cukup untuk barang: " . $barang['nama_barang']);
        }

        $stmt_detail->execute([
            ':id_transaksi' => $id_transaksi,
            ':id_barang'    => $id_barang,
            ':jumlah'       => $jumlah
        ]);

        $stmt_update_stok->execute([
            ':qty'       => $jumlah,
            ':id_barang' => $id_barang
        ]);
    }
    
    $stmt_pembayaran = $conn->prepare(
        "INSERT INTO pembayaran (id_transaksi, jumlah_bayar, metode_pembayaran)
         VALUES (:id_transaksi, :jumlah_bayar, :metode_pembayaran)"
    );
    $stmt_pembayaran->execute([
        ':id_transaksi'      => $id_transaksi,
        ':jumlah_bayar'      => $trx_bayar,
        ':metode_pembayaran' => $trx_metode
    ]);

    $conn->commit();
    if ($is_included) {
        $checkout_result = ['status' => 'success', 'message' => 'Pembayaran berhasil! Transaksi tersimpan.'];
    } else {
        unset($_SESSION['cart'], $_SESSION['bayar'], $_SESSION['metode']);
        $_SESSION['pesan'] = 'Transaksi berhasil! Stok telah diperbarui.';
        header('Location: ../public/index.php');
        exit;
    }

} catch (Exception $e) {
    $conn->rollBack();

    if ($is_included) {
        $checkout_result = ['status' => 'error', 'message' => $e->getMessage()];
    } else {
        $_SESSION['error'] = $e->getMessage();
        header('Location: ../public/index.php');
        exit;
    }
}