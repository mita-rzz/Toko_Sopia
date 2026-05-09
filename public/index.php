<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'proses_pembayaran') {
    header('Content-Type: application/json');

    $cart  = json_decode($_POST['cart'], true);
    $bayar = isset($_POST['bayar']) ? (int)$_POST['bayar'] : 0;

    if (empty($cart)) {
        echo json_encode(['status' => 'error', 'message' => 'Keranjang kosong!']);
        exit;
    }

    $total = 0;
    foreach ($cart as $item) {
        $total += $item['harga'] * $item['qty'];
    }

    if ($bayar < $total) {
        echo json_encode(['status' => 'error', 'message' => 'Transaksi dibatalkan: Uang bayar kurang dari total belanja!']);
        exit;
    }

    $checkout_cart   = $cart;
    $checkout_bayar  = $bayar;
    $checkout_metode = isset($_POST['metode']) ? $_POST['metode'] : 'Tunai';

    include '../process/checkout.php';

    echo json_encode($checkout_result);
    exit;
}

$stmt_barang = $conn->prepare("SELECT * FROM barang ORDER BY id_barang DESC");
$stmt_barang->execute();
$result_barang = $stmt_barang->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Toko Sopia</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f6f9;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 80px;
            background-color: #ffffff;
            height: 100%;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
            position: fixed;
            z-index: 100;
        }

        .logo-menu {
            font-size: 28px;
            color: #005371;
            margin-bottom: 40px;
            cursor: pointer;
        }

        .nav-item {
            position: relative;
            margin-bottom: 20px;
            text-decoration: none;
            color: #757575;
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .nav-item i {
            font-size: 24px;
        }

        .nav-item.active {
            background: linear-gradient(90deg, #22a699, #005371);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(34, 166, 153, 0.4);
        }

        .nav-item:hover:not(.active) {
            background-color: #f0f0f0;
            color: #22a699;
        }

        .tooltip {
            position: absolute;
            left: 70px;
            background-color: #333;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
            transform: translateX(-10px);
            z-index: 200;
        }

        .tooltip::before {
            content: '';
            position: absolute;
            top: 50%;
            left: -4px;
            transform: translateY(-50%);
            border-width: 5px;
            border-style: solid;
            border-color: transparent #333 transparent transparent;
        }

        .nav-item:hover .tooltip {
            opacity: 1;
            transform: translateX(0);
        }

        .main-content {
            margin-left: 80px;
            padding: 20px;
            width: calc(100% - 80px);
            display: flex;
            gap: 20px;
        }

        .produk-area {
            flex: 7;
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .header-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .search-container {
            position: relative;
            margin-bottom: 20px;
        }

        .search-container i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #888;
            font-size: 20px;
        }

        .search-input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
        }

        .search-input:focus {
            border-color: #22a699;
            box-shadow: 0 0 8px rgba(34, 166, 153, 0.2);
        }

        .grid-produk {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }

        .card-produk {
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
        }

        .card-produk:hover {
            border-color: #22a699;
            box-shadow: 0 4px 10px rgba(34, 166, 153, 0.1);
        }

        .img-placeholder {
            width: 100%;
            height: 120px;
            background-color: #f0f0f0;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #aaa;
            font-size: 40px;
        }

        .nama-barang {
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
        }

        .harga-barang {
            font-size: 16px;
            font-weight: 700;
            color: #005371;
        }

        .stok-barang {
            font-size: 12px;
            color: #757575;
            margin-top: 5px;
        }

        .keranjang-area {
            flex: 3;
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
        }

        .keranjang-list {
            flex-grow: 1;
            overflow-y: auto;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }

        .item-keranjang {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #eee;
        }

        .payment-section {
            margin-bottom: 20px;
            background: #f9fbfd;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #eee;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: #005371;
        }

        .total-area div {
            display: flex;
            justify-content: space-between;
            font-size: 15px;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }

        .total-area .grand-total {
            font-size: 18px;
            font-weight: 700;
            color: #005371;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }

        .btn-bayar {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(90deg, #22a699, #005371);
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-bayar:hover {
            opacity: 0.9;
            box-shadow: 0 4px 15px rgba(34, 166, 153, 0.4);
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <a href="index.php" class="nav-item active">
            <i class='bx bx-laptop'></i> <span class="tooltip">Kasir / Transaksi</span>
        </a>

        <a href="laporan.php" class="nav-item">
            <i class='bx bx-bar-chart-alt-2'></i>
            <span class="tooltip">Laporan</span>
        </a>
    </div>

    <div class="main-content">

        <div class="produk-area">
            <h2 class="header-title">Menu Kasir</h2>

            <div class="search-container">
                <i class='bx bx-search'></i>
                <input type="text" class="search-input" placeholder="Cari nama barang atau scan barcode...">
            </div>

            <div class="grid-produk">
                <?php
                if (!empty($result_barang)) {
                    foreach ($result_barang as $row) {
                ?>
                        <div class="card-produk" onclick="addToCart(<?= $row['id_barang'] ?>, <?= htmlspecialchars(json_encode($row['nama_barang']), ENT_QUOTES, 'UTF-8') ?>, <?= $row['harga'] ?>)">
                            <div class="img-placeholder"><i class='bx bx-package'></i></div>
                            <div class="nama-barang"><?= htmlspecialchars($row['nama_barang']) ?></div>
                            <div class="harga-barang">Rp <?= number_format($row['harga'], 0, ',', '.') ?></div>
                            <div class="stok-barang">Stok: <?= htmlspecialchars($row['stok']) ?></div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p style='color: #888; grid-column: 1 / -1;'>Data barang belum ada di database. Silakan isi tabel barang terlebih dahulu.</p>";
                }
                ?>
            </div>
        </div>

        <div class="keranjang-area">
            <h2 class="header-title" style="font-size: 20px;">Pesanan Saat Ini</h2>

            <div class="keranjang-list">
                <div style="text-align: center; color: #aaa; margin-top: 50px;">
                    <i class='bx bx-cart' style="font-size: 40px;"></i>
                    <p>Keranjang masih kosong</p>
                </div>
            </div>

            <div class="payment-section">
                <div class="form-group">
                    <label for="metode">Metode Pembayaran</label>
                    <select id="metode" class="form-control" name="metode_pembayaran">
                        <option value="Tunai">Tunai</option>
                        <option value="QRIS">QRIS / E-Wallet</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="bayar">Uang Bayar (Rp)</label>
                    <input type="number" id="bayar" class="form-control" placeholder="Masukkan nominal bayar...">
                </div>
            </div>

            <div class="total-area">
                <div>
                    <span>Subtotal</span>
                    <span id="subtotal">Rp 0</span>
                </div>
                <div>
                    <span>Kembalian</span>
                    <span id="kembalian" style="color: #e74c3c;">Rp 0</span>
                </div>
                <div class="grand-total">
                    <span>Total Tagihan</span>
                    <span id="total-tagihan">Rp 0</span>
                </div>
            </div>

            <button class="btn-bayar" style="margin-top: 15px;" onclick="prosesPembayaran()">Proses Pembayaran</button>
        </div>

    </div>

    <script>
        let cart = [];

        function formatRupiah(angka) {
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function addToCart(id, nama, harga) {
            let existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.qty++;
            } else {
                cart.push({
                    id: id,
                    nama: nama,
                    harga: harga,
                    qty: 1
                });
            }
            renderCart();
        }

        function increaseQty(id) {
            let item = cart.find(i => i.id === id);
            if (item) {
                item.qty++;
                renderCart();
            }
        }

        function decreaseQty(id) {
            let itemIndex = cart.findIndex(item => item.id === id);
            if (itemIndex > -1) {
                if (cart[itemIndex].qty > 1) {
                    cart[itemIndex].qty--;
                } else {
                    cart.splice(itemIndex, 1);
                }
            }
            renderCart();
        }

        function renderCart() {
            let cartContainer = document.querySelector('.keranjang-list');

            if (cart.length === 0) {
                cartContainer.innerHTML = `
                    <div style="text-align: center; color: #aaa; margin-top: 50px;">
                        <i class='bx bx-cart' style="font-size: 40px;"></i>
                        <p>Keranjang masih kosong</p>
                    </div>
                `;
                updateTotals();
                return;
            }

            let html = '';
            cart.forEach(item => {
                let totalHargaItem = item.harga * item.qty;
                html += `
                <div class="item-keranjang" style="padding: 10px; border-bottom: 1px dashed #eee; display: flex; justify-content: space-between; align-items: center;">
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 500; color: #333;">${item.nama}</div>
                        <div style="font-size: 12px; color: #757575;">${formatRupiah(item.harga)} x ${item.qty}</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 14px; font-weight: 600; color: #005371; margin-bottom: 5px;">${formatRupiah(totalHargaItem)}</div>
                        <div style="display: flex; gap: 5px; justify-content: flex-end;">
                            <button type="button" onclick="decreaseQty(${item.id})" style="background: #e74c3c; color: white; border: none; border-radius: 4px; width: 20px; height: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center;">-</button>
                            <span style="font-size: 13px; width: 20px; text-align: center;">${item.qty}</span>
                            <button type="button" onclick="increaseQty(${item.id})" style="background: #22a699; color: white; border: none; border-radius: 4px; width: 20px; height: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center;">+</button>
                        </div>
                    </div>
                </div>`;
            });

            cartContainer.innerHTML = html;
            updateTotals();
        }

        function updateTotals() {
            let total = cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);

            document.getElementById('subtotal').innerText = formatRupiah(total);
            document.getElementById('total-tagihan').innerText = formatRupiah(total);

            calculateChange();
        }

        function calculateChange() {
            let total = cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);
            let bayarInput = document.getElementById('bayar').value;
            let bayar = bayarInput ? parseInt(bayarInput) : 0;

            let kembalian = bayar - total;
            let kembalianElement = document.getElementById('kembalian');

            if (kembalian >= 0 && bayar >= total) {
                kembalianElement.innerText = formatRupiah(kembalian);
                kembalianElement.style.color = '#22a699';
            } else {
                kembalianElement.innerText = formatRupiah(kembalian < 0 ? 0 : kembalian);
                kembalianElement.style.color = '#e74c3c';
            }
        }

        document.getElementById('bayar').addEventListener('input', calculateChange);

        function prosesPembayaran() {
            if (cart.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Keranjang masih kosong!'
                });
                return;
            }

            let total = cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);
            let bayarInput = document.getElementById('bayar').value;
            let bayar = bayarInput ? parseInt(bayarInput) : 0;

            if (bayar < total) {
                Swal.fire({
                    icon: 'error',
                    title: 'Transaksi Dibatalkan',
                    text: 'Uang bayar kurang dari total belanja!'
                });
                return;
            }

            let formData = new FormData();
            formData.append('action', 'proses_pembayaran');
            formData.append('cart', JSON.stringify(cart));
            formData.append('bayar', bayar);

            fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat memproses pembayaran.'
                    });
                });
        }

        document.querySelector('.search-input').addEventListener('input', function(e) {
            let searchTerm = e.target.value.toLowerCase();
            let cards = document.querySelectorAll('.card-produk');

            cards.forEach(card => {
                let name = card.querySelector('.nama-barang').innerText.toLowerCase();
                if (name.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>