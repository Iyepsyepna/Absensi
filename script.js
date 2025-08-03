// Fungsi untuk menampilkan konfirmasi sebelum logout
document.addEventListener('DOMContentLoaded', function() {
    // Tambahkan event listener untuk tombol logout
    const logoutBtn = document.getElementById('logout-btn');
    if(logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if(confirm('Apakah Anda yakin ingin logout?')) {
                window.location.href = this.href;
            }
        });
    }

    // Format tanggal untuk input
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.value = new Date().toISOString().substr(0, 10);
    });

    // Toggle sidebar pada mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    if(sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('main').classList.toggle('active');
        });
    }

    // Animasi untuk tombol
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});

// Fungsi untuk menampilkan loading spinner
function showLoading() {
    const spinner = document.createElement('div');
    spinner.className = 'loading-spinner';
    spinner.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    `;
    document.body.appendChild(spinner);
}

// Fungsi untuk menyembunyikan loading spinner
function hideLoading() {
    const spinner = document.querySelector('.loading-spinner');
    if(spinner) {
        spinner.remove();
    }
}

// Event listener untuk form submit
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        showLoading();
    });
});
