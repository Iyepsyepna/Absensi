<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

// Handle delete
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM siswa WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $_SESSION['success'] = "Data siswa berhasil dihapus";
    header('Location: index.php');
    exit;
}

// Handle search
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$kelas_id = isset($_GET['kelas']) ? (int)$_GET['kelas'] : null;

$query = "SELECT s.*, k.nama_kelas FROM siswa s JOIN kelas k ON s.kelas_id = k.id";

if (!empty($keyword)) {
    $query .= " WHERE s.nis LIKE '%" . mysqli_real_escape_string($conn, $keyword) . "%' 
                OR s.nama LIKE '%" . mysqli_real_escape_string($conn, $keyword) . "%'";
}

if ($kelas_id) {
    $query .= " AND s.kelas_id = $kelas_id";
}

$query .= " ORDER BY s.nama";
$siswa = mysqli_query($conn, $query);

// Fetch all classes for filter
$kelas_query = "SELECT * FROM kelas ORDER BY tingkat, nama_kelas";
$kelas_result = mysqli_query($conn, $kelas_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Siswa</title>
    <link href="../style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <?php include '../sidebar.php'; ?>

    <main class="col-md-9 ms-sm-9 col-lg-9 px-md-4 py-4">
        <h1>Data Siswa</h1>
        
        <a href="tambah.php" class="btn btn-primary mt-3 mb-4">Tambah Siswa</a>
        <a href="../dashboard.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
        </a>

        <!-- Form Pencarian -->
        <form method="GET" class="mt-3 mb-4 search-container">
            <div class="input-group">
                <select name="kelas" class="form-select">
                    <option value="">Semua Kelas</option>
                    <?php while($k = mysqli_fetch_assoc($kelas_result)): ?>
                        <option value="<?= $k['id'] ?>" <?= (isset($_GET['kelas']) && $_GET['kelas'] == $k['id']) ? 'selected' : '' ?>>
                            Kelas <?= $k['tingkat'] . ' ' . $k['nama_kelas'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <input type="text" name="keyword" class="form-control" placeholder="Cari NIS atau Nama Siswa..." value="<?= htmlspecialchars($keyword); ?>">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
            </div>
        </form>

        <p>Jumlah siswa: <?= mysqli_num_rows($siswa) ?></p>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while($row = mysqli_fetch_assoc($siswa)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['nis']; ?></td>
                        <td><?= $row['nama']; ?></td>
                        <td><?= $row['nama_kelas']; ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="index.php?delete=<?= $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="../script.js"></script>
</body>
</html>
