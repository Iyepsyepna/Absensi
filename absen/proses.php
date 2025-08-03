<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

// Handle delete
if(isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM absensi WHERE id = $id");
    
    $_SESSION['success'] = "Data absensi berhasil dihapus";
    header('Location: index.php');
    exit;
}

// Handle create/update
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kelas_id = (int)$_POST['kelas_id'];
    $tanggal = $_POST['tanggal'];
    $user_id = $_SESSION['user_id'];
    
    if(isset($_POST['status'])) {
        foreach($_POST['status'] as $siswa_id => $status) {
            $keterangan = $_POST['keterangan'][$siswa_id] ?? '';
            
            // Check if attendance already exists
            $check = mysqli_query($conn, 
                "SELECT id FROM absensi 
                 WHERE siswa_id = $siswa_id 
                 AND DATE(tanggal) = '$tanggal'");
            
            if(mysqli_num_rows($check) > 0) {
                $row = mysqli_fetch_assoc($check);
                $query = "UPDATE absensi SET 
                         status = '$status',
                         keterangan = '$keterangan',
                         user_id = $user_id
                         WHERE id = {$row['id']}";
            } else {
                $query = "INSERT INTO absensi 
                         (siswa_id, status, tanggal, keterangan, user_id) 
                         VALUES 
                         ($siswa_id, '$status', '$tanggal', '$keterangan', $user_id)";
            }
            
            mysqli_query($conn, $query);
        }
    }
    
    $_SESSION['success'] = "Data absensi berhasil disimpan";
    header("Location: index.php?tgl_awal=$tanggal&tgl_akhir=$tanggal");
    exit;
}

header('Location: index.php');
?>
