<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

// Validasi parameter
if(!isset($_GET['tanggal']) || !isset($_GET['kelas_id'])) {
    die("Parameter tidak lengkap");
}

$tanggal = $_GET['tanggal'];
$kelas_id = (int)$_GET['kelas_id'];

// Validasi format tanggal
if(!DateTime::createFromFormat('Y-m-d', $tanggal)) {
    die("Format tanggal tidak valid");
}

// Ambil data kelas
$kelas = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT * FROM kelas WHERE id = $kelas_id"));

if(!$kelas) {
    die("Kelas tidak ditemukan");
}

// Ambil data siswa dan absensi
$query = "SELECT 
            s.id, s.nis, s.nama, 
            a.status, a.keterangan, a.tanggal
          FROM siswa s
          LEFT JOIN absensi a ON a.siswa_id = s.id AND DATE(a.tanggal) = ?
          WHERE s.kelas_id = ?
          ORDER BY s.nama";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'si', $tanggal, $kelas_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="../../style.css" rel="stylesheet">
    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 500;
        }
        .status-H { background-color: #d4edda; color: #155724; }
        .status-I { background-color: #fff3cd; color: #856404; }
        .status-S { background-color: #cce5ff; color: #004085; }
        .status-A { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    <?php include '../sidebar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Detail Absensi Siswa</h1>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-check"></i> 
                    <?= htmlspecialchars(date('d F Y', strtotime($tanggal))) ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <strong>Kelas:</strong> <?= htmlspecialchars($kelas['nama_kelas']) ?> 
                    (<?= htmlspecialchars($kelas['jurusan']) ?>)
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">NIS</th>
                                <th>Nama Siswa</th>
                                <th width="15%">Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nis']) ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td>
                                        <?php if($row['status']): ?>
                                            <span class="status-badge status-<?= $row['status'] ?>">
                                                <?php 
                                                    switch($row['status']) {
                                                        case 'H': echo 'Hadir'; break;
                                                        case 'I': echo 'Izin'; break;
                                                        case 'S': echo 'Sakit'; break;
                                                        case 'A': echo 'Alpa'; break;
                                                    }
                                                ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">Belum diisi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">Tidak ada data siswa di kelas ini</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
