<?php
$host = "localhost:3307";
$user = "root";
$pass = "";
$db  = "toko_sopia";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
