<?php
$folder = 'images/';
$categories = [];

// Fungsi untuk scan direktori secara rekursif
function scanDirectoryRecursively($dir) {
    $files = [];
    if (!is_dir($dir)) {
        return $files;
    }
    
    $scan = scandir($dir);
    foreach ($scan as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        
        if (is_dir($path)) {
            // Jika folder, lakukan rekursi
            $files = array_merge($files, scanDirectoryRecursively($path));
        } elseif (in_array(pathinfo($path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
            $files[] = $path;
        }
    }
    return $files;
}

// Ambil semua file gambar secara rekursif
$allFiles = scanDirectoryRecursively($folder);

// Kelompokkan gambar berdasarkan kategori (chX)
foreach ($allFiles as $file) {
    preg_match('/_ch(\d+)/', $file, $matches);
    if (isset($matches[1])) {
        $category = 'ch' . $matches[1];
        $subfolderName = basename(dirname($file)); // Nama subfolder tempat gambar berada
        $categories[$category][] = [
            'path' => $file,
            'name' => $subfolderName . ' - ' . basename($file)
        ];
    }
}

// Ambil kategori dan halaman dari parameter GET
$selectedCategory = $_GET['category'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = isset($_GET['itemsPerPage']) ? (int)$_GET['itemsPerPage'] : 8;

// Jika hanya ingin jumlah total gambar, kirim data total
if (isset($_GET['countOnly']) && $_GET['countOnly'] == 'true') {
    echo json_encode(['total' => count($categories[$selectedCategory] ?? [])]);
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
$startIndex = ($page - 1) * $itemsPerPage;
$paginatedFiles = array_slice($categoryFiles, $startIndex, $itemsPerPage);

// Menampilkan gambar dengan nama subfolder di depannya
foreach ($paginatedFiles as $fileData) {
    echo "<div class='border p-2 bg-white shadow-lg rounded-lg transform transition-transform duration-300 hover:scale-105'>";
    echo "<img src='{$fileData['path']}' alt='{$fileData['name']}' class='w-full h-48 object-cover rounded-t-lg'>";
    echo "<p class='text-center mt-2 text-gray-700'>{$fileData['name']}</p>";
    echo "</div>";
}
?>
