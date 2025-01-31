<?php
$folder = 'images/';
$files = scandir($folder);
$categories = [];

// Mengelompokkan gambar berdasarkan kategori
foreach ($files as $file) {
    if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
        preg_match('/_ch(\d+)/', $file, $matches);
        if (isset($matches[1])) {
            $category = 'ch' . $matches[1];
            $categories[$category][] = $file;
        }
    }
}

// Ambil kategori dan halaman dari parameter GET
$selectedCategory = $_GET['category'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = isset($_GET['itemsPerPage']) ? (int)$_GET['itemsPerPage'] : 8;
$countOnly = isset($_GET['countOnly']) ? $_GET['countOnly'] : false;

// Jika parameter countOnly=true, kirimkan jumlah gambar total
if ($countOnly === 'true') {
    if (isset($categories[$selectedCategory])) {
        echo json_encode(['total' => count($categories[$selectedCategory])]);
    } else {
        echo json_encode(['total' => 0]);
    }
    exit;
}

// Jika kategori tidak ditemukan, tampilkan pesan error
if (!isset($categories[$selectedCategory])) {
    echo "<p class='text-center text-red-500'>Tidak ada gambar untuk kategori ini.</p>";
    exit;
}

// Ambil gambar sesuai halaman yang diminta
$categoryFiles = $categories[$selectedCategory];
$totalFiles = count($categoryFiles);
$totalPages = ceil($totalFiles / $itemsPerPage);

// Tentukan gambar yang akan ditampilkan pada halaman saat ini
$startIndex = ($page - 1) * $itemsPerPage;
$paginatedFiles = array_slice($categoryFiles, $startIndex, $itemsPerPage);

// Menampilkan gambar
foreach ($paginatedFiles as $file) {
    $filePath = $folder . $file;
    echo "<div class='border p-2 bg-white shadow-lg rounded-lg transform transition-transform duration-300 hover:scale-105'>";
    echo "<img src='$filePath' alt='$file' class='w-full h-48 object-cover rounded-t-lg'>";
    echo "<p class='text-center mt-2 text-gray-700'>$file</p>";
    echo "</div>";
}
?>