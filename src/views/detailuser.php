<?php
session_start();
include '../partials/sidebar.php';
include '../config/database.php';

// Pastikan hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}


// Ambil ID user dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "<script>alert('User tidak ditemukan!'); window.location='user.php';</script>";
    exit;
}

// Ambil data user berdasarkan ID
$query = mysqli_query($conn, "SELECT * FROM users WHERE id_user = $id");
$user = mysqli_fetch_assoc($query);
if (!$user) {
    echo "<script>alert('Data user tidak ditemukan!'); window.location='user.php';</script>";
    exit;
}

// ===== UPDATE USER =====
if (isset($_POST['update'])) {
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);

    // Jika password diisi, pastikan cocok lalu update dengan hash
    if (!empty($password)) {
        if ($password !== $confirm) {
            echo "<script>alert('Konfirmasi password tidak cocok!');</script>";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET nama=?, username=?, password=? WHERE id_user=?");
            $stmt->bind_param("sssi", $nama, $username, $hashed, $id);
            $stmt->execute();
            echo "<script>alert('User berhasil diperbarui!'); window.location='user.php';</script>";
        }
    } else {
        // Jika password kosong, update tanpa ubah password
        $stmt = $conn->prepare("UPDATE users SET nama=?, username=? WHERE id_user=?");
        $stmt->bind_param("ssi", $nama, $username, $id);
        $stmt->execute();
        echo "<script>alert('User berhasil diperbarui!'); window.location='user.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail User</title>
  <link href="../assets/css/output.css" rel="stylesheet">
</head>
<body class="bg-white">

  <main id="main-content" 
        class="ml-56 mt-[24px] mb-[24px] mr-[24px] transition-all duration-300 ease-in-out p-[40px] min-h-screen bg-zinc-100 rounded-[20px]">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-[#FB8E1B]">Detail User</h1>
        <p class="text-sm text-gray-600">
          Manage information for this user.
        </p>
      </div>
      <div class="flex items-center gap-3">
        <p class="text-sm font-semibold text-gray-800">
          Welcome back, <?= htmlspecialchars($_SESSION['nama']) ?>
        </p>
        <img src="../assets/image/profile.svg" alt="Profile Icon" class="w-7 h-7 rounded-full cursor-pointer">
      </div>
    </div>

    <!-- Card -->
    <div class="bg-white w-[400px] p-8 rounded-[10px] shadow-md">
      <form method="POST" class="flex flex-col gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Name</label>
          <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required
                 class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Username</label>
          <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required
                 class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none">
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Password</label>
          <div class="relative">
            <input autocomplete="off" type="password" name="password" required 
                  class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none pr-10"
                  id="passwordField">
            <button type="button" 
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 transition-colors focus:outline-none"
                    data-toggle-password="passwordField">
              <img src="../assets/image/notvisible.svg" alt="Show Password" class="w-5 h-5" data-password-icon>
            </button>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Confirm Password</label>
          <div class="relative">
            <input autocomplete="off" type="password" name="confirm_password" required 
                  class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none pr-10"
                  id="confirmPasswordField">
            <button type="button" 
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 transition-colors focus:outline-none"
                    data-toggle-password="confirmPasswordField">
              <img src="../assets/image/notvisible.svg" alt="Show Password" class="w-5 h-5" data-password-icon>
            </button>
          </div>
        </div>

        <div class="mt-4">
          <button type="submit" name="update"
                  class="bg-[#023936] text-white font-medium px-4 py-2 rounded-md w-full hover:bg-[#035949] transition">
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </main>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-toggle-password]').forEach(button => {
      button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-toggle-password');
        const input = document.getElementById(targetId);
        const icon = this.querySelector('[data-password-icon]');
        
        if (input.type === 'password') {
          input.type = 'text';
          icon.src = '../assets/image/visible.svg';
          icon.alt = 'Hide Password';
        } else {
          input.type = 'password';
          icon.src = '../assets/image/notvisible.svg';
          icon.alt = 'Show Password';
        }
      });
    });
  });
</script>

</body>
</html>
