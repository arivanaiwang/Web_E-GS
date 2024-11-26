<?php
include 'config.php'; // Menggunakan konfigurasi MySQLi
session_start();

// Memeriksa apakah pengguna telah login
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit();
}

// Mengambil jumlah pengguna dari tabel `users`
$sql = "SELECT COUNT(*) AS jumlah_pengguna FROM users";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $jumlahPengguna = $row['jumlah_pengguna'];
} else {
    echo "Query gagal: " . $conn->error;
}

// Mengambil jumlah hasil dari tabel `hasil_penangkapan`
$sql = "SELECT COUNT(*) AS jumlah_hasil FROM hasil_penangkapan";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $jumlahHasil = $row['jumlah_hasil'];
} else {
    echo "Query gagal: " . $conn->error;
}

// Mengambil jumlah rencana dari tabel `rencana_penangkapan`
$sql = "SELECT COUNT(*) AS jumlah_rencana FROM rencana_penangkapan";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $jumlahRencana = $row['jumlah_rencana'];
} else {
    echo "Query gagal: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - E-GreenShell</title>
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
            margin-top: 60px;
            margin-left: 0;
        }

    .sidebar-open #sidebar {
        transform: translateX(0);
    }

    .sidebar-open .main-content {
        margin-left: 256px; /* Menambahkan margin kiri saat sidebar dibuka */
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
    .sidebar1 {
        margin-top: 70px;
        left: 0;
        width: 256px;
        height: 100%;
        background-color: #2d3748;
        color: white;
    }
    #sidebarToggle {
        z-index: 30;
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
    <div class="main-content p-8 ml-0 duration-300">
        <h1 class="text-4xl font-bold text-gray-900 mb-6">Dashboard</h1>
        <p class="flex items-center text-gray-600 mb-6 bg-blue-100 p-4 rounded-md shadow-inner">
            <i class="fas fa-info-circle text-blue-600 mr-3"></i> <!-- Ikon menarik -->
            <span>Selamat datang di dashboard Anda. Di sini Anda dapat melihat ringkasan informasi dan aktivitas terbaru.</span>
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Kartu pertama -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-700 text-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition duration-300">
                <div class="flex items-center mb-4">
                    <i class="fas fa-users text-3xl mr-3"></i> <!-- Ikon untuk Total Pengguna -->
                    <h2 class="text-xl font-semibold">Total Pengguna</h2>
                </div>
                <p class="text-3xl font-bold mt-3"><?=$jumlahPengguna;?></p>
            </div>
            <!-- Kartu kedua -->
            <div class="bg-gradient-to-r from-green-500 to-green-700 text-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition duration-300">
                <div class="flex items-center mb-4">
                    <i class="fas fa-fish text-3xl mr-3"></i> <!-- Ikon untuk Total Hasil Penangkapan -->
                    <h2 class="text-xl font-semibold">Total Hasil Penangkapan</h2>
                </div>
                <p class="text-3xl font-bold mt-3"><?=$jumlahHasil;?></p>
            </div>
            <!-- Kartu ketiga -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-700 text-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition duration-300">
                <div class="flex items-center mb-4">
                    <i class="fas fa-clipboard-list text-3xl mr-3"></i> <!-- Ikon untuk Total Rencana Penangkapan -->
                    <h2 class="text-xl font-semibold">Total Rencana Penangkapan</h2>
                </div>
                <p class="text-3xl font-bold mt-3"><?=$jumlahRencana;?></p>
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
