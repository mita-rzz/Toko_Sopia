<?php
require_once '../config/database.php';

// Fitur 3: Search Barang menggunakan Prepared Statement
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql_produk = "SELECT * FROM barang WHERE nama_barang LIKE :search";
$stmt_produk = $conn->prepare($sql_produk);
$stmt_produk->execute(['search' => "%$search%"]);
$result_produk = $stmt_produk->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>POS Toko Sopia</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f9; padding: 20px; }
        .container { display: flex; gap: 20px; max-width: 1200px; margin: auto; }
        .kiri, .kanan { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .kiri { flex: 2; } .kanan { flex: 1; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #2c3e50; color: white; }
        .btn { padding: 5px 10px; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn-add { background: #27ae60; }
        .btn-del { background: #e74c3c; }
        .btn-update { background: #f39c12; }
        .alert { padding: 10px; background: #dff0d8; color: #3c763d; margin-bottom: 15px; border-radius: 4px; }
        .alert-error { background: #f2dede; color: #a94442; }
    </style>
</head>
<body>

    <h1 style="text-align:center;">Kasir Toko Sopia</h1>

    <?php if(isset($_SESSION['pesan'])): ?>
        <div class="alert"><?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?></div>
    <?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="container">
        <!-- Bagian Kiri: Daftar Produk -->
        <div class="kiri">
            <h2>Daftar Barang</h2>
            
            <!-- Fitur 3: Form Search -->
            <form method="GET" action="index.php" style="margin-bottom: 15px;">
                <input type="text" name="search" placeholder="Cari nama barang..." value="<?= htmlspecialchars($search) ?>" style="padding: 8px; width: 60%;">
                <button type="submit" class="btn btn-add">Cari</button>
                <a href="index.php" class="btn btn-update">Reset</a>
            </form>

            <table>
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
                <?php foreach($result_produk as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td>
                        <!-- Fitur 2: Update Stok Manual -->
                        <form action="../process/stock_process.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id_barang" value="<?= $row['id_barang'] ?>">
                            <input type="number" name="stok_baru" value="<?= $row['stok'] ?>" style="width: 50px;">
                            <button type="submit" class="btn btn-update" style="font-size: 12px;">Update</button>
                        </form>
                    </td>
                    <td>
                        <!-- Fitur 1: Tambah ke Keranjang -->
                        <a href="../process/cart_process.php?action=add&id=<?= $row['id_barang'] ?>" class="btn btn-add">Tambahkan</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- Bagian Kanan: Keranjang -->
        <div class="kanan">
            <h2>Keranjang Transaksi</h2>
            <table>
                <tr>
                    <th>Barang</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
                <?php 
                $total_belanja = 0;
                if(!empty($_SESSION['cart'])): 
                    foreach($_SESSION['cart'] as $id => $item): 
                        $subtotal = $item['harga'] * $item['qty'];
                        $total_belanja += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['nama']) ?></td>
                    <td><?= $item['qty'] ?></td>
                    <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                    <td>
                        <!-- Fitur 4: Hapus dari keranjang -->
                        <a href="../process/cart_process.php?action=delete&id=<?= $id ?>" class="btn btn-del">Hapus</a>
                    </td>
                </tr>
                <?php 
                    endforeach; 
                else: 
                ?>
                <tr><td colspan="4" style="text-align:center;">Keranjang kosong</td></tr>
                <?php endif; ?>
            </table>
            
            <h3 style="text-align:right;">Total: Rp <?= number_format($total_belanja, 0, ',', '.') ?></h3>
            
            <?php if(!empty($_SESSION['cart'])): ?>
                <form action="../process/checkout.php" method="POST">
                    <button type="submit" class="btn btn-add" style="width: 100%; padding: 15px; font-size: 16px;">Commit Transaksi</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>