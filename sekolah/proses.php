<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input data
    $nama_sekolah = mysqli_real_escape_string($conn, $_POST['nama_sekolah']);
    $nss = mysqli_real_escape_string($conn, $_POST['nss']);
    $npsn = mysqli_real_escape_string($conn, $_POST['npsn']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);
    $kabupaten = mysqli_real_escape_string($conn, $_POST['kabupaten']);
    $provinsi = mysqli_real_escape_string($conn, $_POST['provinsi']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $website = mysqli_real_escape_string($conn, $_POST['website']);
    $kepala_sekolah = mysqli_real_escape_string($conn, $_POST['kepala_sekolah']);
    $nip_kepala_sekolah = mysqli_real_escape_string($conn, $_POST['nip_kepala_sekolah']);

    // Check if school data already exists
    $check = mysqli_query($conn, "SELECT * FROM sekolah LIMIT 1");
    
    if(mysqli_num_rows($check) > 0) {
        // Update existing data
        $query = "UPDATE sekolah SET 
                 nama_sekolah = '$nama_sekolah',
                 nss = '$nss',
                 npsn = '$npsn',
                 alamat = '$alamat',
                 kecamatan = '$kecamatan',
                 kabupaten = '$kabupaten',
                 provinsi = '$provinsi',
                 telepon = '$telepon',
                 email = '$email',
                 website = '$website',
                 kepala_sekolah = '$kepala_sekolah',
                 nip_kepala_sekolah = '$nip_kepala_sekolah'";
    } else {
        // Insert new data
        $query = "INSERT INTO sekolah (
                 nama_sekolah, nss, npsn, alamat, kecamatan, 
                 kabupaten, provinsi, telepon, email, website, 
                 kepala_sekolah, nip_kepala_sekolah) 
                 VALUES (
                 '$nama_sekolah', '$nss', '$npsn', '$alamat', '$kecamatan', 
                 '$kabupaten', '$provinsi', '$telepon', '$email', '$website', 
                 '$kepala_sekolah', '$nip_kepala_sekolah')";
    }

    if(mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Data sekolah berhasil diperbarui";
    } else {
        $_SESSION['error'] = "Gagal memperbarui data: " . mysqli_error($conn);
    }
}

header('Location: index.php');
exit;
?>
