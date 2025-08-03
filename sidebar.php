<div class="sidebar">
    <div class="text-center py-3">
        <img src="../img/BG.MERAH.jpg" class="rounded-circle border border-3 border-primary mb-2" style="width: 80px; height: auto;">
        <h6 class="mb-0"><?= $_SESSION['nama'] ?></h6>
        <small><?= $_SESSION['role'] == 'admin' ? 'Administrator' : 'Guru' ?></small>
    </div>
    
    <hr>
    
    <ul class="sidebar-menu">
        <li>
            <a href="../dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="../siswa/index.php">
                <i class="bi bi-people-fill"></i> Data Siswa
            </a>
        </li>
        <li>
            <a href="../kelas/index.php">
                <i class="bi bi-house-door-fill"></i> Data Kelas
            </a>
        </li>
        <li>
            <a href="../sekolah/index.php">
                <i class="bi bi-building"></i> Data Sekolah
            </a>
        </li>
        <li>
            <a href="../absen/index.php">
                <i class="bi bi-clipboard-check-fill"></i> Absensi
            </a>
        </li>
        <?php if($_SESSION['role'] == 'admin'): ?>
        <li>
            <a href="../user/index.php">
                <i class="bi bi-person-vcard-fill"></i> User Management
            </a>
        </li>
        <?php endif; ?>
        <li>
            <a href="../absen/laporan.analisis.php">
                <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan
            </a>
        </li>
        <li>
            <a href="../logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
        <li>
            <a href="#" class="dropdown-toggle">
                <i class="bi bi-list"></i> More Options
            </a>
            <ul class="dropdown-menu show"> <!-- Tambahkan kelas 'show' di sini -->
                <li><a href="../user/profil.php" class="dropdown-item">Profil</a></li>
                <li><a href="https://docs.google.com/spreadsheets/d/1ZIGNDOzUoHPz4umAFVKgOoXjRRdGaO3B/edit?usp=sharing&ouid=102957725295425717089&rtpof=true&sd=true" class="dropdown-item">Poin kelas 7</a></li>
                <li><a href="https://docs.google.com/spreadsheets/d/1u2ErV7j_c_-kkneLRPxwSEUPLE0Evz6k/edit?usp=sharing&ouid=102957725295425717089&rtpof=true&sd=true" class="dropdown-item">Poin kelas 8</a></li>
                <li><a href="" class="dropdown-item">---</a></li>
            </ul>
        </li>
    </ul>
</div>

<!-- Tambahkan CSS untuk dropdown dan responsivitas -->
<style>
    .sidebar {
        width: 250px; /* Lebar tetap untuk sidebar */
        max-width: 100%; /* Pastikan tidak melebihi lebar layar */
        overflow: auto; /* Tambahkan scroll jika konten melebihi tinggi */
    }

    .dropdown-menu {
        display: none; /* Sembunyikan dropdown secara default */
        position: absolute;
        background-color: white;
        border: 1px solid #ccc;
        z-index: 1000;
    }

    .dropdown-menu.show {
        display: block; /* Tampilkan dropdown jika kelas 'show' ditambahkan */
    }

    .dropdown-item {
        padding: 10px;
        text-decoration: none;
        color: black;
    }

    .dropdown-item:hover {
        background-color: #f1f1f1;
    }

    /* Media query untuk perangkat mobile */
    /* Media query untuk perangkat mobile */
    @media (max-width: 768px) {
        .sidebar {
            display: none; /* Sembunyikan sidebar pada perangkat mobile */
        }
    }
</style>

<!-- Tidak perlu JavaScript untuk dropdown -->
