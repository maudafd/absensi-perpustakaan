<?php
//konfigurasi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_absensi_perpustakaan";

//buat koneksi dengan mysql server
$conn = new mysqli($host, $user, $pass);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

//pilih database yg sebelumnya sudah dibuat
// Cek apakah database sudah ada
$check_db = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'");
if ($check_db->num_rows == 0) {
    // Buat database jika belum ada
    if ($conn->query("CREATE DATABASE $db")) {
        echo "Database $db berhasil dibuat.<br>";
    } else {
        die("Error membuat database: " . $conn->error);
    }
}

if (!$conn->select_db($db)) {
    die("Gagal memilih database: " . $conn->error . "<br>Pastikan database '$db' sudah ada.");
}

// Query buat tabel t_users
$sql_users = " CREATE TABLE IF NOT EXISTS t_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'mahasiswa',
    create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Query buat tabel t_absensi
$sql_absensi = "CREATE TABLE IF NOT EXISTS t_absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(50) NOT NULL,
    nim VARCHAR(15) NOT NULL,
    jurusan VARCHAR(50) NOT NULL,
    keperluan ENUM('Baca Buku','Pinjam Buku','Kembalikan Buku','Belajar Kelompok','Lainnya') NOT NULL,
    tanggal_waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Query buat insert user default
$hashed_password = password_hash('mahasiswa123', PASSWORD_BCRYPT);
$sql_insert_user = "INSERT IGNORE INTO t_users (username, password, role)
    VALUES ('mahasiswa', '$hashed_password', 'mahasiswa'),
    ('admin', '" . password_hash('admin123', PASSWORD_BCRYPT) . "', 'admin')";


// Eksekusi query
$queries = [
    'Tabel Users' => $sql_users,
    'Tabel Absensi' => $sql_absensi,
    'User Default' => $sql_insert_user
];

foreach ($queries as $name => $sql) {
    if ($conn->query($sql)) {
        echo "$name berhasil diinisialisasi<br>";
    } else {
        echo "Error pada $name: " . $conn->error . "<br>";
    }
}

mysqli_close($conn);
echo "<br>Setup database selesai!";
?>