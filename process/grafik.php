<?php
header('Content-Type: application/json');

include '../config/database.php';

$allowed = ['penjualan_harian', 'penjualan_bulanan', 'barang_terlaris', 'ringkasan'];
$type    = isset($_GET['type']) ? $_GET['type'] : '';

if (!in_array($type, $allowed, true)) {
    echo json_encode(['status' => 'error', 'message' => 'Tipe grafik tidak dikenali.']);
    exit;
}

if ($type === 'penjualan_harian') {
    $sql = "
        SELECT
            DATE(tanggal)                       AS tanggal,
            DATE_FORMAT(tanggal, '%d %b')       AS label_hari,
            COUNT(id_transaksi)                 AS jumlah_transaksi,
            COALESCE(SUM(total_pembayaran), 0)       AS total_penjualan
        FROM transaksi
        WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY DATE(tanggal)
        ORDER BY tanggal ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $data = array_map(function ($row) {
        return [
            'label'            => $row['label_hari'],
            'jumlah_transaksi' => (int)   $row['jumlah_transaksi'],
            'total_penjualan'  => (float) $row['total_penjualan'],
        ];
    }, $rows);

    echo json_encode(['status' => 'success', 'type' => $type, 'data' => $data]);
}
elseif ($type === 'penjualan_bulanan') {
    $sql = "
        SELECT
            DATE_FORMAT(tanggal, '%Y-%m')       AS bulan,
            DATE_FORMAT(tanggal, '%b %Y')       AS label_bulan,
            COUNT(id_transaksi)                 AS jumlah_transaksi,
            COALESCE(SUM(total_pembayaran), 0)       AS total_penjualan
        FROM transaksi
        WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
        ORDER BY bulan ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $data = array_map(function ($row) {
        return [
            'label'            => $row['label_bulan'],
            'jumlah_transaksi' => (int)   $row['jumlah_transaksi'],
            'total_penjualan'  => (float) $row['total_penjualan'],
        ];
    }, $rows);

    echo json_encode(['status' => 'success', 'type' => $type, 'data' => $data]);
}
elseif ($type === 'barang_terlaris') {
    $sql = "
        SELECT
            b.nama_barang,
            COALESCE(SUM(dt.jumlah), 0)              AS total_terjual,
            COALESCE(SUM(dt.jumlah * b.harga), 0)   AS total_omzet
        FROM detail_transaksi AS dt
        JOIN barang            AS b  ON dt.id_barang    = b.id_barang
        JOIN transaksi         AS t  ON dt.id_transaksi = t.id_transaksi
        WHERE t.tanggal >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY b.id_barang, b.nama_barang
        ORDER BY total_terjual DESC
        LIMIT 10
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    $data = array_map(function ($row) {
        return [
            'nama_barang'   => $row['nama_barang'],
            'total_terjual' => (int)   $row['total_terjual'],
            'total_omzet'   => (float) $row['total_omzet'],
        ];
    }, $rows);

    echo json_encode(['status' => 'success', 'type' => $type, 'data' => $data]);
}
elseif ($type === 'ringkasan') {
    $data = [];
    $stmt = $conn->prepare("
        SELECT
            COALESCE(SUM(total_pembayaran), 0) AS total,
            COUNT(id_transaksi)           AS cnt
        FROM transaksi
        WHERE DATE(tanggal) = CURDATE()
    ");
    $stmt->execute();
    $row = $stmt->fetch();
    $data['penjualan_hari_ini']  = (float) $row['total'];
    $data['transaksi_hari_ini']  = (int)   $row['cnt'];

    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(total_pembayaran), 0) AS total
        FROM transaksi
        WHERE MONTH(tanggal) = MONTH(CURDATE())
          AND YEAR(tanggal)  = YEAR(CURDATE())
    ");
    $stmt->execute();
    $row = $stmt->fetch();
    $data['penjualan_bulan_ini'] = (float) $row['total'];

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM barang
        WHERE stok > 0
    ");
    $stmt->execute();
    $row = $stmt->fetch();
    $data['produk_aktif'] = (int) $row['total'];

    echo json_encode(['status' => 'success', 'type' => $type, 'data' => $data]);
}
