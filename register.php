<?php
require_once 'config.php'; // Koneksi menggunakan MySQLi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash password untuk keamanan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Periksa apakah username sudah terdaftar
    $query = "SELECT * FROM users WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $username); // 's' untuk tipe data string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Username sudah terdaftar.";
    } else {
        // Insert data ke tabel users
        $insert_query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param('sss', $username, $email, $hashed_password); // 'sss' untuk 3 tipe data string

        if ($insert_stmt->execute()) {
            $success_message = "Registrasi berhasil! Silakan <a href='login.php' class='text-blue-400 hover:underline'>login</a>.";
        } else {
            $error = "Terjadi kesalahan saat registrasi. Silakan coba lagi.";
        }
    }
    
    // Tutup statement untuk membebaskan resource
    $stmt->close();
    $insert_stmt->close();
}

// Tutup koneksi database
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - E-GreenShell</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gradient-to-b from-blue-50 to-green-50 text-gray-900 font-sans">

    <!-- Header -->
    <header class="text-center py-16 bg-green-100">
        <h1 class="text-4xl font-bold text-green-800">E-GreenShell</h1>
        <p class="text-lg text-gray-700 mt-4">Silakan isi formulir di bawah untuk mendaftar</p>
    </header>

    <!-- Register Form Section -->
    <section class="py-16">
        <div class="container mx-auto">
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition p-8 w-full max-w-md mx-auto">
                <h2 class="text-3xl font-bold text-center text-green-900 mb-6">Register</h2>
                <form action="register.php" method="POST">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-600">Username</label>
                        <input type="text" id="username" name="username" class="w-full p-3 mt-2 bg-gray-100 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-600">Email</label>
                        <input type="email" id="email" name="email" class="w-full p-3 mt-2 bg-gray-100 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-gray-600">Password</label>
                        <input type="password" id="password" name="password" class="w-full p-3 mt-2 bg-gray-100 rounded" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded hover:bg-blue-600 transition">Register</button>
                </form>

                <!-- Success/Error Messages -->
                <?php if (isset($error)) : ?>
                    <div class="mt-4 text-red-500 text-center">
                        <p><?php echo $error; ?></p>
                    </div>
                <?php elseif (isset($success_message)) : ?>
                    <div class="mt-4 text-green-500 text-center">
                        <p><?php echo $success_message; ?></p>
                    </div>
                <?php endif; ?>

                <div class="mt-6 text-center">
                    <p class="text-gray-700">Sudah memiliki akun? <a href="login.php" class="text-blue-500 hover:underline">Login</a></p>
                    <p class="mt-2"><a href="index.html" class="text-blue-500 hover:underline">Kembali ke Home</a></p>
                </div>
            </div>
        </div>
    </section>

</body>
</html>
