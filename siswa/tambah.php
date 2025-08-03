<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis = mysqli_real_escape_string($conn, $_POST['nis']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $kelas_id = $_POST['kelas_id'];
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    // Validasi NIS unik
    $check = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$nis'");
    if(mysqli_num_rows($check) > 0) {
        $error = "NIS sudah terdaftar!";
    } else {
        $query = "INSERT INTO siswa (nis, nama, jenis_kelamin, kelas_id, alamat) 
                 VALUES ('$nis', '$nama', '$jenis_kelamin', '$kelas_id', '$alamat')";
        
        if(mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Data siswa berhasil ditambahkan";
            header('Location: index.php');
            exit;
        } else {
            $error = "Gagal menambahkan data: " . mysqli_error($conn);
        }
    }
}

$kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY tingkat, nama_kelas");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <?php include '../sidebar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h2>Tambah Data Siswa</h2>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">NIS <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nis" required maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-select" name="jenis_kelamin" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select class="form-select" name="kelas_id" required>
                                <option value="">Pilih Kelas</option>
                                <?php while($row = mysqli_fetch_assoc($kelas)): ?>
                                    <option value="<?= $row['id'] ?>">
                                        <?= $row['tingkat'] ?> <?= $row['nama_kelas'] ?> (<?= $row['jurusan'] ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
