<?php
$host = 'localhost';
$database = 'pdimnnrq_absensi';
$username = 'pdimnnrq_user-absensi';
$password = '{N+%mGE?1l7!+A=&';

date_default_timezone_set('Asia/Jakarta');



$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset to utf8
mysqli_set_charset($conn, "utf8mb4");
