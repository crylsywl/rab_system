<?php
session_start();
include "../partials/sidebar.php";
include "../config/database.php";

// ===== FUNCTIONS =====
function generateVersionId() {
    $microtime = round(microtime(true) * 1000); // timestamp in milliseconds
    $random = mt_rand(1000, 9999);
    return "ver_" . $microtime . "_" . $random;
}

if (!in_array($_SESSION["role"], ["admin", "user_rab"])) {
    header("Location: ../login.php");
    exit();
}

// ===== GET ID RAB dari URL =====
if (!isset($_GET['id'])) {
    die("ID RAB tidak ditemukan");
}
$id_rab = mysqli_real_escape_string($conn, $_GET['id']);

// ===== DELETE VERSI RAB =====
if (isset($_GET["delete_id_version"])) {
    $id_version = mysqli_real_escape_string($conn, $_GET["delete_id_version"]);

    mysqli_begin_transaction($conn);
    try {
        // Hapus detail (isiRAB) dulu
        mysqli_query($conn, "DELETE FROM isirab WHERE id_rab = '$id_rab' AND id_version = '$id_version'");
        
        // Hapus versi RAB
        mysqli_query($conn, "DELETE FROM rab WHERE id_rab = '$id_rab' AND id_version = '$id_version'");
        
        mysqli_commit($conn);
        echo "<script>alert('Versi RAB berhasil dihapus!'); window.location='listbackuprab.php?id=$id_rab';</script>";
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Gagal menghapus versi RAB: " . htmlspecialchars($e->getMessage()) . "');</script>";
    }
}


// ===== SET AS ACTIVE VERSION =====
if (isset($_GET["restore_id_version"])) {
    $id_version_to_activate = mysqli_real_escape_string($conn, $_GET["restore_id_version"]);
    
    mysqli_begin_transaction($conn);
    try {
        // Step 1: Set ALL versions to not latest (for this RAB)
        $stmt_deactivate_all = $conn->prepare("
            UPDATE rab SET is_latest = FALSE 
            WHERE id_rab = ?
        ");
        $stmt_deactivate_all->bind_param("s", $id_rab);
        $stmt_deactivate_all->execute();
        
        // Step 2: Set the selected version to latest
        $stmt_activate = $conn->prepare("
            UPDATE rab SET is_latest = TRUE 
            WHERE id_rab = ? AND id_version = ?
        ");
        $stmt_activate->bind_param("ss", $id_rab, $id_version_to_activate);
        $stmt_activate->execute();
        
        // Check if any row was affected
        if ($stmt_activate->affected_rows === 0) {
            throw new Exception("Versi yang dipilih tidak ditemukan");
        }
        
        mysqli_commit($conn);
        echo "<script>window.location='listbackuprab.php?id=$id_rab';</script>";
        exit();
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Gagal set versi sebagai aktif: " . htmlspecialchars($e->getMessage()) . "');</script>";
    }
}

// =============================
// 🔍 Search & Pagination
// =============================
$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$limit = isset($_GET["limit"]) ? (int) $_GET["limit"] : 5;
$page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;
if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

// =============================
// 📊 Hitung total data (semua versi untuk id_rab tertentu)
// =============================
$safeSearch = $search !== "" ? mysqli_real_escape_string($conn, $search) : "";
$searchCondition = $search !== "" ? " AND created_at LIKE '%$safeSearch%'" : "";

$totalSql = "
    SELECT COUNT(*) AS total 
    FROM rab 
    WHERE id_rab = '$id_rab' $searchCondition
";
$totalQuery = mysqli_query($conn, $totalSql);
$totalRow = mysqli_fetch_assoc($totalQuery);
$total = (int) $totalRow["total"];
$totalPages = max(1, ceil($total / $limit));

// =============================
// 🧾 Ambil semua versi RAB untuk id_rab tertentu
// =============================
$query = "
    SELECT 
        id_rab,
        id_version,
        project_name,
        unit,
        type,
        location,
        jumlah_total_budget,
        pembulatan_budget,
        permeterpersegi_budget,
        jumlah_total_additional,
        pembulatan_additional,
        permeterpersegi_additional,
        version_description,
        created_at,
        is_latest
    FROM rab 
    WHERE id_rab = '$id_rab' $searchCondition
    ORDER BY created_at DESC
    LIMIT $limit OFFSET $offset
";

$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// =============================
// 🎯 Get project info untuk header
// =============================
$projectInfoQuery = "SELECT project_name FROM rab WHERE id_rab = '$id_rab' LIMIT 1";
$projectInfoResult = mysqli_query($conn, $projectInfoQuery);
$projectInfo = mysqli_fetch_assoc($projectInfoResult);
$projectName = $projectInfo ? $projectInfo['project_name'] : 'Unknown Project';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Versi RAB - <?= htmlspecialchars($projectName) ?></title>
  <link href="../assets/css/output.css" rel="stylesheet">
</head>

<body class="bg-white">
<main id="main-content" 
  class="ml-56 mt-[24px] mb-[24px] mr-[24px] transition-all duration-300 ease-in-out p-[40px] min-h-screen bg-zinc-100 rounded-[20px]">

  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-[#FB8E1B]">RAB Versions - <?= htmlspecialchars($projectName) ?></h1>
      <p class="text-sm text-gray-600">
        View all budget plan versions for <?= htmlspecialchars($projectName) ?>
      </p>
    </div>
    <div class="flex items-center gap-3">
      <p class="text-sm font-semibold text-gray-800">
        Welcome back, <?= htmlspecialchars($_SESSION["nama"]) ?>
      </p>
      <img src="../assets/image/profile.svg" alt="Profile Icon" class="w-7 h-7 rounded-full cursor-pointer">
    </div>
  </div>

  <div class="w-full mx-auto bg-white rounded-[20px] p-[40px]">
    <!-- Top content -->
    <div class="flex flex-col-reverse gap-3 lg:flex-row lg:items-center lg:justify-between mb-[24px]">
      <form method="GET" class="flex items-center">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id_rab) ?>">
        <input autocomplete="off" type="text" name="search" value="<?= htmlspecialchars($search) ?>"
          class="bg-zinc-100 rounded-tl-[10px] rounded-bl-[10px] px-3 py-2 w-60 outline-none"
          placeholder="Search version...">
        <?php if ($search): ?>
          <a href="?id=<?= urlencode($id_rab) ?>&?page=1">
            <img src="../assets/image/clear.svg" alt="clear" class="absolute flex-reverse -ml-[30px] -mt-[12px]">
          </a>
        <?php endif; ?>
        <button type="submit" class="bg-[#023936] text-white px-4 py-2 rounded-tr-[10px] rounded-br-[10px] h-[40px] flex items-center">
          <img src="../assets/image/search.svg" alt="search">
        </button>
      </form>
    </div>

    <!-- Table -->
    <div class="w-full overflow-auto rounded-xl shadow-md">
      <table class="w-full">
        <thead class="border-[#023936] bg-[#E6EBEB] border-b-2">
          <tr>
            <th class="py-4 px-3 text-left">Versi</th>
            <th class="py-4 px-3 text-left">Deskripsi</th>
            <th class="py-4 px-3 text-left">Status</th>
            <th class="py-4 px-3 text-left">Total Budget</th>
            <th class="py-4 px-3 text-left">Pembulatan</th>
            <th class="py-4 px-3 text-left">Per m²</th>
            <th class="py-4 px-3 text-center">Action</th>
          </tr>
        </thead>
        <tbody class="">
          <?php if (empty($data)): ?>
            <tr><td colspan="8" class="text-center py-8 text-gray-500">Belum ada data versi RAB.</td></tr>
          <?php else: ?>
            <?php foreach ($data as $row): ?>
              <tr class="hover:bg-[#E6EBEB] <?= $row['is_latest'] ? 'bg-green-50' : '' ?>">
                <td class="py-8 px-3">
                  <?= htmlspecialchars(date('d M Y H:i:s', strtotime($row["created_at"]))) ?>
                </td>
                <td class="py-8 px-3">
                  <?= htmlspecialchars($row["version_description"] ?? 'No description') ?>
                  <?= $row['is_latest'] ? '<span class="ml-2 px-2 py-1 bg-green-100 text-green-800 font-bold text-xs rounded">ACCEPTED</span>' : '' ?>
                </td>
                <td class="py-8 px-3">
                  <?= $row['is_latest'] ? 'Aktif' : 'Backup' ?>
                </td>
                <td class="py-8 px-3">Rp <?= number_format(
                    $row["jumlah_total_additional"],
                    0,
                    ",",
                    "."
                ) ?></td>
                <td class="py-8 px-3">Rp <?= number_format(
                    $row["pembulatan_additional"],
                    0,
                    ",",
                    "."
                ) ?></td>
                <td class="py-8 px-3">Rp <?= number_format(
                    $row["permeterpersegi_additional"],
                    0,
                    ",",
                    "."
                ) ?></td>
                <td class="py-8 px-3 text-center">
                  <?php if ($row['is_latest']): ?>
                    <div class="flex flex-row gap-2 justify-center items-center">
                      <a href="detailrab.php?id_rab=<?= urlencode($row['id_rab']) ?>&id_version=<?= urlencode($row['id_version']) ?>" 
                        class="flex items-center justify-center hover:bg-blue-50 transition-colors w-8 h-8 duration-150 rounded-xs"
                        text="Edit RAB">
                              <img src="../assets/image/edit.svg" alt="Edit" >
                      </a>
                      <a href="" 
                        class="flex items-center justify-center hover:bg-blue-50 transition-colors w-8 h-8 duration-150 rounded-xs"
                        text="Print RAB">
                              <img src="../assets/image/print.svg" alt="Print" >
                      </a>
                    </div>
                  <?php else: ?>
                    <div class="relative" style="position: static;">
                        <!-- Dropdown Trigger -->
                        <button type="button" 
                                class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-gray-50 hover:bg-gray-100 transition-all duration-200 focus:outline-none border border-gray-200"
                                id="dropdown-button-<?= $row['id_version'] ?>"
                                onclick="toggleDropdown('<?= $row['id_version'] ?>')">
                            <!-- Three dots icon -->
                            <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="dropdown-menu-<?= $row['id_version'] ?>" 
                            class="hidden absolute right-0 mt-1 w-56 bg-white rounded-lg shadow-xl  z-50 transform transition-all duration-200 ease-out origin-top-right">
                            <div class="" role="menu" aria-orientation="vertical">
                                
                                <!-- Edit Action -->
                                <a href="detailrab.php?id_rab=<?= urlencode($row['id_rab']) ?>&id_version=<?= urlencode($row['id_version']) ?>" 
                                  class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150 group"
                                  role="menuitem">
                                    <div class="mr-3">
                                        <img src="../assets/image/edit.svg" alt="Edit" class="">
                                    </div>
                                    <span class="font-medium">Edit RAB</span>
                                </a>

                                <a href=""
                                  class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150 group"
                                  role="menuitem">
                                    <div class="mr-3">
                                        <img src="../assets/image/print.svg" alt="Print" class="">
                                    </div>
                                    <span class="font-medium">Print RAB</span>
                                </a>

                                <a href="?id=<?= urlencode($id_rab) ?>&restore_id_version=<?= urlencode($row["id_version"]) ?>"
                                  onclick="return confirm('Apakah Anda yakin ingin SET versi ini sebagai VERSI AKTIF?')"
                                  class="flex items-center px-4 py-3 text-sm text-green-700 hover:bg-green-50 hover:text-green-800 transition-colors duration-150 group"
                                  role="menuitem">
                                    <div class="mr-3">
                                        <img src="../assets/image/active.svg" alt="Set Active" class="">
                                    </div>
                                    <span class="font-medium">Set sebagai Aktif</span>
                                </a>

                                <a href="?id=<?= urlencode($id_rab) ?>&delete_id_version=<?= urlencode($row["id_version"]) ?>"
                                  onclick="return confirm('Apakah Anda yakin ingin menghapus versi ini?')"
                                  class="flex items-center px-4 py-3 text-sm text-red-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-150 group"
                                  role="menuitem">
                                    <div class="mr-3">
                                        <img src="../assets/image/delete.svg" alt="Delete" class="">
                                    </div>
                                    <span class="font-medium">Hapus Versi</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
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
        <input type="hidden" name="id" value="<?= htmlspecialchars($id_rab) ?>">
        <label for="limit" class="text-sm">Show:</label>
        <select name="limit" id="limit" class="px-2 py-1 rounded" onchange="this.form.submit()">
          <?php foreach ([5, 10, 25, 50] as $opt): ?>
            <option value="<?= $opt ?>" <?= $opt == $limit ? "selected" : "" ?>><?= $opt ?></option>
          <?php endforeach; ?>
        </select>
        <input autocomplete="off" type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
      </form>
        
      <?php if ($totalPages > 1): ?>
      <div class="flex justify-center mt-6">
        <div class="flex items-center gap-1 px-4 py-2 bg-white border border-gray-200 rounded-full shadow-sm">
            
        <!-- Tombol Sebelumnya -->
        <a href="?id=<?= urlencode($id_rab) ?>&page=<?= max(1, $page - 1) ?>&limit=<?= $limit ?>&search=<?= urlencode($search) ?>"
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
            echo '<a href="?id=' . urlencode($id_rab) . '&page=1&limit=' . $limit . '&search=' . urlencode($search) . '" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-[#023936] transition">1</a>';
            if ($start > 2) {
                echo '<span class="flex items-center justify-center w-8 h-8 text-gray-400">...</span>';
            }
        }

        // Halaman utama
        for ($i = $start; $i <= $end; $i++): ?>
              <a href="?id=<?= urlencode($id_rab) ?>&page=<?= $i ?>&limit=<?= $limit ?>&search=<?= urlencode($search) ?>"
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
            echo '<a href="?id=' . urlencode($id_rab) . '&page=' . $totalPages . '&limit=' . $limit . '&search=' . urlencode($search) . '" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-[#023936] transition">' . $totalPages . '</a>';
        }
        ?>

            <!-- Tombol Selanjutnya -->
            <a href="?id=<?= urlencode($id_rab) ?>&page=<?= min($totalPages, $page + 1) ?>&limit=<?= $limit ?>&search=<?= urlencode($search) ?>"
              class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition text-[#023936] font-semibold">›</a>

          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<script>
  function toggleDropdown(id_version) {
    const dropdownMenu = document.getElementById('dropdown-menu-' + id_version);
    const dropdownButton = document.getElementById('dropdown-button-' + id_version);
    
    // Tutup semua dropdown lainnya
    document.querySelectorAll('[id^="dropdown-menu-"]').forEach(menu => {
        if (menu.id !== 'dropdown-menu-' + id_version) {
          menu.classList.add('hidden');
        }
      });
      
      // Toggle dropdown saat ini
      dropdownMenu.classList.toggle('hidden');
      
    // Position dropdown berdasarkan button trigger
    if (!dropdownMenu.classList.contains('hidden')) {
      const rect = dropdownButton.getBoundingClientRect();
      dropdownMenu.style.left = (rect.left - 200) + 'px'; // Adjust position
      dropdownMenu.style.top = (rect.bottom + 5) + 'px';
    }
  }

  // Tutup dropdown ketika klik di luar
  document.addEventListener('click', function(e) {
    if (!e.target.closest('.relative')) {
      document.querySelectorAll('[id^="dropdown-menu-"]').forEach(menu => {
        menu.classList.add('hidden');
      });
      }
  });
</script>

</body>
</html>