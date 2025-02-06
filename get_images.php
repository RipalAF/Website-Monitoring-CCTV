<?php
$folder = 'images/';
$directories = array_diff(scandir($folder), ['.', '..']);
$categories = [];

// Loop ke dalam setiap subfolder
foreach ($directories as $dir) {
    $subfolderPath = $folder . $dir . '/';
    if (is_dir($subfolderPath)) {
        $files = scandir($subfolderPath);
        foreach ($files as $file) {
            if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
                preg_match('/_ch(\d+)/', $file, $matches);
                if (isset($matches[1])) {
                    $category = 'ch' . $matches[1];
                    $categories[$category][] = [
                        'path' => $subfolderPath . $file,
                        'name' => $dir . ' - ' . $file // Tambahkan nama subfolder sebelum nama file
                    ];
                }
            }
        }
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
    echo "<p class='text-center mt-2 text-gray-700'>{$fileData['name']}</p>"; // Nama subfolder + nama file
    echo "</div>";
}
?>
