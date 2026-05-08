<?php
// config/database.php

$host = "localhost";
$user = "root";
$pass = ""; // Sesuaikan dengan password database Anda
$db  = "tokosopia";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
die("Koneksi database gagal: " . mysqli_connect_error());
}
?>