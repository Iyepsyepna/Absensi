<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

// Handle success/error messages
if(isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if(isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Fetch all users except current user
$query = "SELECT * FROM users WHERE id != {$_SESSION['user_id']} ORDER BY role, nama";
$users = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .user-card {
            border-left: 4px solid #4361ee;
            transition: all 0.3s;
        }
        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .role-admin { border-left-color: #4361ee; }
        .role-guru { border-left-color: #3a0ca3; }
        .role-staff { border-left-color: #f72585; }
        .profile-pic {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
    <link href="../style.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <?php include '../sidebar.php'; ?>

    <main class="col-md-9 ms-sm-9 col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Manajemen Pengguna</h1>
            <div>
                <a href="tambah.php" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Tambah User
                </a>
            </div>
        </div>

        <?php if(isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php while($user = mysqli_fetch_assoc($users)):
                $role_class = 'role-' . strtolower($user['role']);
            ?>
            <div class="col-md-6 mb-4">
                <div class="card user-card h-100 <?= $role_class ?>">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <img src="https://placehold.co/100x100?text=<?= strtoupper(substr($user['nama'], 0, 1)) ?>" 
                                     class="profile-pic me-3" alt="User Profile">
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">
                                    <?= $user['nama'] ?>
                                    <span class="badge bg-primary float-end">
                                        <?= strtoupper($user['role']) ?>
                                    </span>
                                </h5>
                                <p class="card-text text-muted mb-1">
                                    <i class="bi bi-person-badge"></i> <?= $user['username'] ?>
                                </p>
                                <p class="card-text text-muted mb-1">
                                    <i class="bi bi-calendar"></i> Terdaftar: <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                                </p>

                                <div class="btn-group mt-2">
                                    <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="hapus.php?id=<?= $user['id'] ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Hapus user ini?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
