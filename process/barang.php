<?php
header('Content-Type: application/json');
include '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

try {

    /* ──────────────────────────────────────────────────────────────
     | GET  – list barang (JOIN kategori) atau daftar kategori
     ────────────────────────────────────────────────────────────── */
    if ($method === 'GET') {

        // ?type=kategori  → kembalikan semua kategori
        if (isset($_GET['type']) && $_GET['type'] === 'kategori') {
            $stmt = $conn->prepare("SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC");
            $stmt->execute();
            echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
            exit;
        }

        // default → list barang + JOIN kategori
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $sql = "SELECT b.id_barang, b.nama_barang, b.harga, b.stok,
                       b.id_kategori, k.nama_kategori
                FROM   barang b
                LEFT JOIN kategori k ON b.id_kategori = k.id_kategori";

        if ($search !== '') {
            $sql .= " WHERE b.nama_barang LIKE :s OR k.nama_kategori LIKE :s";
            $sql .= " ORDER BY b.id_barang DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':s' => '%' . $search . '%']);
        } else {
            $sql .= " ORDER BY b.id_barang DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        }

        echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        exit;
    }

    /* ──────────────────────────────────────────────────────────────
     | Parse body untuk POST / PUT / DELETE
     ────────────────────────────────────────────────────────────── */
    $body = [];
    if ($method === 'POST') {
        $body = $_POST;
    } else {
        parse_str(file_get_contents('php://input'), $body);
    }

    /* ──────────────────────────────────────────────────────────────
     | POST – tambah barang baru
     ────────────────────────────────────────────────────────────── */
    if ($method === 'POST') {
        $nama        = trim($body['nama_barang']  ?? '');
        $harga       = (int)($body['harga']       ?? 0);
        $stok        = (int)($body['stok']        ?? 0);
        $id_kategori = (int)($body['id_kategori'] ?? 0);

        if ($nama === '' || $harga <= 0 || $id_kategori <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Nama barang, harga, dan kategori wajib diisi!']);
            exit;
        }

        $stmt = $conn->prepare(
            "INSERT INTO barang (nama_barang, harga, stok, id_kategori)
             VALUES (:nama, :harga, :stok, :id_kategori)"
        );
        $stmt->execute([
            ':nama'        => $nama,
            ':harga'       => $harga,
            ':stok'        => $stok,
            ':id_kategori' => $id_kategori,
        ]);
        echo json_encode(['status' => 'success', 'message' => 'Barang berhasil ditambahkan!']);
        exit;
    }

    /* ──────────────────────────────────────────────────────────────
     | PUT – edit barang
     ────────────────────────────────────────────────────────────── */
    if ($method === 'PUT') {
        $id          = (int)($body['id_barang']   ?? 0);
        $nama        = trim($body['nama_barang']  ?? '');
        $harga       = (int)($body['harga']       ?? 0);
        $stok        = (int)($body['stok']        ?? 0);
        $id_kategori = (int)($body['id_kategori'] ?? 0);

        if ($id <= 0 || $nama === '' || $harga <= 0 || $id_kategori <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak valid!']);
            exit;
        }

        $stmt = $conn->prepare(
            "UPDATE barang
             SET nama_barang = :nama, harga = :harga, stok = :stok, id_kategori = :id_kategori
             WHERE id_barang = :id"
        );
        $stmt->execute([
            ':nama'        => $nama,
            ':harga'       => $harga,
            ':stok'        => $stok,
            ':id_kategori' => $id_kategori,
            ':id'          => $id,
        ]);
        echo json_encode(['status' => 'success', 'message' => 'Barang berhasil diperbarui!']);
        exit;
    }

    /* ──────────────────────────────────────────────────────────────
     | DELETE – hapus barang
     ────────────────────────────────────────────────────────────── */
    if ($method === 'DELETE') {
        $id = (int)($body['id_barang'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID tidak valid!']);
            exit;
        }
        $stmt = $conn->prepare("DELETE FROM barang WHERE id_barang = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode(['status' => 'success', 'message' => 'Barang berhasil dihapus!']);
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Metode tidak didukung.']);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
