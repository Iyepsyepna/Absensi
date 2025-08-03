<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

// Get school data
$sekolah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM sekolah LIMIT 1"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Sekolah - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .profile-header {
            background-color: #4361ee;
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        .profile-logo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 5px solid white;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .info-card {
            border-left: 4px solid #4361ee;
            transition: all 0.3s;
        }
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
    <link href="../style.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <?php include '../sidebar.php'; ?>

    <main class="col-md-9 ms-sm-9 col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Profil Sekolah</h1>
            <?php if($_SESSION['role'] == 'admin'): ?>
                <a href="edit.php" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit Profil
                </a>
            <?php endif; ?>
        </div>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="profile-header text-center">
                <?php if(!empty($sekolah['logo'])): ?>
                    <img src="../img/Logo_NEPAT.jpg<?= $sekolah['logo'] ?>" class="profile-logo mb-3" alt="Logo Sekolah">
                <?php else: ?>
                    <img src="https://absen.c-test.my.id/img/Logo_NEPAT.png" class="profile-logo mb-3" alt="Logo Sekolah">
                <?php endif; ?>
                <h3><?= htmlspecialchars($sekolah['nama_sekolah'] ?? 'Nama Sekolah') ?></h3>
                <p class="mb-0"><?= htmlspecialchars($sekolah['npsn'] ?? '') ?> | <?= htmlspecialchars($sekolah['nss'] ?? '') ?></p>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card info-card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-building"></i> Identitas Sekolah</h5>
                                <hr>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Nama Sekolah</h6>
                                    <p><?= htmlspecialchars($sekolah['nama_sekolah'] ?? '-') ?></p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">NPSN</h6>
                                    <p><?= htmlspecialchars($sekolah['npsn'] ?? '-') ?></p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Akreditasi</h6>
                                    <p><?= htmlspecialchars($sekolah['nss'] ?? '-') ?></p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Alamat</h6>
                                    <p><?= htmlspecialchars($sekolah['alamat'] ?? '-') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card info-card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-telephone"></i> Kontak & Informasi</h5>
                                <hr>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Kepala Sekolah</h6>
                                    <p><?= htmlspecialchars($sekolah['kepala_sekolah'] ?? '-') ?></p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">NIP Kepala Sekolah</h6>
                                    <p><?= htmlspecialchars($sekolah['nip_kepala_sekolah'] ?? '-') ?></p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Telepon</h6>
                                    <p><?= htmlspecialchars($sekolah['telepon'] ?? '-') ?></p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Email</h6>
                                    <p><?= htmlspecialchars($sekolah['email'] ?? '-') ?></p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Website</h6>
                                    <p>
                                        <?php if(!empty($sekolah['website'])): ?>
                                            <a href="<?= htmlspecialchars($sekolah['website']) ?>" target="_blank">
                                                <?= htmlspecialchars($sekolah['website']) ?>
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
