<?php
session_start();
include '../partials/sidebar.php';
include '../config/database.php';

// Pastikan hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    header("Location: admin.php");
    exit;
}

// Konfigurasi search & pagination
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Hitung offset
$offset = ($page - 1) * $limit;

// Query dasar
$whereClause = "WHERE role='supplier'";
if ($search !== '') {
    $safeSearch = mysqli_real_escape_string($conn, $search);
    $whereClause .= " AND nama LIKE '%$safeSearch%'";
}

// Ambil total data
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM users $whereClause");
$totalRow = mysqli_fetch_assoc($totalQuery);
$total = $totalRow['total'];
$totalPages = ceil($total / $limit);

// Ambil data per halaman
$query = "SELECT * FROM users $whereClause ORDER BY id_user ASC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Ambil hasil ke array
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// ===== CREATE SUPPLIER =====
if (isset($_POST['create'])) {
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        echo "<script>alert('Konfirmasi password tidak cocok!');</script>";
    } else {
        // Enkripsi password
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Insert ke tabel users
        $stmt = $conn->prepare("INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, 'supplier')");
        $stmt->bind_param("sss", $nama, $username, $hashed);
        $stmt->execute();

        // Ambil id_user terakhir
        $id_user = mysqli_insert_id($conn);

        // Insert ke tabel supplier dengan data awal null
        $stmt2 = $conn->prepare("INSERT INTO supplier (id_user, email, no_telp, address) VALUES (?, NULL, NULL, NULL)");
        $stmt2->bind_param("i", $id_user);
        $stmt2->execute();

        echo "<script>alert('Supplier berhasil ditambahkan ke dua tabel!'); window.location='supplier.php';</script>";
    }
}


// ===== DELETE USER =====
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete = mysqli_query($conn, "DELETE FROM rab WHERE id_rab = $id");
    if ($delete) {
        echo "<script>alert('Supplier berhasil dihapus!'); window.location='supplier.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus supplier!');</script>";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman Supplier</title>
  <link href="../assets/css/output.css" rel="stylesheet">
</head>
<body class="bg-white">
  <main id="main-content" 
      class="ml-56 mt-[24px] mb-[24px] mr-[24px] transition-all duration-300 ease-in-out p-[40px] min-h-screen bg-zinc-100 rounded-[20px]">
  <div class="">
      <div class="flex items-center justify-between mb-6">
     
        <div>
          <h1 class="text-2xl font-semibold text-[#FB8E1B]">Supplier</h1>
          <p class="text-sm text-gray-600">
            List of all Suppliers, create new supplier and manage existing ones.
          </p>
        </div>
    
        <!-- Right side: Profile info -->
        <div class="flex items-center gap-3">
          <p class="text-sm font-semibold text-gray-800">
            Welcome back, <?= htmlspecialchars($_SESSION['nama']) ?>
          </p>
          <img src="../assets/image/profile.svg" alt="Profile Icon" class="w-7 h-7 rounded-full cursor-pointer">
        </div>
      </div>
    
      <!-- Main Content Body -->
    <div class="w-full mx-auto bg-white rounded-[20px] p-[40px]">
        <!-- TopContent -->
        <div class="flex flex-col-reverse gap-3 lg:flex-row lg:items-center lg:justify-between mb-[24px]">
        <form method="GET" class="flex items-center">
            <input autocomplete="off" type="text" name="search" value="<?= htmlspecialchars($search) ?>"
            class="bg-zinc-100 rounded-tl-[10px] rounded-bl-[10px] px-3 py-2 w-60 outline-none focus:zinc-100"
            placeholder="Search by name...">
              <?php if ($search): ?>
              <a href="?page=1" class=" ">
                <img src="../assets/image/clear.svg" alt="clear" class="absolute flex-reverse -ml-[30px] -mt-[12px]">
              </a>
              <?php endif; ?>
            <button type="submit" class="bg-[#023936] text-white px-4 py-2 cursor-pointer rounded-tr-[10px] rounded-br-[10px] inline-flex h-[40px] justify-center items-center">
                <img src="../assets/image/search.svg" alt="">
            </button>
        </form>

        <button id="openModal" class="px-5 py-2 bg-[#023936] w-auto cursor-pointer text-white rounded-[10px] inline-flex justify-center items-center gap-2.5">Create Supplier</button>
        </div>

        <!-- Table -->
        <div class="w-full overflow-auto rounded-xl shadow-md">
        <table class="w-full">
            <thead class="border-[#023936] bg-[#E6EBEB] border-b-2">
              <tr>
                <th class="py-4 tracking-wide px-3 text-left hidden">ID</th>
                <th class="py-4 tracking-wide px-3 text-left">Nama</th>
                <th class="py-4 tracking-wide px-3 text-left">Username</th>
                <th class="py-4 tracking-wide px-3 text-left">Action</th>
              </tr>
            </thead>
            <tbody class="">
              <?php if (empty($data)): ?>
                <tr><td colspan="4" class="text-center py-4 text-gray-500">Data kosong.</td></tr>
              <?php else: ?>
                <?php foreach ($data as $row): ?>
                  <tr class="hover:bg-[#E6EBEB]">
                    <td class="py-8 px-3 hidden"><?= htmlspecialchars($row['id_user']) ?></td>
                    <td class="py-8 px-3"><?= htmlspecialchars($row['nama']) ?></td>
                    <td class="py-8 px-3"><?= htmlspecialchars($row['username'] ?? '-') ?></td>
                    <td class="py-8 px-3">
                      <div class="flex gap-3 items-center">
                        <!-- Tombol Edit -->
                        <a href="detailsupplier.php?id=<?= $row['id_user'] ?>" 
                          class="flex items-center justify-center hover:bg-blue-100 transition-colors w-8 h-8 duration-150 rounded-xs" title="Edit Supplier">
                          <img src="../assets/image/edit.svg" alt="Edit" class="w-5 h-5">
                        </a>

                        <!-- Tombol Hapus -->
                        <a href="?delete=<?= $row['id_user'] ?>" 
                          onclick="return confirm('Apakah Anda yakin ingin menghapus supplier ini?')"
                          class="flex items-center justify-center hover:bg-blue-100 transition-colors w-8 h-8 duration-150 rounded-xs" title="Hapus Supplier">
                          <img src="../assets/image/delete.svg" alt="Delete" class="w-5 h-5">
                        </a>
                      </div>
                    </td>
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

<!-- MODAL CREATE USER -->
<div id="createUserModal"
     class="fixed inset-0 flex items-center justify-center bg-black/40 opacity-0 pointer-events-none transition-opacity duration-300 z-[70]">
  
  <div id="modalBox"
       class="bg-white rounded-xl p-8 w-[400px] shadow-lg relative transform scale-95 transition-transform duration-300">
    <h2 class="text-2xl font-semibold text-[#FB8E1B] mb-6">Add Supplier</h2>

    <form method="POST" class="flex flex-col gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input autocomplete="off" type="text" name="nama" required 
               class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Username</label>
        <input autocomplete="off" type="text" name="username" required 
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

      <div class="flex justify-end gap-3 mt-4">
        <button type="button" id="closeModal"
                class="bg-gray-200 text-[#023936] font-medium px-4 py-2 rounded-md hover:bg-gray-300 transition">
          Cancel
        </button>
        <button type="submit" name="create"
                class="bg-[#023936] text-white font-medium px-4 py-2 rounded-md hover:bg-[#035949] transition">
          Create Supplier
        </button>
      </div>
    </form>
  </div>
</div>


<script>
  const modal = document.getElementById('createUserModal');
  const modalBox = document.getElementById('modalBox');
  const openModalBtn = document.getElementById('openModal');
  const closeModalBtn = document.getElementById('closeModal');

  function openModal() {
    // tampilkan modal
    modal.classList.remove('pointer-events-none');
    modal.classList.remove('opacity-0');
    modal.classList.add('opacity-100');

    // animasi scale modal box
    modalBox.classList.remove('scale-95');
    modalBox.classList.add('scale-100');
  }

  function closeModal() {
    // efek keluar halus
    modal.classList.add('opacity-0');
    modal.classList.remove('opacity-100');
    modalBox.classList.add('scale-95');
    modalBox.classList.remove('scale-100');

    // sembunyikan modal setelah animasi selesai
    setTimeout(() => {
      modal.classList.add('pointer-events-none');
    }, 300);
  }

  openModalBtn.addEventListener('click', openModal);
  closeModalBtn.addEventListener('click', closeModal);

  // Tutup modal jika klik area luar
  modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
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
