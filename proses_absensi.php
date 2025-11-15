<?php
session_start();

//cek jika user blm login, arahkan ke halaman login
if(!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}
//cek jika bukan metode post
if($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error_message'] = "Metode akses tidak valid";
    
    // Rarahkan sesuai role user
    if($_SESSION["role"] === "admin") {
        header("location: admin_dashboard.php?tab=absensi");
    } else {
        header("location: user_dashboard.php?tab=absensi");
    }
    exit;
}
require_once "config.php";
//ambil data dari form
$nama = mysqli_real_escape_string($conn, $_POST['nama']);
$nim = mysqli_real_escape_string($conn, $_POST['nim']);
$jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);
$keperluan = mysqli_real_escape_string($conn, $_POST['keperluan']);

// Validasi data
if(empty($nama) || empty($nim) || empty($jurusan) || empty($keperluan)) {
    $_SESSION['error_message'] = "Semua field harus diisi!"; 
    // Redirect sesuai role user
    if($_SESSION["role"] === "admin") {
        header("location: admin_dashboard.php?tab=absensi");
    } else {
        header("location: user_dashboard.php?tab=absensi");
    }
    exit;
}

// Validasi nim hanya berisi angka
if(!preg_match("/^[0-9]+$/", $nim)) {
    $_SESSION['error_message'] = "NIM hanya boleh berisi angka!";
    
    // Redirect sesuai role user
    if($_SESSION["role"] === "admin") {
        header("location: admin_dashboard.php?tab=absensi");
    } else {
        header("location: user_dashboard.php?tab=absensi");
    }
    exit;
}

// Insert data ke database menggunakan prepared statement
$sql = "INSERT INTO t_absensi (nama, nim, jurusan, keperluan) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    $_SESSION['error_message'] = "Error preparing statement: " . $conn->error;
    
    // Redirect sesuai role user
    if($_SESSION["role"] === "admin") {
        header("location: admin_dashboard.php?tab=absensi");
    } else {
        header("location: user_dashboard.php?tab=absensi");
    }
    exit;
}

$stmt->bind_param("ssss", $nama, $nim, $jurusan, $keperluan);
if($stmt->execute()) {
    $_SESSION['success_message'] = "Absensi berhasil dicatat!";
} else {
    $_SESSION['error_message'] = "Gagal mencatat absensi: " . $conn->error;
mysqli_close($conn);
}

//tutup koneksi
$stmt->close();
mysqli_close($conn);

header("Location: " . ($_SESSION["role"] === "admin" ? "admin_dashboard.php?tab=absensi" : "user_dashboard.php?tab=absensi"));
exit;

?>