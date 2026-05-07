<?php
session_start(); // <-- Tambahkan titik koma di sini

$host = "localhost";
$username = "root";
$password = "";
$dbname = "toko_sopia";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // echo "Koneksi database berhasil!"; 
    // Catatan: Sebaiknya baris echo di atas dihapus atau di-comment jika aplikasi sudah berjalan normal, 
    // agar teks ini tidak bocor dan merusak tampilan HTML di halaman public.
    
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>