<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

// Handle delete notification
if(isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
if(isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Initialize variable for siswa
$siswa = [];

// Check if kelas parameter is set
if (isset($_GET['kelas'])) {
    $kelas_id = $_GET['kelas'];
    // Lakukan query untuk mengambil siswa berdasarkan $kelas_id
    $query_siswa = "SELECT * FROM siswa WHERE kelas_id = ?";
    $stmt = $conn->prepare($query_siswa);
    $stmt->bind_param("i", $kelas_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $siswa = $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch all classes with jumlah siswa
$query = "SELECT k.*, COUNT(s.id) as jumlah_siswa 
          FROM kelas k 
          LEFT JOIN siswa s ON k.id = s.kelas_id 
          GROUP BY k.id 
          ORDER BY k.tingkat, k.nama_kelas";
$kelas = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kelas - Sistem Absensi</title>
    <link href="../style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .card-class {
            transition: all 0.3s;
            border-left: 4px solid #4361ee;
        }
        .card-class:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .badge-tier {
            font-size: 0.8em;
            background-color: #3a0ca3;
        }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    <?php include '../sidebar.php'; ?>

    <main class="col-md-9 ms-sm-9 col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Manajemen Kelas</h1>
            <div class="btn-toolbar">
                <a href="../dashboard.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                
                
            </div>
            <a href="tambah.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Kelas
                </a>
        </div>

        <?php if(isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php while($row = mysqli_fetch_assoc($kelas)): ?>
            <div class="col-md-4 mb-4">
                <div class="card card-class h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <span class="badge badge-tier me-2">Kelas <?= $row['tingkat'] ?></span>
                            <?= $row['nama_kelas'] ?>
                        </h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="edit.php?id=<?= $row['id'] ?>"><i class="bi bi-pencil"></i> Edit</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus kelas ini?')"><i class="bi bi-trash"></i> Hapus</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><strong>Wali Kelas:</strong> <?= $row['wali_kelas'] ?: '-' ?></p>
                        <p class="card-text"><strong>Jumlah Siswa:</strong> <?= $row['jumlah_siswa'] ?></p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="../siswa/index.php?kelas=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-people"></i> Lihat Siswa
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <?php if (!empty($siswa)): ?>
            <h2 class="mt-4">Daftar Siswa di Kelas <?= $kelas_id ?></h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($siswa as $s): ?>
                        <tr>
                            <td><?= $s['id'] ?></td>
                            <td><?= $s['nama'] ?></td>
                            <td>
                                <a href="edit_siswa.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <a href="hapus_siswa.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus siswa ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
