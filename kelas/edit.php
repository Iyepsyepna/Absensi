<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

if(!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
$kelas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kelas WHERE id = $id"));

if(!$kelas) {
    $_SESSION['error'] = "Data kelas tidak ditemukan";
    header('Location: index.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kelas = mysqli_real_escape_string($conn, $_POST['nama_kelas']);
    $tingkat = $_POST['tingkat'];
    $jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);
    $wali_kelas = mysqli_real_escape_string($conn, $_POST['wali_kelas']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // Check if class already exists (excluding current)
    $check = mysqli_query($conn, "SELECT id FROM kelas WHERE nama_kelas = '$nama_kelas' AND tingkat = '$tingkat' AND id != $id");
    if(mysqli_num_rows($check) > 0) {
        $error = "Kelas dengan nama dan tingkat yang sama sudah ada!";
    } else {
        $query = "UPDATE kelas SET 
                 nama_kelas = '$nama_kelas',
                 tingkat = '$tingkat',
                 jurusan = '$jurusan',
                 wali_kelas = '$wali_kelas',
                 keterangan = '$keterangan'
                 WHERE id = $id";
        
        if(mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Data kelas berhasil diperbarui";
            header('Location: index.php');
            exit;
        } else {
            $error = "Gagal memperbarui data: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kelas - Sistem Absensi</title>
    <link href="../style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Edit Data Kelas</h1>
            <a href="index.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_kelas" 
                                   value="<?= htmlspecialchars($kelas['nama_kelas']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                            <select class="form-select" name="tingkat" required>
                                <option value="7" <?= $kelas['tingkat'] == '7' ? 'selected' : '' ?>>Kelas 7</option>
                                <option value="8" <?= $kelas['tingkat'] == '8' ? 'selected' : '' ?>>Kelas 8</option>
                                <option value="9" <?= $kelas['tingkat'] == '9' ? 'selected' : '' ?>>Kelas 9</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="jurusan" 
                                   value="<?= htmlspecialchars($kelas['jurusan']) ?>" list="jurusanList" required>
                            <datalist id="jurusanList">
                                <option value="IPA">
                                <option value="IPS">
                                <option value="Bahasa">
                                <option value="Agama">
                                <option value="Teknik">
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Wali Kelas</label>
                            <input type="text" class="form-control" name="wali_kelas"
                                   value="<?= htmlspecialchars($kelas['wali_kelas']) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"><?= htmlspecialchars($kelas['keterangan']) ?></textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                        <a href="index.php" class="btn btn-secondary ms-2">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
