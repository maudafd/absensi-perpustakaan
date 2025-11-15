<?php
session_start();

// Cek jika admin belum login atau role bukan admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("location: index.php");
    exit;
}

require_once "config.php";
$username = $_SESSION["username"];
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'riwayat'; // Default tab riwayat untuk admin

// Query untuk statistik
$total_today = 0;
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT COUNT(*) FROM t_absensi WHERE DATE(tanggal_waktu) = ?");
$stmt->bind_param("s", $today);
$stmt->execute();
$stmt->bind_result($total_today);
$stmt->fetch();
$stmt->close();

// Pagination dan Pencarian
$records_per_page = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $records_per_page;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
$params = [];

if (!empty($search)) {
    $where = "WHERE nama LIKE ? OR nim LIKE ?";
    $search_term = "%$search%";
}

// Query untuk total data
$count_query = "SELECT COUNT(*) AS total FROM t_absensi $where";
$stmt = $conn->prepare($count_query);

if (!empty($search)) {
    $stmt->bind_param("ss", $search_term, $search_term);
}

$stmt->execute();
$count_result = $stmt->get_result();
$count_row = $count_result->fetch_assoc();
$total_records = $count_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Query untuk data
$query = "SELECT * FROM t_absensi $where ORDER BY tanggal_waktu DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);

if (!empty($search)) {
    $stmt->bind_param("ssii", $search_term, $search_term, $offset, $records_per_page);
} else {
    $stmt->bind_param("ii", $offset, $records_per_page);
}

$stmt->execute();
$result = $stmt->get_result();

// Hitung start page untuk pagination
$start_page = max(1, $page - 2);
$end_page = min($total_pages, $page + 2);
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Perpustakaan</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Tailwind Config -->
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
                    },
                    animation: {
                        'fade-in-down': 'fadeInDown 0.5s ease-out',
                    },
                    keyframes: {
                        fadeInDown: {
                            '0%': { opacity: '0', transform: 'translateY(-10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                    },
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer components {
            .nav-item {
                @apply flex-1 text-center py-4 px-5 text-white font-medium transition-all flex items-center justify-center;
            }
            .nav-item.active {
                @apply bg-sage-500 font-bold;
                position: relative;
            }
            .nav-item.active::after {
                content: '';
                @apply absolute bottom-0 left-1/4 w-1/2 h-1 bg-white;
            }
            .content-card {
                @apply bg-white p-8 rounded-lg shadow-md mb-8 border border-sage-100 transition-all hover:-translate-y-1 hover:shadow-lg;
            }
            .stat-card {
                @apply bg-white p-5 rounded-lg shadow text-center;
            }
            .action-btn {
                @apply p-1.5 rounded text-sm inline-flex items-center;
            }
            h1::after, h2::after {
                content: '';
                @apply absolute bottom-0 bg-gradient-to-r from-sage-500 via-sage-300 to-sage-500 h-1;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-sage-50 to-sage-100 min-h-screen text-gray-700">
    <div class="container mx-auto px-5 py-8">
        <!-- Admin Header -->
        <header class="bg-sage-500 text-white rounded-lg p-5 mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold relative pb-3 text-center md:text-left">
                Dashboard Admin Perpustakaan
                <span class="absolute bottom-0 left-1/4 w-1/2 h-1 bg-white"></span>
            </h1>
            <div class="flex items-center gap-4">
                <span><?= htmlspecialchars($username) ?></span>
                <a href="logout.php" class="bg-sage-300 hover:bg-sage-400 text-white px-4 py-2 rounded transition">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </header>

        <!-- Navigation -->
        <nav class="bg-sage-300 rounded-lg overflow-hidden shadow mb-8 flex flex-col md:flex-row">
            <a href="?tab=riwayat" class="nav-item hover:bg-sage-400 <?= $active_tab == 'riwayat' ? 'active' : '' ?>">
                <i class="fas fa-history mr-2"></i> Riwayat Kunjungan
            </a>
            <a href="?tab=laporan" class="nav-item hover:bg-sage-400 <?= $active_tab == 'laporan' ? 'active' : '' ?>">
                <i class="fas fa-file-alt mr-2"></i> Laporan
            </a>
        </nav>

        <!-- Notifications -->
        <?php if(isset($_SESSION['success_message'])): ?>
        <div class="bg-green-50 text-green-700 border-l-4 border-green-500 p-4 mb-6 animate-fade-in-down">
            <i class="fas fa-check-circle mr-2"></i>
            <?= $_SESSION['success_message'] ?>
        </div>
        <?php unset($_SESSION['success_message']); endif; ?>

        <?php if(isset($_SESSION['error_message'])): ?>
        <div class="bg-red-50 text-red-700 border-l-4 border-red-500 p-4 mb-6 animate-fade-in-down">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?= $_SESSION['error_message'] ?>
        </div>
        <?php unset($_SESSION['error_message']); endif; ?>

        <!-- Riwayat Section -->
        <div class="content-card <?= $active_tab != 'riwayat' ? 'hidden' : '' ?>">
            <h2 class="text-xl font-semibold pb-2 mb-4 border-b border-sage-200 relative">
                <i class="fas fa-history mr-2"></i> Riwayat Kunjungan
                <span class="absolute bottom-0 left-0 w-10 h-0.5 bg-sage-300"></span>
            </h2>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-1 gap-5 mb-6">
                <div class="stat-card">
                    <h3 class="text-sage-500 mb-2"><i class="fas fa-users mr-2"></i> Total Hari Ini</h3>
                    <p class="text-3xl font-bold text-sage-500"><?= $total_today ?></p>
                </div>
            </div>

            <!-- Search Form -->
            <form method="get" class="mb-6">
                <input type="hidden" name="tab" value="riwayat">
                <div class="flex flex-col md:flex-row gap-3">
                    <input type="text" name="search" placeholder="Cari nama atau NIM..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                        class="flex-grow p-3 border rounded-lg focus:ring-2 focus:ring-sage-300 focus:border-sage-300">
                    <button type="submit" class="bg-sage-300 hover:bg-sage-400 text-white px-5 py-3 rounded-lg transition flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i> Cari
                    </button>
                    <?php if(isset($_GET['search'])): ?>
                    <a href="?tab=riwayat" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-3 rounded-lg transition flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i> Reset
                    </a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- Table riwayat -->
            <div class="overflow-x-auto">
                <table class="w-full shadow-sm rounded-lg overflow-hidden">
                    <thead class="bg-sage-300 text-white">
                        <tr>
                            <th class="p-3 text-left">No</th>
                            <th class="p-3 text-left">Tanggal</th>
                            <th class="p-3 text-left">Nama</th>
                            <th class="p-3 text-left">NIM</th>
                            <th class="p-3 text-left">Jurusan</th>
                            <th class="p-3 text-left">Keperluan</th>
                            <th class="p-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                        <?php 
                        $i = $offset + 1;
                        while($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-sage-50 even:bg-sage-50/50">
                            <td class="p-3"><?= $i++ ?></td>
                            <td class="p-3"><?= date('d-m-Y H:i', strtotime($row['tanggal_waktu'])) ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['nama']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['nim']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['jurusan']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($row['keperluan']) ?></td>
                            <td class="p-3">
                                <div class="flex gap-2">
                                    <a href="edit_absensi.php?id=<?= $row['id'] ?>" class="action-btn bg-blue-50 text-blue-600 hover:bg-blue-100">
                                    <i class="fas fa-edit mr-1"></i>
                                    </a>

                                    </a>
                                    <a href="javascript:void(0)" onclick="confirmDelete(<?= $row['id'] ?>)" 
                                    class="action-btn bg-red-50 text-red-600 hover:bg-red-100">
                                    <i class="fas fa-trash-alt mr-1"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="7" class="p-5 text-center text-gray-500">
                                <i class="fas fa-info-circle mr-2"></i> Tidak ada data kunjungan yang ditemukan.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-center mt-8 flex-wrap gap-2">
                <?php if($page > 1): ?>
                    <a href="?tab=riwayat&page=1<?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
                    class="px-3 py-1 border rounded hover:bg-gray-100">
                    <i class="fas fa-angle-double-left mr-1"></i> Awal
                    </a>
                    <a href="?tab=riwayat&page=<?= $page-1 ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
                    class="px-3 py-1 border rounded hover:bg-gray-100">
                    <i class="fas fa-angle-left mr-1"></i> Prev
                    </a>
                <?php endif; ?>
                
                <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                    <a href="?tab=riwayat&page=<?= $i ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
                    class="px-3 py-1 border rounded <?= $i == $page ? 'bg-sage-300 text-white border-sage-300' : 'hover:bg-gray-100' ?>">
                    <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if($page < $total_pages): ?>
                    <a href="?tab=riwayat&page=<?= $page+1 ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
                    class="px-3 py-1 border rounded hover:bg-gray-100">
                    Next <i class="fas fa-angle-right ml-1"></i>
                    </a>
                    <a href="?tab=riwayat&page=<?= $total_pages ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
                    class="px-3 py-1 border rounded hover:bg-gray-100">
                    Akhir <i class="fas fa-angle-double-right ml-1"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Laporan Section -->
        <div class="content-card <?= $active_tab != 'laporan' ? 'hidden' : '' ?>">
            <h2 class="text-xl font-semibold pb-2 mb-4 border-b border-sage-200 relative">
                <i class="fas fa-file-alt mr-2"></i> Laporan
                <span class="absolute bottom-0 left-0 w-10 h-0.5 bg-sage-300"></span>
            </h2>
            
            <div class="bg-sage-50 rounded-lg p-6 shadow-inner">
                <p class="text-center text-gray-500">
                    <i class="fas fa-info-circle mr-2"></i> Fitur laporan akan segera tersedia.
                </p>
            </div>
        </div>
    </div>

    <script>
    function confirmDelete(id) {
        if(confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            window.location.href = 'hapus_absensi.php?id=' + id;
        }
    }
    </script>
</body>
</html>