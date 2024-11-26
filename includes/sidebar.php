<link rel="stylesheet" href="css/sidebar.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<div id="sidebar" class="bg-gray-800 w-64 min-h-screen p-4 transform -translate-x-full transition-transform duration-300 fixed">
    <h2 class="text-3xl font-semibold mb-6 text-white">E-GreenShell</h2>
    <nav class="space-y-4 text-lg">
        <a class="username">
            <i class="username text-lg font-bold text-gray-200"></i>
            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </a>
        <a href="beranda.php" class="block text-gray-400 hover:text-white flex items-center space-x-2">
            <i class="fas fa-home"></i>
            <span>Beranda</span>
        </a>
        <a href="dashboard.php" class="block text-gray-400 hover:text-white flex items-center space-x-2">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="berita.php" class="block text-gray-400 hover:text-white flex items-center space-x-2">
            <i class="fas fa-newspaper"></i>
            <span>Berita</span>
        </a>
        <a href="profil.php" class="block text-gray-400 hover:text-white flex items-center space-x-2">
            <i class="fas fa-user"></i>
            <span>Profil</span>
        </a>
        <a href="rencana_penangkapan.php" class="block text-gray-400 hover:text-white flex items-center space-x-2">
            <i class="fas fa-fish"></i>
            <span>Rencana Penangkapan</span>
        </a>
        <a href="hasil_penangkapan.php" class="block text-gray-400 hover:text-white flex items-center space-x-2">
            <i class="fas fa-clipboard-list"></i>
            <span>Hasil Penangkapan</span>
        </a>
        <a href="pembayaran.php" class="block text-gray-400 hover:text-white flex items-center space-x-2">
            <i class="fas fa-credit-card"></i>
            <span>Pembayaran</span>
        </a>
    </nav>
</div>
<script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            const body = document.body;
            body.classList.toggle('sidebar-closed');
            body.classList.toggle('sidebar-open');
            console.log('Sidebar toggle:', body.classList.contains('sidebar-closed'));
        });
</script>