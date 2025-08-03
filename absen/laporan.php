<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-t');
$kelas_id = isset($_GET['kelas']) ? (int)$_GET['kelas'] : 0;

// Get summary data
$query = "SELECT 
            s.nama as nama_siswa,
            k.nama_kelas,
            k.tingkat,
            COUNT(CASE WHEN a.status = 'H' THEN 1 END) as hadir,
            COUNT(CASE WHEN a.status = 'I' THEN 1 END) as izin,
            COUNT(CASE WHEN a.status = 'S' THEN 1 END) as sakit,
            COUNT(CASE WHEN a.status = 'A' THEN 1 END) as alpa,
            COUNT(*) as total
          FROM siswa s
          LEFT JOIN absensi a ON s.id = a.siswa_id AND DATE(a.tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
          JOIN kelas k ON s.kelas_id = k.id
          WHERE ($kelas_id = 0 OR s.kelas_id = $kelas_id)
          GROUP BY s.id
          ORDER BY k.tingkat, k.nama_kelas, s.nama";

$laporan = mysqli_query($conn, $query);

// Get classes for filter
$kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY tingkat, nama_kelas");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .table-responsive { max-height: 70vh; }
        .table-summary th { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    <?php include '../sidebar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Laporan Absensi</h1>
            <div>
                <a href="export.php?tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>&kelas=<?= $kelas_id ?>" 
                   class="btn btn-success">
                    <i class="bi bi-file-excel"></i> Export Excel
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
                                <option value="0">Semua Kelas</option>
                                <?php while($row = mysqli_fetch_assoc($kelas)): ?>
                                    <option value="<?= $row['id'] ?>" <?= $kelas_id == $row['id'] ? 'selected' : '' ?>>
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

                <div class="alert alert-info mb-3">
                    Periode: <?= date('d/m/Y', strtotime($tgl_awal)) ?> - <?= date('d/m/Y', strtotime($tgl_akhir)) ?>
                    <?php if($kelas_id > 0): ?>
                        | Kelas: <?= mysqli_fetch_assoc(mysqli_query($conn, 
                            "SELECT CONCAT(tingkat, ' ', nama_kelas) as nama FROM kelas WHERE id = $kelas_id"))['nama'] ?>
                    <?php endif; ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Nama Siswa</th>
                                <th rowspan="2">Kelas</th>
                                <th colspan="4" class="text-center">Jumlah Absensi</th>
                                <th rowspan="2">Total</th>
                            </tr>
                            <tr>
                                <th>Hadir</th>
                                <th>Izin</th>
                                <th>Sakit</th>
                                <th>Alpa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while($row = mysqli_fetch_assoc($laporan)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['nama_siswa'] ?></td>
                                <td><?= $row['tingkat'] . ' ' . $row['nama_kelas'] ?></td>
                                <td class="text-center"><?= $row['hadir'] ?></td>
                                <td class="text-center"><?= $row['izin'] ?></td>
                                <td class="text-center"><?= $row['sakit'] ?></td>
                                <td class="text-center"><?= $row['alpa'] ?></td>
                                <td class="text-center"><?= $row['total'] ?></td>
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
