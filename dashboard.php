<?php
session_start();
require 'koneksi.php';

// Redirect jika belum login
if(!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}

// Query data untuk dashboard
$totalSiswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa");
$totalSiswa = mysqli_fetch_assoc($totalSiswa)['total'];

$totalKelas = mysqli_query($conn, "SELECT COUNT(*) as total FROM kelas");
$totalKelas = mysqli_fetch_assoc($totalKelas)['total'];

$queryAbsenHariIni = "SELECT COUNT(*) as total FROM absen WHERE DATE(tanggal) = CURDATE()";
$totalAbsenHariIni = mysqli_query($conn, $queryAbsenHariIni);
$totalAbsenHariIni = mysqli_fetch_assoc($totalAbsenHariIni)['total'];

$queryAbsenTerbaru = "SELECT a.id, s.nama, k.nama_kelas, a.status, a.tanggal 
                     FROM absen a 
                     JOIN siswa s ON a.siswa_id = s.id 
                     JOIN kelas k ON s.kelas_id = k.id 
                     ORDER BY a.tanggal DESC LIMIT 5";
$absenTerbaru = mysqli_query($conn, $queryAbsenTerbaru);


// Set default filter (current month)
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-t');
$kelas_id = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;

// Query untuk analisis absensi
$query = "SELECT 
            k.tingkat,
            k.nama_kelas,
            k.jurusan,
            COUNT(CASE WHEN a.status = 'H' THEN 1 END) AS total_hadir,
            COUNT(CASE WHEN a.status = 'I' THEN 1 END) AS total_izin,
            COUNT(CASE WHEN a.status = 'S' THEN 1 END) AS total_sakit,
            COUNT(CASE WHEN a.status = 'A' THEN 1 END) AS total_alpa,
            COUNT(*) AS total,
            ROUND((COUNT(CASE WHEN a.status = 'H' THEN 1 END) * 100 / COUNT(*)), 2) AS persen_hadir
          FROM absensi a
          JOIN siswa s ON a.siswa_id = s.id
          JOIN kelas k ON s.kelas_id = k.id
          WHERE DATE(a.tanggal) BETWEEN ? AND ?
          " . ($kelas_id > 0 ? "AND s.kelas_id = $kelas_id" : "") . "
          GROUP BY k.id
          ORDER BY k.tingkat, k.nama_kelas";

$stmt = mysqli_prepare($conn, $query);
if($kelas_id > 0) {
    mysqli_stmt_bind_param($stmt, 'ss', $tgl_awal, $tgl_akhir);
} else {
    mysqli_stmt_bind_param($stmt, 'ss', $tgl_awal, $tgl_akhir);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Get classes for filter dropdown
$kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY tingkat, nama_kelas");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .card-header {
            background-color: #4361ee;
            color: white;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .badge-hadir { background-color: #28a745; }
        .badge-izin { background-color: #ffc107; color: #212529; }
        .badge-sakit { background-color: #17a2b8; }
        .badge-alpa { background-color: #dc3545; }
    </style>
    <link href="style.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="col-md-2 ms-sm-1 col-lg-12 px-md-6 py-4">
        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Dashboard</h2>
                <div class="text-muted"><?= date('l, d F Y') ?></div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="card-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <h5 class="card-title">Total Siswa</h5>
                            <h2 class="card-text"><?= $totalSiswa ?></h2>
                            <a href="siswa/index.php" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="card-icon">
                                <i class="bi bi-house-door"></i>
                            </div>
                            <h5 class="card-title">Total Kelas</h5>
                            <h2 class="card-text"><?= $totalKelas ?></h2>
                            <a href="kelas/index.php" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="card-icon">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                            <h5 class="card-title">Absensi Hari Ini</h5>
                            <h2 class="card-text"><?= $row['total'] ?></h2>
                            <a href="absen/index.php" class="btn btn-sm btn-outline-primary">Input Absen</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Absensi -->
            
            
            
        <div class="card">
            <div class="card-header">
                <h5 class="mb-2"><i class="bi bi-table"></i> Data Analisis Absensi</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    Menampilkan data dari <strong><?= date('d M Y', strtotime($tgl_awal)) ?></strong> 
                    sampai <strong><?= date('d M Y', strtotime($tgl_akhir)) ?></strong>
                    <?php if($kelas_id > 0): ?>
                        untuk kelas <?= htmlspecialchars(mysqli_fetch_assoc(mysqli_query($conn, 
                            "SELECT CONCAT(tingkat, ' ', nama_kelas) as nama FROM kelas WHERE id = $kelas_id"))['nama']) ?>
                    <?php endif; ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Kelas</th>
                                <th>Hadir</th>
                                <th>Izin</th>
                                <th>Sakit</th>
                                <th>Alpa</th>
                                <th>Total</th>
                                <th>% Hadir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $row['tingkat'] . ' ' . $row['nama_kelas'] . ' ' ?></td>
                                <td><span class="badge badge-hadir"><?= $row['total_hadir'] ?></span></td>
                                <td><span class="badge badge-izin"><?= $row['total_izin'] ?></span></td>
                                <td><span class="badge badge-sakit"><?= $row['total_sakit'] ?></span></td>
                                <td><span class="badge badge-alpa"><?= $row['total_alpa'] ?></span></td>
                                <td><strong><?= $row['total'] ?></strong></td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: <?= $row['persen_hadir'] ?>%" 
                                             aria-valuenow="<?= $row['persen_hadir'] ?>" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            <?= $row['persen_hadir'] ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <p> </p>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-primary-custom text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 mb-3">
                            <a href="absen/index.php" class="btn btn-primary w-100 py-3">
                                <i class="bi bi-clipboard-check-fill me-2"></i>Input Absen
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="siswa/index.php?action=tambah" class="btn btn-success w-100 py-3">
                                <i class="bi bi-person-plus-fill me-2"></i>Tambah Siswa
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="kelas/index.php?action=tambah" class="btn btn-info w-100 py-3">
                                <i class="bi bi-plus-square-fill me-2"></i>Tambah Kelas
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="absen/laporan.analisis.php" class="btn btn-warning w-100 py-3">
                                <i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tambahkan JavaScript untuk interaksi dropdown (jika diperlukan) -->
<script>
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const dropdownMenu = dropdownToggle.nextElementSibling;
    // Periksa status dropdown saat halaman dimuat
    if (localStorage.getItem('dropdownOpen') === 'true') {
        dropdownMenu.classList.add('show');
    }
    dropdownToggle.addEventListener('click', event => {
        dropdownMenu.classList.toggle('show');
        // Simpan status dropdown ke localStorage
        localStorage.setItem('dropdownOpen', dropdownMenu.classList.contains('show'));
    });
</script>
</body>
</html>

