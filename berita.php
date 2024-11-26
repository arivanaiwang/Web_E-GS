<?php
require 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit();
}

// Proses penambahan postingan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['caption'])) {
    $caption = $_POST['caption'];
    $photo = $_FILES['photo'];

    if ($photo['error'] == 0) {
        $photoPath = 'uploads/' . uniqid() . '_' . $photo['name'];
        move_uploaded_file($photo['tmp_name'], $photoPath);

        // Menggunakan MySQLi untuk menambahkan data
        $query = "INSERT INTO posts (user_id, photo, caption, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iss', $_SESSION['user_id'], $photoPath, $caption);
        $stmt->execute();

        // Redirect ke halaman yang sama untuk menampilkan postingan baru
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Gagal mengunggah foto.";
    }
}

// Proses penambahan komentar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'], $_POST['post_id'])) {
    $comment = $_POST['comment'];
    $post_id = $_POST['post_id'];

    // Menggunakan MySQLi untuk menambahkan komentar
    $query = "INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iis', $post_id, $_SESSION['user_id'], $comment);
    $stmt->execute();

    // Redirect untuk refresh halaman
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Proses like
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['like'], $_POST['post_id'])) {
    $post_id = $_POST['post_id'];

    // Menggunakan MySQLi untuk menambahkan like
    $query = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $post_id, $_SESSION['user_id']);
    $stmt->execute();

    // Redirect untuk refresh halaman
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Ambil data postingan, jumlah likes, dan komentar
$query = "
    SELECT 
        posts.id AS post_id, 
        posts.photo, 
        posts.caption, 
        posts.created_at, 
        users.username,
        COUNT(likes.id) AS like_count
    FROM posts
    LEFT JOIN likes ON posts.id = likes.post_id
    LEFT JOIN users ON posts.user_id = users.id
    GROUP BY posts.id";
$result = $conn->query($query);
$posts = $result->fetch_all(MYSQLI_ASSOC);

// Ambil semua komentar untuk masing-masing postingan
$commentsQuery = "
    SELECT 
        comments.post_id, 
        users.username, 
        comments.comment, 
        comments.created_at
    FROM comments
    LEFT JOIN users ON comments.user_id = users.id
    ORDER BY comments.created_at ASC";
$resultComments = $conn->query($commentsQuery);

// Kelompokkan komentar berdasarkan post_id
$comments = [];
while ($row = $resultComments->fetch_assoc()) {
    $comments[$row['post_id']][] = $row;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita - E-GreenShell</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f0f4f8;
            margin: 0;
        }

        .main-content {
            margin-top: 80px;
            margin-left: 0;
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .post {
            background-color: white;
            padding: 20px;
            margin: 10px 0;
            width: 100%;
            max-width: 600px; /* Membuat ukuran postingan lebih kecil */
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-align: center; /* Memusatkan konten */
        }

        .post:hover {
            transform: scale(1.05); /* Efek hover */
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .post img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #e5e7eb; /* Border ringan di sekitar gambar */
        }

        .post-header {
            font-weight: bold;
            color: #2d3748;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .post p {
            color: #4B5563;
            font-size: 1rem;
            line-height: 1.5;
        }

        .post .like-count {
            font-weight: bold;
            margin-top: 10px;
            color: #1D4ED8;
        }

        .comments {
            margin-top: 15px;
            text-align: left;
        }

        .comment {
            padding: 10px;
            background-color: #f9fafb;
            border-radius: 5px;
            margin-bottom: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .comment strong {
            color: #1D4ED8;
        }

        footer {
            background-color: #2d3748;
            color: #e2e8f0;
            padding: 15px 0;
            text-align: center;
            margin-top: auto;
        }

        footer a {
            color: #f9a825;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: #1cc88a;
        }

        .username {
            color: #ffcc00;
            background-color: #2d3748;
            padding: 4px 8px;
            border-radius: 5px;
            font-weight: bold;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            max-width: 90%;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal.active {
            display: flex;
        }
        .sidebar1{
            margin-top: 70px;
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
                <a href="pembayaran.php" class="block hover:bg-blue-700">
                    <i class="fas fa-credit-card"></i>
                    <span>Pembayaran</span>
                </a>
                <a href="logout.php" class="block hover:bg-blue-700">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </div>
    </div>


    <h1 class="mt-60 text-center text-3xl font-semibold">Daftar Postingan</h1>
    <!-- Tombol untuk menambah postingan -->
    <div class="text-center my-5">
        <button id="openModal" class="bg-blue-500 text-white py-2 px-4 rounded-full">Tambah Postingan</button>
    </div>

    <!-- Modal untuk tambah postingan -->
    <div id="postModal" class="modal">
        <div class="modal-content">
            <h2 class="text-xl font-bold mb-4">Tambah Postingan</h2>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
                <label for="caption" class="block text-gray-700 mb-2">Caption:</label>
                <textarea name="caption" id="caption" rows="4" cols="50" required class="border p-2 w-full mb-4"></textarea>

                <label for="photo" class="block text-gray-700 mb-2">Foto:</label>
                <input type="file" name="photo" id="photo" accept="image/*" required class="border p-2 w-full mb-4">

                <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded">Tambah Postingan</button>
                <button type="button" id="closeModal" class="bg-red-500 text-white py-2 px-4 rounded mt-2">Tutup</button>
            </form>
        </div>
    </div>

    <!-- Menampilkan postingan -->
    <div class="main-content">
    <?php foreach ($posts as $post): ?>
        <div class="post">
            <div class="post-header">
                <?= htmlspecialchars($post['username']) ?> - <?= htmlspecialchars($post['created_at']) ?>
            </div>
            <img src="<?= htmlspecialchars($post['photo']) ?>" alt="Post Image">
            <p><?= htmlspecialchars($post['caption']) ?></p>
            <p><strong><?= htmlspecialchars($post['like_count']) ?> Likes</strong></p>

            <div class="comments">
                <h4>Komentar:</h4>
                <?php if (!empty($comments[$post['post_id']])): ?>
                    <?php foreach ($comments[$post['post_id']] as $comment): ?>
                        <div class="comment">
                            <strong><?= htmlspecialchars($comment['username']) ?>: </strong>
                            <?= htmlspecialchars($comment['comment']) ?><br>
                            <small style="font-size: 0.8em;"><?= htmlspecialchars($comment['created_at']) ?>: </small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Belum ada komentar.</p>
                <?php endif; ?>
            </div>

            <!-- Form komentar -->
            <form action="berita.php" method="POST" class="mt-4">
                <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['post_id']) ?>">
                <textarea name="comment" rows="2" placeholder="Tulis komentar..." class="border p-2 w-full mb-2"></textarea>
                <button type="submit" class="bg-green-500 text-white py-1 px-3 rounded">Kirim Komentar</button>
            </form>

            <!-- Tombol Like -->
            <form action="berita.php" method="POST">
                <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['post_id']) ?>">
                <button type="submit" name="like" class="bg-blue-500 text-white py-1 px-3 rounded mt-2">
                    <i class="fas fa-thumbs-up"></i> Suka
                </button>
            </form>
        </div>
    <?php endforeach; ?>

    </div>
    <footer>
    <p>&copy; 2024 E-GreenShell. Semua hak dilindungi.</p>
</footer>

<script>
    document.getElementById("sidebarToggle").addEventListener("click", function() {
    document.getElementById("sidebar").classList.toggle("-translate-x-full");
        });
        // Menampilkan modal
        document.getElementById('openModal').addEventListener('click', function() {
            document.getElementById('postModal').classList.add('active');
        });

        // Menutup modal
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('postModal').classList.remove('active');
        });
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;

            fetch('like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `post_id=${postId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Reload halaman untuk memperbarui jumlah like
                }
            });
        });
    });

    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const postId = this.dataset.postId;
            const comment = this.querySelector('textarea').value;

            fetch('comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `post_id=${postId}&comment=${encodeURIComponent(comment)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Reload halaman untuk memperbarui komentar
                } else {
                    alert(data.message);
                }
            });
        });
    });
</script>
</body>
</html>
