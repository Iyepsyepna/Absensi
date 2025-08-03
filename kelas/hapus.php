<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

if(!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

// Check if class has students
$check = mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa WHERE kelas_id = $id");
$result = mysqli_fetch_assoc($check);

if($result['total'] > 0) {
    $_SESSION['error'] = "Tidak bisa menghapus kelas karena masih memiliki siswa! Pindahkan siswa terlebih dahulu.";
    header('Location: index.php');
    exit;
}

if(mysqli_query($conn, "DELETE FROM kelas WHERE id = $id")) {
    $_SESSION['success'] = "Data kelas berhasil dihapus";
} else {
    $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($conn);
}

header('Location: index.php');
exit;
?>
