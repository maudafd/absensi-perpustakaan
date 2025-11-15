<?php
session_start();
require_once "config.php";

// Cek auth dan role admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("location: index.php");
    exit;
}
// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
// Proses form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $nim = trim($_POST['nim']);
    $jurusan = trim($_POST['jurusan']);
    $keperluan = trim($_POST['keperluan']);
    
    $stmt = $conn->prepare("UPDATE t_absensi SET nama=?, nim=?, jurusan=?, keperluan=? WHERE id=?");
    $stmt->bind_param("ssssi", $nama, $nim, $jurusan, $keperluan, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Data berhasil diperbarui";
        header("location: admin_dashboard.php?tab=riwayat");
        exit;
    } else {
        $error = "Gagal memperbarui data: " . $conn->error;
    }
}
// Ambil data yang akan diedit
$data = [];
if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM t_absensi WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    if (!$data) {
        $_SESSION['error_message'] = "Data tidak ditemukan";
        header("location: admin_dashboard.php?tab=riwayat");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Kunjungan</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style type="text/tailwindcss">
        @layer components {
            .content-card {
                @apply bg-white p-8 rounded-lg shadow-md mb-8 border border-sage-100;
            }
            h1::after, h2::after {
                content: '';
                @apply absolute bottom-0 bg-gradient-to-r from-sage-500 via-sage-300 to-sage-500 h-1;
            }
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sage: {
                            50: '#F5F5F0',
                            100: '#E8EBDF',
                            200: '#C5C9B8',
                            300: '#8A9B6E',
                            400: '#6B7F5A',
                            500: '#4A6B57',
                        },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-sage-50 to-sage-100 min-h-screen text-gray-700">
    <div class="container mx-auto px-5 py-8">
        <div class="content-card max-w-3xl mx-auto">
            <h1 class="text-2xl font-semibold pb-2 mb-6 relative">
                <i class="fas fa-edit mr-2"></i> Edit Data Kunjungan
                <span class="absolute bottom-0 left-0 w-20 h-1 bg-sage-300"></span>
            </h1>
            
            <?php if(isset($error)): ?>
                <div class="bg-red-50 text-red-700 border-l-4 border-red-500 p-4 mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <form method="post" class="space-y-4">
                <div class="space-y-2">
                    <label class="block text-sage-600 font-medium">Nama:</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($data['nama'] ?? '') ?>" 
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-sage-300 focus:border-sage-300" required>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sage-600 font-medium">NIM:</label>
                    <input type="text" name="nim" value="<?= htmlspecialchars($data['nim'] ?? '') ?>" 
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-sage-300 focus:border-sage-300" required>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sage-600 font-medium">Jurusan:</label>
                    <input type="text" name="jurusan" value="<?= htmlspecialchars($data['jurusan'] ?? '') ?>" 
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-sage-300 focus:border-sage-300" required>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sage-600 font-medium">Keperluan:</label>
                    <textarea name="keperluan" 
                        class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-sage-300 focus:border-sage-300 h-32" required><?= htmlspecialchars($data['keperluan'] ?? '') ?></textarea>
                </div>
                
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="bg-sage-300 hover:bg-sage-400 text-white px-5 py-3 rounded-lg transition flex-1">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                    <a href="admin_dashboard.php?tab=riwayat" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-3 rounded-lg transition flex-1 text-center">
                        <i class="fas fa-times mr-2"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>