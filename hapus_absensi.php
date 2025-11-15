<?php
session_start();
// Cek jika admin blm login, arahkan ke halaman login
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("location: index.php");
    exit;
}
require_once "config.php";
// Cek jika ID tersedia
if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    // Gunakan prepared statement untuk keamanan
    $sql = "DELETE FROM t_absensi WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $_SESSION['error_message'] = "Error preparing statement: " . $conn->error;
        header("location: admin_dashboard.php?tab=riwayat");
        exit;
    }
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        $_SESSION['success_message'] = "Data absensi berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus data: " . $conn->error;
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = "ID tidak valid!";
}

// Tutup koneksi dan redirect
mysqli_close($conn);
header("location: admin_dashboard.php?tab=riwayat");
exit;
?>