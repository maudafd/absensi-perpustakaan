<?php
session_start();

// Cek jika admin belum login atau role bukan mahasiswa
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "mahasiswa") {
    header("location: index.php");
    exit;
}

require_once "config.php";
$username = $_SESSION["username"];
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'absensi';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasboard Mahasiswa</title>
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
        <header class="bg-sage-500 text-white rounded-lg p-5 mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <h1 class="text-2xl font-bold relative pb-3 text-center md:text-left">
                Dashboard Absensi Perpustakaan
                <span class="absolute bottom-0 left-1/4 w-1/2 h-1 bg-white"></span>
            </h1>
            <div class="flex items-center gap-4">
                <span><?= htmlspecialchars($username) ?></span>
                <a href="logout.php" class="bg-sage-300 hover:bg-sage-400 text-white px-4 py-2 rounded transition">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </header>

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

        <!-- Form Absensi -->
    <?php if ($active_tab == 'absensi'): ?>
    <?php include 'absensi_form.php'; ?>
    <?php endif; ?>

    <!-- Profile Section -->
        <div class="content <?= $active_tab != 'profile' ? 'hidden' : '' ?>" id="profileSection">
            <h2>Profil Pengguna</h2>
            <div class="profile-info">
                <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
                <p><strong>Role:</strong> Mahasiswa </p>
            </div>
        </div>
    </div>

</body>
</html>