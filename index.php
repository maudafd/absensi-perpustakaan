<?php
session_start();

//cek jika user sudah login, arahkan ke dasboard sesuai role
if (isset($_SESSION["user_id"])) {
    if ($_SESSION["role"] === "admin") {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        header("Location: user_dashboard.php");
        exit;
    }
}

//cek jika form login di submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
require_once "config.php";

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
        
    // Gunakan prepared statement untuk keamanan
    $sql = "SELECT id, username, password, role FROM t_users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    //pastikan ada hasil dan data valid
    if ($result && $result->num_rows ===1) {
        $row = $result->fetch_assoc();
    
        //verifikasi password,
        if (password_verify($password, $row['password'])) {
            // Set session variabel
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
        
        // Generate CSRF token for user security
        $_SESSION['token'] = bin2hex(random_bytes(32));

        // arahkan ke dashboard sesuai role
        if ($row['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            $login_err = "Username atau password salah";
        }
    } else {
        $login_err = "Username tidak ditemukan";
    }
    
    //tutup koneksi
    mysqli_close($conn);

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login - Sistem Absensi Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gradient-to-br from-[#f5f5f0] to-[#e8ebdf] min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md p-8 bg-white rounded-xl shadow-lg relative border border-grey">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#4A6B57] to-[#8A9B6E] rounded-t-xl"></div>

        <div class="text-5xl text-center text-[#4A6B57] mb-6">📚</div>

        <h1 class="text-2xl font-semibold text-center text-[#4A6B57] mb-6 relative pb-3">
        SISTEM ABSENSI PERPUSTAKAAN POLITEKNIK NEGERI MADIUN
        <span class="absolute bottom-0 left-1/4 w-1/2 h-0.5 bg-gradient-to-r from-[#4A6B57] via-[#8A9B6E] to-[#4A6B57]"></span>
        </h1>

        <?php if (isset($login_err)) : ?>
        <div class="bg-red-100 text-red-800 text-sm px-4 py-3 rounded mb-4 border-l-4 border-red-600 text-center">
            <?= $login_err ?>
        </div>
        <?php endif; ?>

        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST" class="space-y-5">
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input type="text" name="username" id="username" required placeholder="Masukkan username"
            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" id="password" required placeholder="Masukkan password"
            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2" />
        </div>

        <button type="submit"
            class="w-full bg-[#8A9B5E] text-white py-2 rounded-md font-semibold hover:bg-[#6B7F5A] transition">
            LOGIN
        </button>
        </form>

        <div class="text-xs text-center text-gray-500 mt-6">
        &copy; <?= date('Y') ?> Sistem Absensi Perpustakaan
        </div>
    </div>

</body>
</html>
