<?php
session_start();
include '../partials/sidebar.php';
include '../config/database.php';

// Pastikan hanya admin atau supplier yang bisa akses
if (!in_array($_SESSION['role'], ['admin', 'supplier'])) {
    header("Location: login.php");
    exit;
}

// Tentukan ID supplier yang akan ditampilkan
if ($_SESSION['role'] === 'supplier') {
    // Supplier hanya boleh melihat/mengubah datanya sendiri
    $id = $_SESSION['id_user'];
} else {
    // Admin bisa melihat siapa pun lewat URL
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        echo "<script>alert('Supplier tidak ditemukan!'); window.location='supplier.php';</script>";
        exit;
    }
}

// Ambil data user
$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id_user = $id");
$user = mysqli_fetch_assoc($userQuery);
if (!$user) {
    echo "<script>alert('Data Supplier tidak ditemukan!'); window.location='supplier.php';</script>";
    exit;
}

// Ambil data supplier (email, no_telp, address)
$supplierQuery = mysqli_query($conn, "SELECT * FROM supplier WHERE id_user = $id");
$supplier = mysqli_fetch_assoc($supplierQuery);

// ===== UPDATE INFO (TAB 1) =====
if (isset($_POST['update_info'])) {
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);

    if (!empty($password)) {
        if ($password !== $confirm) {
            echo "<script>alert('Konfirmasi password tidak cocok!');</script>";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET nama=?, username=?, password=? WHERE id_user=?");
            $stmt->bind_param("sssi", $nama, $username, $hashed, $id);
            $stmt->execute();
            echo "<script>alert('Informasi akun berhasil diperbarui!'); window.location='detailsupplier.php?id=$id';</script>";
        }
    } else {
        $stmt = $conn->prepare("UPDATE users SET nama=?, username=? WHERE id_user=?");
        $stmt->bind_param("ssi", $nama, $username, $id);
        $stmt->execute();
        echo "<script>alert('Informasi akun berhasil diperbarui!'); window.location='detailsupplier.php?id=$id';</script>";
    }
}

// ===== UPDATE DETAIL (TAB 2) =====
if (isset($_POST['update_detail'])) {
    $email = trim($_POST['email']);
    $no_telp = trim($_POST['no_telp']);
    $address = trim($_POST['address']);

    $stmt = $conn->prepare("UPDATE supplier SET email=?, no_telp=?, address=? WHERE id_user=?");
    $stmt->bind_param("sssi", $email, $no_telp, $address, $id);
    $stmt->execute();

    echo "<script>alert('Detail supplier berhasil diperbarui!'); window.location='detailsupplier.php?id=$id';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Supplier</title>
  <link href="../assets/css/output.css" rel="stylesheet">
</head>
<body class="bg-white">

<main id="main-content" 
      class="ml-56 mt-[24px] mb-[24px] mr-[24px] transition-all duration-300 ease-in-out p-[40px] min-h-screen bg-zinc-100 rounded-[20px]">

  <!-- Header -->
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-[#FB8E1B]">Detail Supplier</h1>
      <p class="text-sm text-gray-600">Manage information for this Supplier.</p>
    </div>
    <div class="flex items-center gap-3">
      <p class="text-sm font-semibold text-gray-800">
        Welcome back, <?= htmlspecialchars($_SESSION['nama']) ?>
      </p>
      <img src="../assets/image/profile.svg" alt="Profile Icon" class="w-7 h-7 rounded-full cursor-pointer">
    </div>
  </div>

  <!-- Tabs -->
  <div>
    <div class="flex mb-4 bg-[#e7e7e7] p-1 shadow-inner rounded-[10px] w-fit">
      <button id="tabInfo" class="px-4 py-1 font-semibold rounded-[10px] transition bg-white shadow-sm hover:text-black">Info</button>
      <button id="tabDetail" class="px-4 py-1 font-semibold rounded-[10px] transition text-gray-500 hover:text-black ">Detail</button>
    </div>

    <div class="bg-white rounded-[10px] p-6 shadow-md w-[450px]">
        <!-- TAB 1: Info -->
        <div id="contentInfo">
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
    
            <button type="submit" name="update_info"
                    class="bg-[#023936] text-white font-medium px-4 py-2 rounded-md w-full hover:bg-[#035949] transition">
              Save Changes
            </button>
          </form>
        </div>
    
        <!-- TAB 2: Detail -->
        <div id="contentDetail" class="hidden">
          <form method="POST" class="flex flex-col gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Email</label>
              <input type="email" name="email" value="<?= htmlspecialchars($supplier['email'] ?? '') ?>"
                     class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none">
            </div>
    
            <div>
              <label class="block text-sm font-medium mb-1">No. Telp</label>
              <input type="text" name="no_telp" value="<?= htmlspecialchars($supplier['no_telp'] ?? '') ?>"
                     class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none">
            </div>
    
            <div>
              <label class="block text-sm font-medium mb-1">Address</label>
              <textarea name="address" rows="3"
                        class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none"><?= htmlspecialchars($supplier['address'] ?? '') ?></textarea>
            </div>
    
            <button type="submit" name="update_detail"
                    class="bg-[#023936] text-white font-medium px-4 py-2 rounded-md w-full hover:bg-[#035949] transition">
              Save Changes
            </button>
          </form>
        </div>
    </div>
  </div>
</main>

<!-- Script Tab Switching -->
<script>
  const tabInfo = document.getElementById('tabInfo');
  const tabDetail = document.getElementById('tabDetail');
  const contentInfo = document.getElementById('contentInfo');
  const contentDetail = document.getElementById('contentDetail');

  tabInfo.addEventListener('click', () => {
    tabInfo.classList.add('bg-white', 'shadow-sm', 'text-black');
    tabInfo.classList.remove('text-gray-500');
    tabDetail.classList.remove('bg-white', 'shadow-sm', 'text-black');
    tabDetail.classList.add('text-gray-500');
    contentInfo.classList.remove('hidden');
    contentDetail.classList.add('hidden');
  });

  tabDetail.addEventListener('click', () => {
    tabDetail.classList.add('bg-white', 'shadow-sm', 'text-black');
    tabDetail.classList.remove('text-gray-500');
    tabInfo.classList.remove('bg-white', 'shadow-sm', 'text-black');
    tabInfo.classList.add('text-gray-500');
    contentDetail.classList.remove('hidden');
    contentInfo.classList.add('hidden');
  });
</script>

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
