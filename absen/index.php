<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

// Filter by date
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-d', strtotime('-7 days'));
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

// Fetch attendance data
$query = "SELECT a.*, s.nama as nama_siswa, k.nama_kelas, k.tingkat 
          FROM absensi a
          JOIN siswa s ON a.siswa_id = s.id
          JOIN kelas k ON s.kelas_id = k.id
          WHERE DATE(a.tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
          ORDER BY a.tanggal DESC, k.tingkat, k.nama_kelas, s.nama";
$absensi = mysqli_query($conn, $query);

// Fetch classes for filter
$kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY tingkat, nama_kelas");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Absensi - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="../style.css" rel="stylesheet">
    <style>
        .status-hadir { background-color: #d4edda; }
        .status-izin { background-color: #fff3cd; }
        .status-sakit { background-color: #cce5ff; }
        .status-alpa { background-color: #f8d7da; }
        .table-responsive { max-height: 70vh; }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    <?php include '../sidebar.php'; ?>

    <main class="col-md-9 ms-sm-9 col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Daftar Absensi Siswa</h1>
            <div>
                <a href="input.php" class="btn btn-primary me-2">
                    <i class="bi bi-plus-circle"></i> Input Absen
                </a>
                <a href="export.index.php?tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>" 
                   class="btn btn-success">
                    <i class="bi bi-file-excel"></i> Export
                </a>
                <a href="../dashboard.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" class="form-control" name="tgl_awal" value="<?= $tgl_awal ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" name="tgl_akhir" value="<?= $tgl_akhir ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kelas</label>
                            <select class="form-select" name="kelas">
                                <option value="">Semua Kelas</option>
                                <?php while($row = mysqli_fetch_assoc($kelas)): ?>
                                    <option value="<?= $row['id'] ?>" <?= isset($_GET['kelas']) && $_GET['kelas'] == $row['id'] ? 'selected' : '' ?>>
                                        <?= $row['tingkat'] ?> <?= $row['nama_kelas'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th>Jam</th>
                                <th>Di Input Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($absensi)): 
                                $status_class = '';
                                switch($row['status']) {
                                    case 'H': $status_class = 'status-hadir'; break;
                                    case 'I': $status_class = 'status-izin'; break;
                                    case 'S': $status_class = 'status-sakit'; break;
                                    case 'A': $status_class = 'status-alpa'; break;
                                }
                            ?>
                            <tr class="<?= $status_class ?>">
                                <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?> <?= date('H:i', strtotime($row['created_at'])) ?></td>
                                <td><?= $row['nama_siswa'] ?></td>
                                <td><?= $row['tingkat'] . ' ' . $row['nama_kelas'] ?></td>
                                <td>
                                    <?php 
                                        $status = [
                                            'H' => 'Hadir',
                                            'I' => 'Izin', 
                                            'S' => 'Sakit',
                                            'A' => 'Alpa'
                                        ];
                                        echo $status[$row['status']];
                                    ?>
                                </td>
                                <td><?= date('H:i', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <?php 
                                        $user_id = [
                                            '1' => 'Kepala Sekolah',
                                            '2' => 'Guru', 
                                            '3' => 'Wakasek',
                                            '4' => 'Wakasek',
                                            '5' => 'Iyep Syepna, S.Pd.'
                                        ];
                                        echo $user_id[$row['user_id']];
                                    ?>
                                    </td>
                                
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
