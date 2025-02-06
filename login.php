<?php
session_start();


$valid_username = "admin";
$valid_password = "password123"; 

// Cek apakah pengguna sudah login
if (isset($_SESSION['username'])) {
    header("Location: index.php"); 
    exit(); 
}

// Proses login jika form di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validasi login
    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit(); 
    } else {
        $error_message = "Username atau Password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CCTV Pelabuhan PIDC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="logo/favicon-32x32.png">
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-600 to-indigo-800 px-4">
    <div class="bg-white p-6 sm:p-8 rounded-xl shadow-2xl max-w-sm w-full transform transition duration-300 hover:scale-105">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-6">Login</h2>
        <form action="login.php" method="POST">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-medium">Username</label>
                <input type="text" id="username" name="username" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm sm:text-base" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-medium">Password</label>
                <input type="password" id="password" name="password" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm sm:text-base" required>
            </div>
            <button type="submit" class="w-full bg-indigo-500 text-white p-3 rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200 text-sm sm:text-base">Login</button>
        </form>

        <!-- Jika ada error, tampilkan pesan -->
        <?php if (isset($error_message)): ?>
            <p class="text-red-500 text-center mt-4 text-sm sm:text-base"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>
</body>

</html>
