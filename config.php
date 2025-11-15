<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_absensi_perpustakaan";
$port = "4306";

$conn = mysqli_connect($host, $user, $pass, $db, port:$port);
if(!$conn) {
    die("koneksi gagal: " . mysqli_connect_error());
}
?>