<?php
session_start();
include "../partials/sidebar.php";
include "../config/database.php";

// Cek login
if (!isset($_SESSION["id_user"])) {
    header("Location: ../login.php");
    exit();
}

$id_user = $_SESSION["id_user"];
$role = $_SESSION["role"]; // 'admin' atau 'supplier'

// Konfigurasi search & pagination
$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$limit = isset($_GET["limit"]) ? (int) $_GET["limit"] : 5;
$page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $limit;

// Query pencarian
$whereClause = "WHERE 1";
if ($search !== "") {
    $safeSearch = mysqli_real_escape_string($conn, $search);
    $whereClause .= " AND name LIKE '%$safeSearch%'";
}

// Jika supplier, filter hanya data miliknya
if ($role === "supplier") {
    $whereClause .= " AND id_user = $id_user";
}

// Hitung total data
$totalQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) as total FROM material $whereClause",
);
$totalRow = mysqli_fetch_assoc($totalQuery);
$total = $totalRow["total"];
$totalPages = ceil($total / $limit);

// Ambil data per halaman
if ($role === "admin" || $role === "user_rab") {
    $query = "SELECT m.*, u.nama AS supplier_name 
              FROM material m
              LEFT JOIN users u ON m.id_user = u.id_user
              $whereClause
              ORDER BY m.id_material ASC
              LIMIT $limit OFFSET $offset";
} else {
    $query = "SELECT * FROM material $whereClause ORDER BY id_material ASC LIMIT $limit OFFSET $offset";
}
$result = mysqli_query($conn, $query);
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// ====== CREATE MATERIAL ======
if (isset($_POST["create"])) {
    $name = trim($_POST["name"]);
    $specification = trim($_POST["specification"]);
    $unit = trim($_POST["unit"]);
    $quantity = intval($_POST["quantity"]);
    $price = intval($_POST["price"]);

    if (empty($name) || empty($unit)) {
        echo "<script>alert('Nama dan satuan wajib diisi!');</script>";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO material (id_user, name, specification, unit, quantity, price) VALUES (?, ?, ?, ?, ?, ?)",
        );
        $stmt->bind_param(
            "isssii",
            $id_user,
            $name,
            $specification,
            $unit,
            $quantity,
            $price,
        );
        $stmt->execute();
        echo "<script>alert('Material berhasil ditambahkan!'); window.location='material.php';</script>";
    }
}

// ====== DELETE MATERIAL ======
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);

    // validasi: supplier hanya bisa hapus data miliknya
    if ($role === "supplier") {
        $check = mysqli_query(
            $conn,
            "SELECT id_user FROM material WHERE id_material='$id'",
        );
        $row = mysqli_fetch_assoc($check);
        if ($row && $row["id_user"] == $id_user) {
            mysqli_query($conn, "DELETE FROM material WHERE id_material='$id'");
            echo "<script>alert('Material berhasil dihapus!'); window.location='material.php';</script>";
        } else {
            echo "<script>alert('❌ Anda tidak berhak menghapus material ini!'); window.location='material.php';</script>";
        }
    } else {
        mysqli_query($conn, "DELETE FROM material WHERE id_material='$id'");
        echo "<script>alert('Material berhasil dihapus!'); window.location='material.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Material</title>
  <link href="../assets/css/output.css" rel="stylesheet">
</head>
<body class="bg-white">
<main id="main-content" 
      class="ml-56 mt-[24px] mb-[24px] mr-[24px] transition-all duration-300 ease-in-out p-[40px] min-h-screen bg-zinc-100 rounded-[20px]">

  <div class="">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-[#FB8E1B]">Material</h1>
        <p class="text-sm text-gray-600">List of all Materials, create new material and manage existing ones.</p>
      </div>
      <div class="flex items-center gap-3">
        <p class="text-sm font-semibold text-gray-800">
          Welcome back, <?= htmlspecialchars($_SESSION["nama"]) ?>
        </p>
        <img src="../assets/image/profile.svg" alt="Profile Icon" class="rounded-full cursor-pointer w-7 h-7">
      </div>
    </div>

    <!-- Konten utama -->
    <div class="w-full mx-auto bg-white rounded-[20px] p-[40px]">

      <!-- Search dan tombol tambah -->
      <div class="flex flex-col-reverse gap-3 lg:flex-row lg:items-center lg:justify-between mb-[24px]">
        <form method="GET" class="flex items-center">
            <input autocomplete="off" type="text" name="search" value="<?= htmlspecialchars(
                $search,
            ) ?>"
            class="bg-zinc-100 rounded-tl-[10px] rounded-bl-[10px] px-3 py-2 w-60 outline-none focus:zinc-100"
            placeholder="Search by name...">
              <?php if ($search): ?>
              <a href="?page=1" class="">
                <img src="../assets/image/clear.svg" alt="clear" class="absolute flex-reverse -ml-[30px] -mt-[12px]">
              </a>
              <?php endif; ?>
            <button type="submit" class="bg-[#023936] text-white px-4 py-2 cursor-pointer rounded-tr-[10px] rounded-br-[10px] inline-flex h-[40px] justify-center items-center">
                <img src="../assets/image/search.svg" alt="">
            </button>
        </form>
        <?php if ($role === "admin" || $role === "supplier"): ?>
          <button id="openModal" class="px-5 py-2 bg-[#023936] w-[180px] cursor-pointer text-white rounded-[10px] inline-flex justify-center items-center gap-2.5">
            Create Material
          </button>
        <?php endif; ?>
      </div>

      <!-- Table Data -->
      <div class="w-full overflow-auto shadow-md rounded-xl">
        <table class="w-full">
          <thead class="border-b-2 border-[#023936] bg-[#E6EBEB]">
            <tr>
              <th class="px-3 py-4 text-left hidden">ID</th>
              <th class="px-3 py-4 text-left">Material</th>
              <th class="px-3 py-4 text-left">Specification</th>
              <th class="px-3 py-4 text-left">Unit</th>
              <th class="px-3 py-4 text-left">Quantity</th>
              <th class="px-3 py-4 text-left">Price</th>
              <?php if ($role === "admin" || $role === "user_rab") {
                  echo '<th class="px-3 py-4 text-left">Supplier</th>';
              } ?>
              <?php if ($role === "supplier" || $role === "admin") {
                  echo '<th class="px-3 py-4 text-left">Action</th>';
              } ?>
            </tr>
          </thead>
          <tbody class="">
            <?php if (empty($data)): ?>
              <tr><td colspan="<?= $role === "admin"
                  ? "8"
                  : "7" ?>" class="py-4 text-center text-gray-500">Data material kosong.</td></tr>
            <?php else: ?>
              <?php foreach ($data as $row): ?>
                <tr class="hover:bg-[#E6EBEB]">
                  <td class="px-3 py-8 hidden"><?= htmlspecialchars(
                      $row["id_material"],
                  ) ?></td>
                  <td class="px-3 py-8"><?= htmlspecialchars(
                      $row["name"],
                  ) ?></td>
                  <td class="px-3 py-8"><?= htmlspecialchars(
                      $row["specification"],
                  ) ?></td>
                  <td class="px-3 py-8"><?= htmlspecialchars(
                      $row["unit"],
                  ) ?></td>
                  <td class="px-3 py-8"><?= htmlspecialchars(
                      $row["quantity"],
                  ) ?></td>
                  <td class="px-3 py-8">Rp <?= number_format(
                      $row["price"],
                      0,
                      ",",
                      ".",
                  ) ?></td>
                  <?php if ($role === "admin" || $role === "user_rab"): ?>
                    <td class="px-3 py-8"><?= htmlspecialchars(
                        $row["supplier_name"] ?? "-",
                    ) ?></td>
                  <?php endif; ?>
                  <?php if ($role === "admin" || $role === "supplier"): ?>
                    <td class="px-3 py-8">
                        <div class="flex items-center gap-3">
                            <!-- Tombol Edit -->
                            <a href="detailmaterial.php?id=<?= $row[
                                "id_material"
                            ] ?>" 
                              class="flex items-center justify-center hover:bg-blue-100 transition-colors w-8 h-8 duration-150 rounded-xs" title="Edit Material">
                              <img src="../assets/image/edit.svg" alt="Edit" class="w-5 h-5">
                            </a>
  
                            <!-- Tombol Hapus -->
                            <a href="?delete=<?= $row["id_material"] ?>" 
                              onclick="return confirm('Apakah Anda yakin ingin menghapus material ini?')"
                              class="flex items-center justify-center hover:bg-blue-100 transition-colors w-8 h-8 duration-150 rounded-xs" title="Hapus Material">
                              <img src="../assets/image/delete.svg" alt="Delete" class="w-5 h-5">
                            </a>
                        </div>
                    </td>
                  <?php endif; ?>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex justify-between items-center mt-[24px]">
          <form method="GET" class="flex items-center gap-2">
            <label for="limit" class="text-sm">Show:</label>
            <select name="limit" id="limit" class="px-2 py-1 rounded" onchange="this.form.submit()">
              <?php foreach ([5, 10, 25, 50] as $opt): ?>
                <option value="<?= $opt ?>" <?= $opt == $limit
    ? "selected"
    : "" ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
            <input autocomplete="off" type="hidden" name="search" value="<?= htmlspecialchars(
                $search,
            ) ?>">
          </form>
            
          <?php if ($totalPages > 1): ?>
          <div class="flex justify-center mt-6">
            <div class="flex items-center gap-1 px-4 py-2 bg-white border border-gray-200 rounded-full shadow-sm">
                
            <!-- Tombol Sebelumnya -->
            <a href="?page=<?= max(
                1,
                $page - 1,
            ) ?>&limit=<?= $limit ?>&search=<?= urlencode($search) ?>"
              class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition text-[#023936] font-semibold">‹</a>

            <?php
            $visiblePages = 3;
            $half = floor($visiblePages / 2);

            $start = max(1, $page - $half);
            $end = min($totalPages, $start + $visiblePages - 1);

            if ($end - $start + 1 < $visiblePages) {
                $start = max(1, $end - $visiblePages + 1);
            }

            // Halaman pertama dan ellipsis
            if ($start > 1) {
                echo '<a href="?page=1&limit=' .
                    $limit .
                    "&search=" .
                    urlencode($search) .
                    '" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-[#023936] transition">1</a>';
                if ($start > 2) {
                    echo '<span class="flex items-center justify-center w-8 h-8 text-gray-400">...</span>';
                }
            }

            // Halaman utama
            for ($i = $start; $i <= $end; $i++): ?>
                  <a href="?page=<?= $i ?>&limit=<?= $limit ?>&search=<?= urlencode(
    $search,
) ?>"
                    class="w-8 h-8 flex items-center justify-center rounded-full font-medium transition 
                            <?= $i == $page
                                ? "bg-[#023936] text-white"
                                : "hover:bg-gray-100 text-[#023936]" ?>">
                    <?= $i ?>
                  </a>
                <?php endfor;

            // Halaman terakhir dan ellipsis
            if ($end < $totalPages) {
                if ($end < $totalPages - 1) {
                    echo '<span class="flex items-center justify-center w-8 h-8 text-gray-400">...</span>';
                }
                echo '<a href="?page=' .
                    $totalPages .
                    "&limit=" .
                    $limit .
                    "&search=" .
                    urlencode($search) .
                    '" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-[#023936] transition">' .
                    $totalPages .
                    "</a>";
            }
            ?>

                <!-- Tombol Selanjutnya -->
                <a href="?page=<?= min(
                    $totalPages,
                    $page + 1,
                ) ?>&limit=<?= $limit ?>&search=<?= urlencode($search) ?>"
                  class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition text-[#023936] font-semibold">›</a>

              </div>
            </div>
          <?php endif; ?>
      </div>
    </div>
  </div>
</main>

<!-- MODAL TAMBAH MATERIAL (Sama seperti sebelumnya, tidak berubah) -->
<!-- MODAL TAMBAH MATERIAL -->
<div id="createMaterialModal"
     class="fixed inset-0 flex items-center justify-center bg-black/40 opacity-0 pointer-events-none transition-opacity duration-300 z-[70]">
  <div id="modalBox"
       class="bg-white rounded-xl p-8 w-[450px] shadow-lg relative transform scale-95 transition-transform duration-300">
    <h2 class="text-2xl font-semibold text-[#FB8E1B] mb-6">Tambah Material</h2>

    <form method="POST" class="flex flex-col gap-4">
      <div>
        <label class="block mb-1 text-sm font-medium">Nama Material</label>
        <input type="text" name="name" required
               class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none">
      </div>

      <div>
        <label class="block mb-1 text-sm font-medium">Spesifikasi</label>
        <textarea name="specification" rows="3"
                  class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none"></textarea>
      </div>

      <div>
        <label class="block mb-1 text-sm font-medium">Satuan</label>
        <input type="text" name="unit" required
               class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none">
      </div>

      <div>
        <label class="block mb-1 text-sm font-medium">Kuantitas</label>
        <input type="number" name="quantity" value="0" min="0"
               class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none">
      </div>

      <div>
        <label class="block mb-1 text-sm font-medium">Harga Satuan (Rp)</label>
        <input type="number" name="price" value="0" min="0"
               class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none">
      </div>

      <div class="flex justify-end gap-3 mt-4">
        <button type="button" id="closeModal"
                class="bg-gray-200 text-[#023936] font-medium px-4 py-2 rounded-md hover:bg-gray-300 transition">
          Batal
        </button>
        <button type="submit" name="create"
                class="bg-[#023936] text-white font-medium px-4 py-2 rounded-md hover:bg-[#035949] transition">
          Tambah
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  const modal = document.getElementById('createMaterialModal');
  const modalBox = document.getElementById('modalBox');
  const openModalBtn = document.getElementById('openModal');
  const closeModalBtn = document.getElementById('closeModal');

  function openModal() {
    modal.classList.remove('pointer-events-none', 'opacity-0');
    modal.classList.add('opacity-100');
    modalBox.classList.remove('scale-95');
    modalBox.classList.add('scale-100');
  }

  function closeModal() {
    modal.classList.add('opacity-0');
    modal.classList.remove('opacity-100');
    modalBox.classList.add('scale-95');
    modalBox.classList.remove('scale-100');
    setTimeout(() => {
      modal.classList.add('pointer-events-none');
    }, 300);
  }

  openModalBtn.addEventListener('click', openModal);
  closeModalBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
  });
</script>

</body>
</html>
