<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Jika belum login, arahkan kembali ke halaman login
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCTV Pelabuhan PIDC</title>
    <link rel="icon" type="image/png" href="logo/favicon-32x32.png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
    <!-- Navbar -->
    <nav class="bg-blue-600 p-4 text-white w-full fixed top-0 left-0 z-50 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <img src="logo/pelni.png" alt="Logo Pelni" class="w-12">
            <button class="md:hidden text-white focus:outline-none" onclick="toggleMenu()">
                ☰
            </button>
            <ul id="menu" class="hidden md:flex space-x-4 md:space-x-6">
                <li><a href="#" class="hover:underline">Home</a></li>
                <li><a href="#" class="hover:underline">Gallery</a></li>
                <li><a href="#" class="hover:underline">About</a></li>
                <li><a href="logout.php" class="hover:underline">Logout</a></li>
            </ul>
        </div>
        <div id="mobileMenu" class="hidden md:hidden bg-blue-700 p-4 absolute top-full left-0 w-full shadow-md">
            <ul class="flex flex-col space-y-2">
                <li><a href="#" class="block text-white hover:underline">Home</a></li>
                <li><a href="#" class="block text-white hover:underline">Gallery</a></li>
                <li><a href="#" class="block text-white hover:underline">About</a></li>
                <li><a href="logout.php" class="block text-white hover:underline">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Kontainer Galeri -->
    <div class="container mx-auto p-8 flex-grow mt-16">
        <h1 class="text-2xl font-bold mb-4 text-center">CCTV Pelabuhan PIDC</h1>

        <!-- Tabs -->
        <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400 overflow-x-auto">
            <?php
            $folder = 'images/';
            if (!is_dir($folder)) {
                echo "<p class='text-center text-red-500'>Folder images tidak ditemukan.</p>";
            } else {
                $files = scandir($folder);
                $categories = [];

                // Mengelompokkan file berdasarkan kategori (ch1, ch2, dll.)
                foreach ($files as $file) {
                    if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
                        preg_match('/_ch(\d+)/', $file, $matches);
                        if (isset($matches[1])) {
                            $category = 'ch' . $matches[1];
                            $categories[$category][] = $file;
                        }
                    }
                }

                // Menampilkan tab untuk setiap kategori
                if (!empty($categories)) {
                    $firstCategory = array_key_first($categories);
                    foreach ($categories as $category => $files) {
                        $isActive = $category === $firstCategory ? 'text-blue-600 bg-gray-100 dark:bg-gray-800 dark:text-blue-500' : 'hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300';
                        echo "<li class='me-2'>";
                        echo "<a href='#' data-category='$category' onclick='showCategory(\"$category\", 1)' class='inline-block p-4 rounded-t-lg $isActive'>$category</a>";
                        echo "</li>";
                    }
                } else {
                    echo "<p class='text-center text-red-500'>Tidak ada gambar yang ditemukan.</p>";
                }
            }
            ?>
        </ul>

        <!-- Galeri Gambar -->
        <div id="galleryContainer" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mt-4"></div>

        <!-- Pagination Controls -->
        <div id="paginationControls" class="flex justify-center space-x-2 mt-8">
            <button id="prevPage" class="bg-blue-500 text-white px-4 py-2 rounded-lg disabled:opacity-50" onclick="changePage(-1)">Sebelumnya</button>
            <span id="pageInfo" class="text-gray-700 font-semibold px-4 py-2">Halaman 1 dari 1</span>
            <button id="nextPage" class="bg-blue-500 text-white px-4 py-2 rounded-lg disabled:opacity-50" onclick="changePage(1)">Selanjutnya</button>
        </div>
    </div>

    <script>
        let currentCategory = "<?php echo $firstCategory; ?>";
        let currentPage = 1;
        let itemsPerPage = 8;

        function toggleMenu() {
            let menu = document.getElementById("mobileMenu");
            menu.classList.toggle("hidden");
        }

        function showCategory(category, page) {
            currentCategory = category;
            currentPage = page;

            fetch(`get_images.php?category=${category}&page=${page}&itemsPerPage=${itemsPerPage}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('galleryContainer').innerHTML = data;
                    updatePaginationControls();
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    document.getElementById('galleryContainer').innerHTML = `<p class='text-red-500 text-center'>Gagal memuat gambar.</p>`;
                });
        }

        function changePage(direction) {
            currentPage += direction;
            showCategory(currentCategory, currentPage);
        }

        function updatePaginationControls() {
            fetch(`get_images.php?category=${currentCategory}&itemsPerPage=${itemsPerPage}&countOnly=true`)
                .then(response => response.json())
                .then(data => {
                    const totalPages = Math.ceil(data.total / itemsPerPage);
                    document.getElementById('pageInfo').textContent = `Halaman ${currentPage} dari ${totalPages}`;
                    document.getElementById('prevPage').disabled = currentPage === 1;
                    document.getElementById('nextPage').disabled = currentPage >= totalPages;
                })
                .catch(error => console.error("Fetch error:", error));
        }

        showCategory(currentCategory, currentPage);
    </script>
</body>
</html>
