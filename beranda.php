<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])){
    header('location: login.php');
    exit();
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - E-GreenShell</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f0f4f8;
        }

        .main-content {
            flex: 1;
            margin-top: 60px; /* Jarak dari navbar */
            margin-left: 256px; /* Jarak untuk memberi ruang bagi sidebar */
            transition-all: 0.3s ease; /* Animasi transisi */
        }
        @media (max-width: 768px) {
        .main-content {
            margin-left: 0; /* Menghapus margin kiri pada layar kecil */
        }
        .sidebar1 {
            position: static; /* Membuat sidebar mengikuti alur halaman */
            width: 100%;
        }
    }

        nav {
            top: 0;
            z-index: 10;
        }

        .sidebar1 {
            position: fixed;
            margin-top: 70px;
            left: 0;
            width: 256px;
            height: 100%;

        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: #cbd5e0;
            padding: 12px;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #2b6cb0;
            color: white;
        }

        .content-section {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        footer {
            background-color: #2d3748;
            color: #e2e8f0;
            padding: 15px 0;
            text-align: center;
            margin-top: auto; /* memastikan footer tetap di bawah */
        }

        footer a {
            color: #f9a825;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: #1cc88a;
        }

        .sidebar-open .main-content {
            margin-left: 256px;
        }

        #sidebarToggle {
            z-index: 30; /* memastikan tombol tetap di atas */
        }
        .username {
        color: #ffcc00; /* Warna kuning keemasan */
        background-color: #2d3748; /* Latar belakang gelap */
        padding: 4px 8px; /* Padding untuk jarak */
        border-radius: 5px; /* Membuat sudut melengkung */
        font-weight: bold; /* Membuat teks tebal */
        }
        /* Card Hover Effects */
        .feature-card:hover {
            transform: scale(1.05);
        }

        /* Image styles */
        img {
            transition: all 0.3s ease-in-out;
        }

        /* Section header styling */
        .content-section h2 {
            color: #1D4ED8; /* Blue */
            font-weight: 600;
        }

        /* Section paragraph styling */
        .content-section p {
            color: #4B5563; /* Gray */
            line-height: 1.6;
        }
    </style>
</head>
<body>
<div>
    <nav class="bg-gradient-to-r from-blue-900 to-green-700 p-5 shadow-lg flex justify-between items-center fixed w-full z-50">
        <button id="sidebarToggle" class="text-white text-3xl">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="text-3xl font-extrabold text-white tracking-wide">E-GreenShell</h1>
    </nav>
</div>

<!-- Sidebar navigasi -->
<div class="sidebar1">
    <div id="sidebar" class="bg-gray-800 w-64 min-h-screen p-4 transform -translate-x-full transition-transform duration-300 fixed top-60px left-0 z-40">
        <div class="text-center mb-6">
            <p class="username">
                <?php echo htmlspecialchars($_SESSION['username']); ?>
            </p>
        </div>
        <nav class="space-y-4 text-lg text-white">
            <a href="beranda.php" class="block hover:bg-blue-700">
                <i class="fas fa-home"></i>
                <span>Beranda</span>
            </a>
            <a href="dashboard.php" class="block hover:bg-blue-700">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="berita.php" class="block hover:bg-blue-700">
                <i class="fas fa-newspaper"></i>
                <span>Berita</span>
            </a>
            <a href="profil.php" class="block hover:bg-blue-700">
                <i class="fas fa-user"></i>
                <span>Profil</span>
            </a>
            <a href="rencana_penangkapan.php" class="block hover:bg-blue-700">
                <i class="fas fa-fish"></i>
                <span>Rencana Penangkapan</span>
            </a>
            <a href="hasil_penangkapan.php" class="block hover:bg-blue-700">
                <i class="fas fa-clipboard-list"></i>
                <span>Hasil Penangkapan</span>
            </a>
            <a href="logout.php" class="block hover:bg-blue-700">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>
</div>

<!-- Konten utama -->
<div class="main-content p-8 ml-0 transition-all duration-300 fade-in">
    <h1 class="text-4xl font-bold mb-6 text-blue-900">Selamat Datang di E-GreenShell</h1>
    <p class="text-xl text-gray-600 mb-8">Portal Informasi Budidaya Kerang Hijau di Banten</p>

    <!-- Deskripsi -->
    <section class="content-section mb-8">
        <h2 class="text-3xl font-semibold text-blue-800 mb-4">Mengapa Kerang Hijau Penting?</h2>
        <p class="text-lg text-gray-700 mb-4">
            Kerang hijau adalah salah satu spesies kerang yang tidak hanya lezat dan bergizi tinggi, tetapi juga menjadi sumber mata pencaharian penting bagi masyarakat pesisir. Kaya akan protein, vitamin, dan mineral, kerang hijau memiliki manfaat kesehatan yang luar biasa. Budidaya kerang hijau juga membantu menjaga keseimbangan ekosistem laut karena kerang ini berperan sebagai penyaring alami air laut, meningkatkan kualitas perairan dan mendukung keberlanjutan ekosistem.
        </p>
        <img src="img/SS1.png" alt="Kerang Hijau" class="rounded-lg shadow-lg w-full md:w-1/2 mx-auto">
    </section>

    <!-- Potensi Perairan Banten -->
    <section class="content-section mb-8">
        <h2 class="text-3xl font-semibold text-blue-800 mb-4">Potensi Perairan Banten untuk Budidaya</h2>
        <p class="text-lg text-gray-700">
            Banten memiliki garis pantai yang luas dan kondisi perairan yang mendukung pertumbuhan kerang hijau, seperti suhu air yang stabil, ketersediaan plankton, dan kualitas air yang baik. Hal ini membuat Banten menjadi lokasi yang ideal untuk pengembangan budidaya kerang hijau yang berkelanjutan.
        </p>
    </section>

    <!-- Fitur Utama -->
    <div class="mt-10">
        <h2 class="text-2xl font-semibold text-blue-900 mb-6">Fitur Utama</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="feature-card bg-white p-6 rounded-lg shadow-lg transition-transform transform hover:scale-105">
                <h3 class="text-xl font-semibold text-blue-800 mb-3">Manajemen Hasil Penangkapan</h3>
                <p class="text-gray-600 mb-4">Melakukan pencatatan dan laporan hasil penangkapan kerang hijau dari para nelayan untuk analisis dan pelaporan.</p>
                <img src="img/SS2.png" alt="Penangkapan Kerang Hijau" class="w-full rounded-lg shadow-md">
            </div>

            <div class="feature-card bg-white p-6 rounded-lg shadow-lg transition-transform transform hover:scale-105">
                <h3 class="text-xl font-semibold text-blue-800 mb-3">Rencana Penangkapan</h3>
                <p class="text-gray-600 mb-4">Mengelola jadwal dan lokasi penangkapan untuk memastikan keberlanjutan hasil tangkapan dan kualitas kerang hijau.</p>
                <img src="img/SS3.png" alt="Rencana Penangkapan" class="w-full rounded-lg shadow-md">
            </div>

            <div class="feature-card bg-white p-6 rounded-lg shadow-lg transition-transform transform hover:scale-105">
                <h3 class="text-xl font-semibold text-blue-800 mb-3">Biodata Nelayan</h3>
                <p class="text-gray-600 mb-4">Menampilkan profil nelayan yang terlibat dalam budidaya kerang hijau untuk memudahkan komunikasi dan pengelolaan data.</p>
                <img src="img/SS5.png" alt="Biodata Nelayan" class="w-full rounded-lg shadow-md">
            </div>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2024 E-GreenShell. Semua hak dilindungi.</p>
</footer>

<script>
    document.getElementById("sidebarToggle").addEventListener("click", function() {
    document.getElementById("sidebar").classList.toggle("-translate-x-full");
        });
</script>
</body>
</html>
