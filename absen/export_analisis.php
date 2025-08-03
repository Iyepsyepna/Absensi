<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-t');
$kelas_id = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;

// Query yang sama dengan laporan
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

// Set headers for Excel file
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Analisis_Absensi_".date('Ymd').".xls\"");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h3 style="text-align: center;">ANALISIS DATA ABSENSI</h3>
    <p style="text-align: center;">
        Periode: <?= date('d/m/Y', strtotime($tgl_awal)) ?> - <?= date('d/m/Y', strtotime($tgl_akhir)) ?>
    </p>

    <table border="1" width="100%">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>No</th>
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
                <td><?= $no++ ?></td>
                <td><?= $row['tingkat'] . ' ' . $row['nama_kelas'] . ' (' . $row['jurusan'] . ')' ?></td>
                <td><?= $row['total_hadir'] ?></td>
                <td><?= $row['total_izin'] ?></td>
                <td><?= $row['total_sakit'] ?></td>
                <td><?= $row['total_alpa'] ?></td>
                <td><?= $row['total'] ?></td>
                <td><?= $row['persen_hadir'] ?>%</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
        <p>Oleh: <?= $_SESSION['nama'] ?></p>
    </div>
</body>
</html>
