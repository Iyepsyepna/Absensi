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

// Get report data
$query = "SELECT 
            s.nis,
            s.nama as nama_siswa,
            CONCAT(k.tingkat, ' ', k.nama_kelas) as kelas,
            COUNT(CASE WHEN a.status = 'H' THEN 1 END) as hadir,
            COUNT(CASE WHEN a.status = 'I' THEN 1 END) as izin,
            COUNT(CASE WHEN a.status = 'S' THEN 1 END) as sakit,
            COUNT(CASE WHEN a.status = 'A' THEN 1 END) as alpa,
            COUNT(*) as total,
            ROUND(COUNT(CASE WHEN a.status = 'H' THEN 1 END) * 100 / COUNT(*), 2) as persen_hadir
          FROM siswa s
          LEFT JOIN absensi a ON s.id = a.siswa_id AND DATE(a.tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
          JOIN kelas k ON s.kelas_id = k.id
          WHERE ($kelas_id = 0 OR s.kelas_id = $kelas_id)
          GROUP BY s.id
          ORDER BY k.tingkat, k.nama_kelas, s.nama";

$data = mysqli_query($conn, $query);

// Generate Excel file
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Laporan_Absensi_".date('Ymd').".xls\"");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th colspan="8" style="text-align: center; font-size: 16px;">
                    LAPORAN ABSENSI SISWA<br>
                    Periode: <?= date('d/m/Y', strtotime($tgl_awal)) ?> - <?= date('d/m/Y', strtotime($tgl_akhir)) ?>
                </th>
            </tr>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
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
            <?php $no = 1; while($row = mysqli_fetch_assoc($data)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['nis'] ?></td>
                <td><?= $row['nama_siswa'] ?></td>
                <td><?= $row['kelas'] ?></td>
                <td><?= $row['hadir'] ?></td>
                <td><?= $row['izin'] ?></td>
                <td><?= $row['sakit'] ?></td>
                <td><?= $row['alpa'] ?></td>
                <td><?= $row['total'] ?></td>
                <td><?= $row['persen_hadir'] ?>%</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
