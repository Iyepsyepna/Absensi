<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

// Get current school data
$sekolah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM sekolah LIMIT 1"));

// Process form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_sekolah = mysqli_real_escape_string($conn, $_POST['nama_sekolah']);
    $nss = mysqli_real_escape_string($conn, $_POST['nss']);
    $npsn = mysqli_real_escape_string($conn, $_POST['npsn']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);
    $kabupaten = mysqli_real_escape_string($conn, $_POST['kabupaten']);
    $provinsi = mysqli_real_escape_string($conn, $_POST['provinsi']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $website = mysqli_real_escape_string($conn, $_POST['website']);
    $kepala_sekolah = mysqli_real_escape_string($conn, $_POST['kepala_sekolah']);
    $nip_kepala_sekolah = mysqli_real_escape_string($conn, $_POST['nip_kepala_sekolah']);

    // Handle logo upload
    $logo = $sekolah['logo'] ?? null;
    if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $new_filename = 'logo_' . uniqid() . '.' . $ext;
        $target_path = "../uploads/logo/" . $new_filename;
        
        // Allowed file types
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if(in_array(strtolower($ext), $allowed)) {
            if(move_uploaded_file($_FILES['logo']['tmp_name'], $target_path)) {
                // Delete old logo if exists
                if(!empty($logo)) {
                    @unlink("../uploads/logo/" . $logo);
                }
                $logo = $new_filename;
            } else {
                $error = "Gagal mengunggah logo";
            }
        } else {
            $error = "Format file tidak didukung. Gunakan JPG, PNG, atau GIF";
        }
    }

    if(!isset($error)) {
        if($sekolah) {
            // Update existing record
            $query = "UPDATE sekolah SET 
                     nama_sekolah = '$nama_sekolah',
                     nss = '$nss',
                     npsn = '$npsn',
                     alamat = '$alamat',
                     kecamatan = '$kecamatan',
                     kabupaten = '$kabupaten',
                     provinsi = '$provinsi',
                     telepon = '$telepon',
                     email = '$email',
                     website = '$website',
                     kepala_sekolah = '$kepala_sekolah',
                     nip_kepala_sekolah = '$nip_kepala_sekolah',
                     logo = '$logo'";
        } else {
            // Insert new record
            $query = "INSERT INTO sekolah (
                     nama_sekolah, nss, npsn, alamat, kecamatan, 
                     kabupaten, provinsi, telepon, email, website, 
                     kepala_sekolah, nip_kepala_sekolah, logo) 
                     VALUES (
                     '$nama_sekolah', '$nss', '$npsn', '$alamat', '$kecamatan', 
                     '$kabupaten', '$provinsi', '$telepon', '$email', '$website', 
                     '$kepala_sekolah', '$nip_kepala_sekolah', '$logo')";
        }

        if(mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Data sekolah berhasil diperbarui";
            header('Location: index.php');
            exit;
        } else {
            $error = "Gagal menyimpan data: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Sekolah - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .logo-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #dee2e6;
            border-radius: 50%;
            margin-bottom: 15px;
        }
        .form-label {
            font-weight: 500;
        }
    </style>
    <link href="../style.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <?php include '../sidebar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Edit Profil Sekolah</h1>
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
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                <?php if(!empty($sekolah['logo'])): ?>
                                    <img src="../uploads/logo/<?= $sekolah['logo'] ?>" class="logo-preview" id="logo-preview">
                                <?php else: ?>
                                    <img src="https://placehold.co/150x150?text=LOGO" class="logo-preview" id="logo-preview">
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo Sekolah</label>
                                <input class="form-control" type="file" id="logo" name="logo" accept="image/*">
                                <div class="form-text">Ukuran maks. 2MB (JPG/PNG)</div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nama_sekolah" class="form-label">Nama Sekolah</label>
                                    <input type="text" class="form-control" id="nama_sekolah" name="nama_sekolah" 
                                           value="<?= htmlspecialchars($sekolah['nama_sekolah'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nss" class="form-label">NSS</label>
                                    <input type="text" class="form-control" id="nss" name="nss" 
                                           value="<?= htmlspecialchars($sekolah['nss'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="npsn" class="form-label">NPSN</label>
                                    <input type="text" class="form-control" id="npsn" name="npsn" 
                                           value="<?= htmlspecialchars($sekolah['npsn'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="telepon" class="form-label">Telepon</label>
                                    <input type="text" class="form-control" id="telepon" name="telepon" 
                                           value="<?= htmlspecialchars($sekolah['telepon'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="2" required><?= htmlspecialchars($sekolah['alamat'] ?? '') ?></textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="kecamatan" class="form-label">Kecamatan</label>
                                    <input type="text" class="form-control" id="kecamatan" name="kecamatan" 
                                           value="<?= htmlspecialchars($sekolah['kecamatan'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="kabupaten" class="form-label">Kabupaten/Kota</label>
                                    <input type="text" class="form-control" id="kabupaten" name="kabupaten" 
                                           value="<?= htmlspecialchars($sekolah['kabupaten'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="provinsi" class="form-label">Provinsi</label>
                                    <input type="text" class="form-control" id="provinsi" name="provinsi" 
                                           value="<?= htmlspecialchars($sekolah['provinsi'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($sekolah['email'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" class="form-control" id="website" name="website" 
                                           value="<?= htmlspecialchars($sekolah['website'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="kepala_sekolah" class="form-label">Kepala Sekolah</label>
                                    <input type="text" class="form-control" id="kepala_sekolah" name="kepala_sekolah" 
                                           value="<?= htmlspecialchars($sekolah['kepala_sekolah'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="nip_kepala_sekolah" class="form-label">NIP Kepala Sekolah</label>
                                    <input type="text" class="form-control" id="nip_kepala_sekolah" name="nip_kepala_sekolah" 
                                           value="<?= htmlspecialchars($sekolah['nip_kepala_sekolah'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                        <a href="index.php" class="btn btn-secondary px-4 ms-2">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview logo when selected
        document.getElementById('logo').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logo-preview').src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>
