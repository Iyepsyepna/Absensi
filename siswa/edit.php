<?php
require '../koneksi.php';
session_start();

// Authorization check
if(!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

// Check if ID parameter exists
if(!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

// Fetch student data
$query = "SELECT s.*, k.nama_kelas, k.tingkat, k.jurusan 
          FROM siswa s 
          JOIN kelas k ON s.kelas_id = k.id 
          WHERE s.id = $id";
$result = mysqli_query($conn, $query);
$siswa = mysqli_fetch_assoc($result);

if(!$siswa) {
    $_SESSION['error'] = "Data siswa tidak ditemukan";
    header('Location: index.php');
    exit;
}

// Process form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $nis = mysqli_real_escape_string($conn, $_POST['nis']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $kelas_id = (int)$_POST['kelas_id'];
    

    // Validate NIS uniqueness (excluding current record)
    $check_nis = mysqli_query($conn, "SELECT id FROM siswa WHERE nis = '$nis' AND id != $id");
    if(mysqli_num_rows($check_nis) > 0) {
        $error = "NIS sudah digunakan oleh siswa lain!";
    } else {
        // Handle file upload
        $foto = $siswa['foto'];
        if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid().'.'.$ext;
            $target_path = "../uploads/siswa/".$new_filename;
            
            if(move_uploaded_file($_FILES['foto']['tmp_name'], $target_path)) {
                // Delete old photo if exists
                if(!empty($foto)) {
                    @unlink("../uploads/siswa/".$foto);
                }
                $foto = $new_filename;
            } else {
                $error = "Gagal mengunggah foto";
            }
        }

        if(!isset($error)) {
            $update_query = "UPDATE siswa SET 
                            nis = '$nis',
                            nama = '$nama',
                            jenis_kelamin = '$jenis_kelamin',
                            kelas_id = $kelas_id,
                            tempat_lahir = '$tempat_lahir',
                            tanggal_lahir = '$tanggal_lahir',
                            agama = '$agama',
                            alamat = '$alamat',
                            telepon = '$telepon',
                            nama_ortu = '$nama_ortu',
                            telepon_ortu = '$telepon_ortu',
                            foto = '$foto'
                            WHERE id = $id";

            if(mysqli_query($conn, $update_query)) {
                $_SESSION['success'] = "Data siswa berhasil diperbarui";
                header('Location: index.php?id='.$id);
                exit;
            } else {
                $error = "Gagal memperbarui data: " . mysqli_error($conn);
            }
        }
    }
}

// Fetch classes for dropdown
$kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY tingkat, nama_kelas");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Siswa - Sistem Absensi</title>
    <link href="../style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .profile-pic-container {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #dee2e6;
            margin: 0 auto 20px;
        }
        .profile-pic {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .form-label {
            font-weight: 500;
        }
        .required:after {
            content: " *";
            color: red;
        }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    <?php include '../sidebar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
            <h1 class="h2">Edit Data Siswa</h1>
            <div class="btn-toolbar">
                <a href="index.php" class="btn btn-sm btn-outline-secondary ms-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Error!</strong> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="form-container">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row mb-4">
                            <div class="col-md-12 text-center">
                                <div class="profile-pic-container">
                                    <?php if(!empty($siswa['foto'])): ?>
                                        <img src="../uploads/siswa/<?= $siswa['foto'] ?>" class="profile-pic" id="profile-pic-preview">
                                    <?php else: ?>
                                        <img src="https://placehold.co/150x150?text=Foto+Siswa" class="profile-pic" id="profile-pic-preview">
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <div class="w-75">
                                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                        <div class="form-text">Max 2MB (JPG/PNG)</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nis" class="form-label required">NIS</label>
                                <input type="text" class="form-control" id="nis" name="nis" 
                                       value="<?= htmlspecialchars($siswa['nis']) ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nama" class="form-label required">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" 
                                       value="<?= htmlspecialchars($siswa['nama']) ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label required">Jenis Kelamin</label>
                                <select class="form-select" name="jenis_kelamin" required>
                                    <option value="L" <?= $siswa['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= $siswa['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label required">Kelas</label>
                                <select class="form-select" name="kelas_id" required>
                                    <?php while($row = mysqli_fetch_assoc($kelas)): ?>
                                        <option value="<?= $row['id'] ?>" <?= $row['id'] == $siswa['kelas_id'] ? 'selected' : '' ?>>
                                            <?= $row['tingkat'] ?> <?= $row['nama_kelas'] ?> (<?= $row['jurusan'] ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                        </div>

                        

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                            <a href="index.php?id=<?= $id ?>" class="btn btn-outline-secondary px-4 ms-2">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview uploaded image
        document.getElementById('foto').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-pic-preview').src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>
