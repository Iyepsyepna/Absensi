<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if username already exists
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    if(mysqli_num_rows($check) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $query = "INSERT INTO users (username, password, nama, role) 
                 VALUES ('$username', '$password', '$nama', '$role')";
        
        if(mysqli_query($conn, $query)) {
            $_SESSION['success'] = "User baru berhasil ditambahkan";
            header('Location: index.php');
            exit;
        } else {
            $error = "Gagal menambahkan user: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .password-toggle { cursor: pointer; }
    </style>
    <link href="../style.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <?php include '../sidebar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Tambah User Baru</h1>
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
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Pilih Role</option>
                                <option value="admin">Admin</option>
                                <option value="guru">Guru</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <span class="input-group-text password-toggle" id="togglePassword">
                                    <i class="bi bi-eye-slash" id="toggleIcon"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-outline-secondary me-md-2">
                                <i class="bi bi-x-circle"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save"></i> Simpan User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Password toggle visibility
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const toggleIcon = document.querySelector('#toggleIcon');
    
    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        toggleIcon.classList.toggle('bi-eye');
        toggleIcon.classList.toggle('bi-eye-slash');
    });

    // Password confirmation check
    const confirm_password = document.querySelector('#confirm_password');
    const passwordMatch = document.querySelector('#passwordMatch');
    
    confirm_password.addEventListener('input', function() {
        if(password.value !== confirm_password.value) {
            passwordMatch.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Password tidak cocok</span>';
        } else {
            passwordMatch.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Password cocok</span>';
        }
    });

    // Password strength validation
    password.addEventListener('input', function() {
        const passwordHelp = document.querySelector('#password + .form-text');
        
        // Simple strength check (you can enhance this)
        if(password.value.length < 8) {
            passwordHelp.innerHTML = '<span class="text-danger">Password terlalu pendek (min 8 karakter)</span>';
        } else {
            passwordHelp.innerHTML = '<span class="text-success">Password cukup kuat</span>';
        }
    });

    // Form validation
    document.getElementById('userForm').addEventListener('submit', function(e) {
        if(password.value !== confirm_password.value) {
            e.preventDefault();
            alert('Password dan konfirmasi password harus sama!');
            return false;
        }
        
        if(password.value.length < 8) {
            e.preventDefault();
            alert('Password minimal 8 karakter!');
            return false;
        }
        
        return true;
    });
</script>