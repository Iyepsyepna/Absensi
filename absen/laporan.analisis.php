<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

// Set default filter (current month)
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-t');
$kelas_id = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;

// Query untuk analisis absensi
$query = "SELECT 
            DATE(a.tanggal) as tanggal,
            k.id as kelas_id,
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
          " . ($kelas_id > 0 ? " AND s.kelas_id = ?" : "") . "
          GROUP BY DATE(a.tanggal), k.id
          ORDER BY a.tanggal, k.nama_kelas";

$stmt = mysqli_prepare($conn, $query);

if($kelas_id > 0) {
    mysqli_stmt_bind_param($stmt, 'ssi', $tgl_awal, $tgl_akhir, $kelas_id);
} else {
    mysqli_stmt_bind_param($stmt, 'ss', $tgl_awal, $tgl_akhir);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Get classes for filter dropdown
$kelas_list = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Analisis Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="../../style.css" rel="stylesheet">
    <style>
        .card-header { background-color: #4361ee; color: white; }
        .badge-hadir { background-color: #28a745; }
        .badge-izin { background-color: #ffc107; color: #212529; }
        .badge-sakit { background-color: #17a2b8; }
        .badge-alpa { background-color: #dc3545; }
        .progress { height: 25px; }
        .progress-bar { line-height: 25px; }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    <?php include '../sidebar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="bi bi-bar-chart"></i> Laporan Analisis Absensi</h1>
            <div>
                <a href="export_analisis.php?tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>&kelas_id=<?= $kelas_id ?>" 
                   class="btn btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
                <a href="../dashboard.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Laporan</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="tgl_awal" class="form-label">Tanggal Awal</label>
                        <input type="date" class="form-control" name="tgl_awal" value="<?= $tgl_awal ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="tgl_akhir" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" name="tgl_akhir" value="<?= $tgl_akhir ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="kelas_id" class="form-label">Kelas</label>
                        <select class="form-select" name="kelas_id">
                            <option value="0">Semua Kelas</option>
                            <?php while($row = mysqli_fetch_assoc($kelas_list)): ?>
                                <option value="<?= $row['id'] ?>" <?= $kelas_id == $row['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['nama_kelas']) ?> (<?= htmlspecialchars($row['jurusan']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-table"></i> Data Analisis</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    Periode: <?= date('d M Y', strtotime($tgl_awal)) ?> - <?= date('d M Y', strtotime($tgl_akhir)) ?>
                    <?php if($kelas_id > 0): 
                        $kelas_nama = mysqli_fetch_assoc(mysqli_query($conn, 
                            "SELECT CONCAT(nama_kelas, ' ', jurusan) as nama FROM kelas WHERE id = $kelas_id"));
                    ?>
                        | Kelas: <?= htmlspecialchars($kelas_nama['nama']) ?>
                    <?php endif; ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Tanggal</th>
                                <th>Kelas</th>
                                <th width="8%">Hadir</th>
                                <th width="8%">Izin</th>
                                <th width="8%">Sakit</th>
                                <th width="8%">Alpa</th>
                                <th width="8%">Total</th>
                                <th width="15%">% Hadir</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($row['nama_kelas']) ?> (<?= htmlspecialchars($row['jurusan']) ?>)</td>
                                    <td><span class="badge badge-hadir"><?= $row['total_hadir'] ?></span></td>
                                    <td><span class="badge badge-izin"><?= $row['total_izin'] ?></span></td>
                                    <td><span class="badge badge-sakit"><?= $row['total_sakit'] ?></span></td>
                                    <td><span class="badge badge-alpa"><?= $row['total_alpa'] ?></span></td>
                                    <td><strong><?= $row['total'] ?></strong></td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: <?= $row['persen_hadir'] ?>%" 
                                                 aria-valuenow="<?= $row['persen_hadir'] ?>">
                                                <?= $row['persen_hadir'] ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="detail_absensi.php?tanggal=<?= urlencode($row['tanggal']) ?>&kelas_id=<?= $row['kelas_id'] ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center py-4">Tidak ada data absensi untuk periode ini</td>
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
