<?php
session_start();

//hapus semua variabel session
$_SESSION = array();

//hapus session cookie
if(ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
    $params["path"], $params["domain"],
    $params["secure"], $params["httponly"]
);
}

//hapus session
session_destroy();

//kembali ke halaman login
header("location: index.php");
exit;
?>