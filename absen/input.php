<?php
require '../koneksi.php';
session_start();

if(!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

$kelas_id = isset($_GET['kelas']) ? (int)$_GET['kelas'] : 0;
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Jika mengedit absensi yang sudah ada
if(isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $absensi = mysqli_fetch_assoc(mysqli_query($conn, 
        "SELECT a.*, s.nama as nama_siswa, k.nama_kelas 
         FROM absensi a
         JOIN siswa s ON a.siswa_id = s.id
         JOIN kelas k ON s.kelas_id = k.id
         WHERE a.id = $edit_id"));
    
    if($absensi) {
        $kelas_id = $absensi['kelas_id'];
        $tanggal = date('Y-m-d', strtotime($absensi['tanggal']));
    }
}

// Get class data
if($kelas_id > 0) {
    $kelas = mysqli_fetch_assoc(mysqli_query($conn, 
        "SELECT * FROM kelas WHERE id = $kelas_id"));
}

// Get students in class
if($kelas_id > 0) {
    $siswa = mysqli_query($conn, 
        "SELECT * FROM siswa WHERE kelas_id = $kelas_id ORDER BY nama");
}

// Process form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kelas_id = (int)$_POST['kelas_id'];
    $tanggal = $_POST['tanggal'];
    
    if(isset($_POST['status'])) {
        foreach($_POST['status'] as $siswa_id => $status) {
            $keterangan = $_POST['keterangan'][$siswa_id] ?? '';
            
            // Check if already exists
            $check = mysqli_query($conn, 
                "SELECT id FROM absensi 
                 WHERE siswa_id = $siswa_id 
                 AND DATE(tanggal) = '$tanggal'");
                
            if(mysqli_num_rows($check) > 0) {
                $row = mysqli_fetch_assoc($check);
                $query = "UPDATE absensi SET 
                         status = '$status',
                         keterangan = '$keterangan',
                         user_id = {$_SESSION['user_id']}
                         WHERE id = {$row['id']}";
            } else {
                $query = "INSERT INTO absensi 
                         (siswa_id, status, tanggal, keterangan, user_id) 
                         VALUES 
                         ($siswa_id, '$status', '$tanggal', '$keterangan', {$_SESSION['user_id']})";
            }
            
            mysqli_query($conn, $query);
        }
        
        $_SESSION['success'] = "Data absensi berhasil disimpan";
        header("Location: index.php?tgl_awal=$tanggal&tgl_akhir=$tanggal");
        exit;
    }
}

// Fetch all classes for dropdown
$kelas_list = mysqli_query($conn, "SELECT * FROM kelas ORDER BY tingkat, nama_kelas");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Absensi - Sistem Absensi</title>
    <link href="../../style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .status-btn {
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .status-hadir { background-color: #d4edda; }
        .status-izin { background-color: #fff3cd; }
        .status-sakit { background-color: #ff7869; }
        .status-alpa { background-color: #f8d7da; }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    <?php include '../sidebar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Input Absensi Siswa</h1>
            <a href="index.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Kelas</label>
                            <select class="form-select" name="kelas" required>
                                <option value="">Pilih Kelas</option>
                                <?php while($row = mysqli_fetch_assoc($kelas_list)): ?>
                                    <option value="<?= $row['id'] ?>" 
                                        <?= $kelas_id == $row['id'] ? 'selected' : '' ?>>
                                        <?= $row['tingkat'] ?> <?= $row['nama_kelas'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" 
                                   value="<?= $tanggal ?>" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Tampilkan
                            </button>
                        </div>
                    </div>
                </form>

                <?php if($kelas_id > 0): ?>
                <form method="POST">
                    <input type="hidden" name="kelas_id" value="<?= $kelas_id ?>">
                    <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
                    
                    <div class="alert alert-info mb-3">
                        <strong>Kelas:</strong> <?= $kelas['tingkat'] ?> <?= $kelas['nama_kelas'] ?> 
                        | <strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($tanggal)) ?>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th width="15%">Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while($row = mysqli_fetch_assoc($siswa)): 
                                    // Get existing attendance if any
                                    $existing = mysqli_query($conn, 
                                        "SELECT status, keterangan FROM absensi 
                                         WHERE siswa_id = {$row['id']} 
                                         AND DATE(tanggal) = '$tanggal'");
                                    $attendance = mysqli_fetch_assoc($existing);
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nis'] ?></td>
                                    <td><?= $row['nama'] ?></td>
                                    <td>
                                        <select class="form-select form-select-sm status-select" 
                                                name="status[<?= $row['id'] ?>]"
                                                data-siswa="<?= $row['nama'] ?>">
                                            <option value="H" <?= isset($attendance) && $attendance['status'] == 'H' ? 'selected' : '' ?>>Hadir</option>
                                            <option value="I" <?= isset($attendance) && $attendance['status'] == 'I' ? 'selected' : '' ?>>Izin</option>
                                            <option value="S" <?= isset($attendance) && $attendance['status'] == 'S' ? 'selected' : '' ?>>Sakit</option>
                                            <option value="A" <?= isset($attendance) && $attendance['status'] == 'A' ? 'selected' : '' ?>>Alpa</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" 
                                               name="keterangan[<?= $row['id'] ?>]" 
                                               value="<?= isset($attendance) ? htmlspecialchars($attendance['keterangan']) : '' ?>"
                                               placeholder="Keterangan">
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save"></i> Simpan Absensi
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Dynamic status select styling
        document.querySelectorAll('.status-select').forEach(select => {
            updateStatusStyle(select);
            select.addEventListener('change', function() {
                updateStatusStyle(this);
            });
        });

        function updateStatusStyle(select) {
            const statusClasses = {
                'H': 'status-hadir',
                'I': 'status-izin', 
                'S': 'status-sakit',
                'A': 'status-alpa'
            };
            
            // Remove all status classes
            select.classList.remove(...Object.values(statusClasses));
            
            // Add current status class
            select.classList.add(statusClasses[select.value]);
        }
    </script>
</body>
</html>
