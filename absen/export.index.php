<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

// Get the date range from the URL parameters
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

// Set headers for Excel file
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="absensi_' . $tgl_awal . '_to_' . $tgl_akhir . '.xls"');

// Output the Excel file
echo "<table border='0' width='100%'>";
echo "<tr><td colspan='6' style='text-align: center; font-size: 26px; font-weight: bold;'>Data Absensi Siswa</td></tr>";
echo "</table>";

// Add three empty rows for spacing
echo "<br><br><br>";

echo "<table border='1' width='100%'>";
echo "<tr>
        <th>Tanggal</th>
        <th>Nama Siswa</th>
        <th>Kelas</th>
        <th>Status</th>
        <th>Jam</th>
        <th>Di Input Oleh</th>
      </tr>";

// Write data rows
while($row = mysqli_fetch_assoc($absensi)) {
    $status = [
        'H' => 'Hadir',
        'I' => 'Izin', 
        'S' => 'Sakit',
        'A' => 'Alpa'
    ];
    
    $user_id = [
        '1' => 'Kepala Sekolah',
        '2' => 'Guru', 
        '3' => 'Wakasek',
        '4' => 'Wakasek',
        '5' => 'Iyep Syepna, S.Pd.'
    ];

    // Determine the background color based on status
    $bg_color = '';
    switch ($row['status']) {
        case 'H':
            $bg_color = '#d4edda'; // Light green for Hadir
            break;
        case 'I':
            $bg_color = '#fff3cd'; // Light yellow for Izin
            break;
        case 'S':
            $bg_color = '#cce5ff'; // Light blue for Sakit
            break;
        case 'A':
            $bg_color = '#f8d7da'; // Light red for Alpa
            break;
    }

    echo "<tr style='background-color: $bg_color;'>
            <td>" . date('d/m/Y', strtotime($row['tanggal'])) . ' ' . date('H:i', strtotime($row['created_at'])) . "</td>
            <td>" . $row['nama_siswa'] . "</td>
            <td>" . $row['tingkat'] . ' ' . $row['nama_kelas'] . "</td>
            <td>" . $status[$row['status']] . "</td>
            <td>" . date('H:i', strtotime($row['created_at'])) . "</td>
            <td>" . $user_id[$row['user_id']] . "</td>
          </tr>";
}

echo "</table>";
exit;
?>
