<?php
require_once 'config.php';
session_start(); // Pastikan session sudah dimulai

// Pastikan 'user_id' ada dalam session
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // Mendapatkan user_id dari session

// Menangani submit form untuk menambahkan atau memperbarui data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_profil'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];
    $nomor_hp = $_POST['nomor_hp'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];

    // Menangani upload gambar
    $foto_profil = null;
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
        $foto_profil = 'uploads/' . basename($_FILES['foto_profil']['name']);
        move_uploaded_file($_FILES['foto_profil']['tmp_name'], $foto_profil);
    }

    // Cek apakah data sudah ada
    if (isset($_GET['edit'])) {
        $id = $_GET['edit'];
        $stmt = $conn->prepare("UPDATE biodata_nelayan SET nama = ?, email = ?, alamat = ?, nomor_hp = ?, tanggal_lahir = ?, jenis_kelamin = ?, foto_profil = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param('sssssssis', $nama, $email, $alamat, $nomor_hp, $tanggal_lahir, $jenis_kelamin, $foto_profil, $id, $user_id);
        $stmt->execute();
    } else {
        // Jika belum ada data (insert)
        $stmt = $conn->prepare("INSERT INTO biodata_nelayan (nama, email, alamat, nomor_hp, tanggal_lahir, jenis_kelamin, foto_profil, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssss', $nama, $email, $alamat, $nomor_hp, $tanggal_lahir, $jenis_kelamin, $foto_profil, $user_id);
        $stmt->execute();
    }

    header('Location: profil.php'); // Redirect setelah submit
    exit;
}

// Menangani penghapusan data
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM biodata_nelayan WHERE id = ? AND user_id = ?");
    $stmt->bind_param('is', $id, $user_id);
    $stmt->execute();
    header('Location: profil.php'); // Redirect setelah hapus
    exit;
}

// Menampilkan data untuk edit jika ada parameter edit
$biodata = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM biodata_nelayan WHERE id = ? AND user_id = ?");
    $stmt->bind_param('is', $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $biodata = $result->fetch_assoc();
}

// Mengambil semua data biodata nelayan untuk ditampilkan
$stmt = $conn->prepare("SELECT * FROM biodata_nelayan WHERE user_id = ?");
$stmt->bind_param('s', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$biodata_all = $result->fetch_all(MYSQLI_ASSOC);

// Cek jika sudah ada data dalam tabel biodata_nelayan
$formDisplayed = count($biodata_all) == 0;
?>

<!DOCTYPE html>
<html lang="en">
<body class="bg-gray-100 text-gray-900">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - E-GreenShell</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
body {
    font-family: 'Inter', sans-serif;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    margin: 0;
}

.main-content {
    flex: 1;
    overflow-y: auto;
    padding-top: 60px; /* Memberi jarak konten dari header */
    margin-left: 256px; /* Menggeser konten untuk mengakomodasi sidebar */
}

header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 10;
    background-color: #1a202c; /* Sesuaikan warna header */
    color: white;
    padding: 10px;
    text-align: center;
}

footer {
    background-color: #1a202c;
    color: white;
    text-align: center;
    padding: 10px;
    position: fixed;
    bottom: 0;
    width: 100%;
    z-index: 5;
}

#sidebar {
    background-color: #2d3748;
    color: white;
    width: 250px;
    position: fixed;
    top: 60px; /* Menempatkan sidebar tepat di bawah header */
    left: -250px;
    height: calc(100% - 60px); /* Menyesuaikan tinggi sidebar dengan tinggi header */
    transition: left 0.3s ease;
    z-index: 5;
}

#sidebar.open {
    left: 0;
}

#sidebarToggle {
    cursor: pointer;
}
    </style>
</head>
<body class="bg-gray-100 text-gray-900">
    <!-- Navbar atas -->
    <nav class="bg-blue-900 p-4 flex justify-between items-center text-white" id="header">
        <div class="flex items-center space-x-3">
            <button id="sidebarToggle" class="text-white text-2xl">
                <i class="fas fa-bars"></i>
            </button>
            <span class="text-2xl font-semibold">E-GreenShell</span>
        </div>
        <div class="flex space-x-4">
            <a href="index.html" class="text-white text-lg"><i class="fas fa-home"></i></a>
            <a href="logout.php" class="text-white text-lg"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar navigasi -->
        <div id="sidebar" class="bg-gray-800 w-64 min-h-screen p-4">
            <h2 class="text-3xl font-semibold mb-6 text-white">E-GreenShell</h2>
            <nav class="space-y-4 text-lg">
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
                <a href="logout.php" class="block hover:bg-blue-700">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </div>

        <!-- Konten Utama -->
            <!-- Konten Utama -->
    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-bold mb-6">Profil Biodata Diri</h1>

        <!-- Tampilkan Form Input Biodata jika belum ada data -->
        <?php if ($formDisplayed): ?>
        <div class="bg-white p-8 rounded shadow-md mb-6">
            <h2 class="text-2xl font-semibold">Tambah Biodata Nelayan</h2>
            <form action="profil.php" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="nama" class="block text-sm font-medium">Nama</label>
                    <input type="text" name="nama" id="nama" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" id="email" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div class="mb-4">
                    <label for="alamat" class="block text-sm font-medium">Alamat</label>
                    <input type="text" name="alamat" id="alamat" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div class="mb-4">
                    <label for="nomor_hp" class="block text-sm font-medium">Nomor HP</label>
                    <input type="text" name="nomor_hp" id="nomor_hp" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div class="mb-4">
                    <label for="tanggal_lahir" class="block text-sm font-medium">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div class="mb-4">
                    <label for="jenis_kelamin" class="block text-sm font-medium">Jenis Kelamin</label>
                    <select name="jenis_kelamin" id="jenis_kelamin" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="foto_profil" class="block text-sm font-medium">Foto Profil</label>
                    <input type="file" name="foto_profil" id="foto_profil" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <button type="submit" name="submit_profil" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Tambah Biodata</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Tampilkan Data Profil dalam bentuk Kartu Nama -->
                <!-- Tampilkan Data Profil dalam Bentuk Kartu -->
        <?php if (!$formDisplayed): ?>
            <div class="bg-white shadow-lg rounded-lg p-6 max-w-md mx-auto" style="background-image: url('img/name.jpg'); background-size: cover; background-position: center;">
                <?php foreach ($biodata_all as $row): ?>
                    <div class="flex items-center space-x-4">
                        <!-- Foto Profil -->
                        <img src="<?= $row['foto_profil'] ? $row['foto_profil'] : 'uploads/default-avatar.png' ?>" alt="Foto Profil" class="w-20 h-20 rounded-full object-cover">
                        
                        <!-- Informasi Profil -->
                        <div>
                            <h2 class="text-xl font-semibold"><?= $row['nama'] ?></h2>
                            <p class="text-sm text-gray-600"><?= $row['email'] ?></p>
                            <p class="text-sm"><?= $row['alamat'] ?></p>
                            <p class="text-sm"><?= $row['nomor_hp'] ?></p>
                            <p class="text-sm"><?= $row['tanggal_lahir'] ?></p>
                            <p class="text-sm"><?= $row['jenis_kelamin'] ?></p>
                        </div>
                    </div>
                    <hr class="my-4">
                <?php endforeach; ?>
                <div class="mt-4">
                        <a href="profil.php?edit=<?= $row['id'] ?>" class="text-blue-500">Edit</a>
                        <a href="profil.php?delete=<?= $row['id'] ?>" class="text-red-500 ml-4">Hapus</a>
                    </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-4 text-center">
        <p>&copy; 2024 E-GreenShell - Semua hak dilindungi</p>
    </footer>

    <!-- Sidebar Toggle Script -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    </script>
</body>
</html>
