<?php
require_once 'config.php'; // Koneksi MySQLi
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id']; // Menyimpan user_id yang ada di session

function getKeteranganFromAPI($lokasi) {
    $apiUrl = "https://api.example.com/getKeterangan?lokasi=" . urlencode($lokasi);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rencana'])) {
    $tanggal = $_POST['tanggal'];
    $lokasi = $_POST['lokasi'];
    $keterangan = $_POST['keterangan'];

    if (isset($_GET['edit'])) {
        $id = $_GET['edit'];
        // Update data yang hanya milik user yang sedang login
        $sql = "UPDATE rencana_penangkapan SET tanggal = ?, lokasi = ?, keterangan = ? WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $tanggal, $lokasi, $keterangan, $id, $userId);
        $stmt->execute();
    } else {
        // Insert data dengan user_id
        $sql = "INSERT INTO rencana_penangkapan (tanggal, lokasi, keterangan, user_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $tanggal, $lokasi, $keterangan, $userId);
        $stmt->execute();
    }

    header('Location: rencana_penangkapan.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Hapus data yang hanya milik user yang sedang login
    $sql = "DELETE FROM rencana_penangkapan WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $userId);
    $stmt->execute();
    header('Location: rencana_penangkapan.php');
    exit;
}

// Jika ada parameter edit, ambil data rencana yang akan diedit
$rencanaEdit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM rencana_penangkapan WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $rencanaEdit = $result->fetch_assoc();
}

$sql = "SELECT * FROM rencana_penangkapan WHERE user_id = ? ORDER BY tanggal DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$rencanaPenangkapan = $result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rencana - E-GreenShell</title>
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

        <div class="flex-1 p-8 ml-0 transition-all duration-300 fade-in">
            <h1 class="text-4xl font-bold mb-6 text-blue-900">Rencana Penangkapan</h1>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Buat Rencana Penangkapan Baru</h2>
                <form action="rencana_penangkapan.php<?= isset($rencanaEdit) ? '?edit=' . $rencanaEdit['id'] : '' ?>" method="POST">
                    <div class="mb-4">
                        <label for="tanggal" class="block text-gray-700">Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" class="w-full p-2 mt-2 bg-gray-200 rounded" value="<?= $rencanaEdit['tanggal'] ?? '' ?>" required>
                    </div>
                    <div class="mb-4">
                        <label for="lokasi" class="block text-gray-700">Lokasi</label>
                        <input type="text" id="lokasi" name="lokasi" class="w-full p-2 mt-2 bg-gray-200 rounded" value="<?= $rencanaEdit['lokasi'] ?? '' ?>" required>
                    </div>
                    <div class="mb-4">
                        <label for="keterangan" class="block text-gray-700">Keterangan</label>
                        <input type="text" id="keterangan" name="keterangan" class="w-full p-2 mt-2 bg-gray-200 rounded" value="<?= $rencanaEdit['keterangan'] ?? '' ?>" required>
                    </div>
                    <button type="submit" name="submit_rencana" class="w-full bg-blue-500 text-white p-2 rounded mt-4"><?= isset($rencanaEdit) ? 'Edit Rencana' : 'Buat Rencana' ?></button>
                </form>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg mt-6">
                <h2 class="text-2xl font-semibold mb-4">Rencana Penangkapan Terdahulu</h2>
                <ul class="space-y-4">
                    <?php foreach ($rencanaPenangkapan as $rencana): ?>
                        <li class="bg-gray-100 p-4 rounded flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-semibold"><?= htmlspecialchars($rencana['lokasi']) ?></h3>
                                <p class="text-gray-600">Tanggal: <?= htmlspecialchars($rencana['tanggal']) ?></p>
                                <p class="text-gray-600">Keterangan: <?= htmlspecialchars($rencana['keterangan']) ?></p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="rencana_penangkapan.php?edit=<?= $rencana['id'] ?>" class="bg-yellow-400 text-white p-2 rounded"><i class="fas fa-edit"></i> Edit</a>
                                <a href="rencana_penangkapan.php?delete=<?= $rencana['id'] ?>" class="bg-red-500 text-white p-2 rounded"><i class="fas fa-trash-alt"></i> Hapus</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-4 text-center mt-auto">
        <p>&copy; 2024 E-GreenShell</p>
    </footer>

    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const body = document.body;

        sidebarToggle.addEventListener('click', () => {
            body.classList.toggle('sidebar-open');
        });
    </script>
</body>
</html>
