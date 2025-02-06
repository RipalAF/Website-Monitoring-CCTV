<?php
session_start();
session_unset(); // Hapus semua sesi
session_destroy(); // Hancurkan sesi
header("Location: login.php"); // Arahkan ke halaman login setelah logout
exit();
?>
