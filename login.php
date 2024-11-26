<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mengambil data pengguna berdasarkan username
    $query = "SELECT * FROM users WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($query);

    // Bind parameter (menghindari SQL Injection)
    $stmt->bind_param("s", $username);
    $stmt->execute();

    // Ambil hasil query
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: beranda.php");
            exit();
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Username tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-GreenShell</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-b from-blue-50 to-green-50 text-gray-900 font-sans">

    <!-- Header -->
    <header class="text-center py-16 bg-green-100">
        <h1 class="text-4xl font-bold text-green-800">E-GS</h1>
        <p class="text-lg text-gray-700 mt-4">Silakan login untuk melanjutkan</p>
    </header>

    <section class="py-16">
        <div class="container mx-auto">
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition p-8 w-full max-w-md mx-auto">
                <h2 class="text-3xl font-bold text-center text-green-900 mb-6">Login</h2>
                <form action="login.php" method="POST">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-600">Username</label>
                        <input type="text" id="username" name="username" class="w-full p-3 mt-2 bg-gray-100 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-gray-600">Password</label>
                        <input type="password" id="password" name="password" class="w-full p-3 mt-2 bg-gray-100 rounded" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded hover:bg-blue-600 transition">Login</button>
                </form>
                <?php if (isset($error)) : ?>
                    <div class="mt-4 text-red-500 text-center">
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>
                <div class="mt-6 text-center">
                    <p class="text-gray-700">Belum memiliki akun? <a href="register.php" class="text-blue-500 hover:underline">Register</a></p>
                    <p class="mt-2"><a href="index.html" class="text-blue-500 hover:underline">Kembali ke Home</a></p>
                </div>
            </div>
        </div>
    </section>

</body>
</html>
