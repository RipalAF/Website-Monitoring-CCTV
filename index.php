<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCTV Pelabuha PIDC</title>
    <link rel="icon" type="image/png" href="logo/favicon-32x32.pnggi">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
    <!-- Navbar -->
    <nav class="bg-blue-600 p-4 text-white">    
        <div class="container mx-auto flex justify-between items-center">
            <img src="logo/pelni.png" alt="" class="w-12">
            <ul class="flex space-x-4">
                <li><a href="#" class="hover:underline">Home</a></li>
                <li><a href="#" class="hover:underline">Gallery</a></li>
                <li><a href="#" class="hover:underline">About</a></li>
            </ul>
        </div>
    </nav>

    <!-- Kontainer Galeri -->
    <div class="container mx-auto p-8 flex-grow">
        <h1 class="text-2xl font-bold mb-4 text-center">CCTV Pelabuhan PIDC</h1>

        <!-- Tabs -->
        <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">
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
                    $firstCategory = array_key_first($categories); // Ambil kategori pertama untuk tab aktif
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
        <div id="galleryContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-4"></div>

                <!-- Dropdown untuk memilih jumlah entri per halaman -->
        <div class="flex justify-center items-center space-x-2 mt-4">
            <label for="entriesPerPage" class="text-gray-700">Menampilkan:</label>
            <select id="entriesPerPage" class="px-3 py-2 border rounded-lg" onchange="changeEntriesPerPage()">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="50">50</option>
            </select>
        </div>

        <!-- Pagination -->
        <div id="paginationControls" class="flex justify-center space-x-2 mt-8">
            <!-- Tombol Previous -->
            <button id="prevPage" class="flex items-center justify-center bg-blue-500 text-white px-6 py-2 rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50" onclick="changePage(-1)" disabled>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="ml-2">Sebelumnya</span>
            </button>

            <!-- Info Halaman -->
            <span id="pageInfo" class="text-gray-700 font-semibold px-4 py-2">Halaman 1 dari 1</span>
            

            <!-- Tombol Next -->
            <button id="nextPage" class="flex items-center justify-center bg-blue-500 text-white px-6 py-2 rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50" onclick="changePage(1)">
                <span class="mr-2">Selanjutnya</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    


    <script>
        let currentCategory = "<?php echo $firstCategory; ?>";
let currentPage = 1;
let itemsPerPage = 8; // Default ke 8

// Fungsi untuk menangani perubahan jumlah entri per halaman
function changeEntriesPerPage() {
    // Ambil jumlah entri per halaman yang dipilih
    itemsPerPage = parseInt(document.getElementById('entriesPerPage').value);
    currentPage = 1; // Reset ke halaman 1 saat jumlah entri per halaman berubah
    showCategory(currentCategory, currentPage);
}

// Fungsi untuk menampilkan kategori dan gambar berdasarkan halaman yang dipilih
function showCategory(category, page) {
    currentCategory = category;
    currentPage = page;

    fetch(`get_images.php?category=${category}&page=${page}&itemsPerPage=${itemsPerPage}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Gagal mengambil data!");
            }
            return response.text();
        })
        .then(data => {
            document.getElementById('galleryContainer').innerHTML = data;
            updatePaginationControls();
        })
        .catch(error => {
            console.error("Fetch error:", error);
            document.getElementById('galleryContainer').innerHTML = `<p class="text-red-500 text-center">Gagal memuat gambar.</p>`;
        });

    // Update tab aktif
    document.querySelectorAll('ul li a').forEach(tab => {
        tab.classList.remove('text-blue-600', 'bg-gray-100', 'dark:bg-gray-800', 'dark:text-blue-500');
        tab.classList.add('hover:text-gray-600', 'hover:bg-gray-50', 'dark:hover:bg-gray-800', 'dark:hover:text-gray-300');
    });

    document.querySelectorAll('ul li a').forEach(tab => {
        if (tab.dataset.category === category) {
            tab.classList.remove('hover:text-gray-600', 'hover:bg-gray-50', 'dark:hover:bg-gray-800', 'dark:hover:text-gray-300');
            tab.classList.add('text-blue-600', 'bg-gray-100', 'dark:bg-gray-800', 'dark:text-blue-500');
        }
    });
}

// Fungsi untuk mengubah halaman (Next/Prev)
function changePage(direction) {
    currentPage += direction;
    showCategory(currentCategory, currentPage);
}

// Fungsi untuk memperbarui kontrol pagination
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

// Memastikan saat halaman pertama kali dibuka, kategori pertama ditampilkan
showCategory(currentCategory, currentPage);


        // Load kategori pertama saat halaman pertama kali dibuka
        showCategory(currentCategory, currentPage);
    </script>
</body>
</html>