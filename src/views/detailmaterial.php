<?php
session_start();
include '../partials/sidebar.php';
include '../config/database.php';

// Cek login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$id_user = intval($_SESSION['id_user']);
$role = $_SESSION['role']; // 'admin' / 'supplier'

// Ambil ID material dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "<script>alert('Material tidak ditemukan!'); window.location='material.php';</script>";
    exit;
}

// Ambil data material sesuai hak akses
if ($role === 'admin') {
    $query = mysqli_query($conn, "SELECT * FROM material WHERE id_material = $id");
} else { // supplier
    $query = mysqli_query($conn, "SELECT * FROM material WHERE id_material = $id AND id_user = $id_user");
}

if (!$query) {
    die("Query Error: " . mysqli_error($conn));
}

$material = mysqli_fetch_assoc($query);
if (!$material) {
    echo "<script>alert('Material tidak ditemukan atau Anda tidak memiliki akses!'); window.location='material.php';</script>";
    exit;
}

// ====== UPDATE MATERIAL ======
if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $specification = trim($_POST['specification']);
    $unit = trim($_POST['unit']);
    $quantity = intval($_POST['quantity']);
    $price = intval($_POST['price']);

    if (empty($name) || empty($unit)) {
        echo "<script>alert('Nama dan satuan harus diisi!');</script>";
    } else {
        $stmt = $conn->prepare("UPDATE material SET name=?, specification=?, unit=?, quantity=?, price=? WHERE id_material=?");
        $stmt->bind_param("sssiii", $name, $specification, $unit, $quantity, $price, $id);
        $stmt->execute();

        echo "<script>alert('Data material berhasil diperbarui!'); window.location='material.php';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Material</title>
  <link href="../assets/css/output.css" rel="stylesheet">
</head>
<body class="bg-white">

<main id="main-content" 
      class="ml-56 mt-[24px] mb-[24px] mr-[24px] transition-all duration-300 ease-in-out p-[40px] min-h-screen bg-zinc-100 rounded-[20px]">

  <!-- Header -->
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-[#FB8E1B]">Edit Material</h1>
      <p class="text-sm text-gray-600">
        Perbarui data material di sini.
      </p>
    </div>
    <div class="flex items-center gap-3">
      <p class="text-sm font-semibold text-gray-800">
        Welcome back, <?= htmlspecialchars($_SESSION['nama']) ?>
      </p>
      <img src="../assets/image/profile.svg" alt="Profile Icon" class="w-7 h-7 rounded-full cursor-pointer">
    </div>
  </div>

  <!-- Form Edit -->
  <div class="bg-white w-[500px] p-8 rounded-[10px] shadow-md">
    <form method="POST" class="flex flex-col gap-4">

      <div>
        <label class="block text-sm font-medium mb-1">Nama Material</label>
        <input type="text" name="name" value="<?= htmlspecialchars($material['name']) ?>" required
               class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Spesifikasi</label>
        <textarea name="specification" rows="3"
                  class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none"><?= htmlspecialchars($material['specification']) ?></textarea>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Satuan</label>
        <input type="text" name="unit" value="<?= htmlspecialchars($material['unit']) ?>" required
               class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Kuantitas</label>
        <input type="number" name="quantity" value="<?= htmlspecialchars($material['quantity']) ?>"
               class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none" min="0">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Harga Satuan (Rp)</label>
        <input type="number" name="price" value="<?= htmlspecialchars($material['price']) ?>"
               class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none" min="0">
      </div>

      <div class="mt-4 flex gap-3">
        <a href="material.php" 
           class="bg-gray-200 text-[#023936] font-medium px-4 py-2 rounded-md hover:bg-gray-300 transition">
          Kembali
        </a>
        <button type="submit" name="update"
                class="bg-[#023936] text-white font-medium px-4 py-2 rounded-md hover:bg-[#035949] transition flex-1">
          Simpan Perubahan
        </button>
      </div>

    </form>
  </div>
</main>

</body>
</html>