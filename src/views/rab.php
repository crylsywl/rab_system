<?php
session_start();
include "../partials/sidebar.php";
include "../config/database.php";

if (!in_array($_SESSION["role"], ["admin", "user_rab"])) {
    header("Location: ../login.php");
    exit();
}

// ===== DELETE RAB (hapus semua versi untuk 1 id_rab) =====
if (isset($_GET["delete_id_rab"])) {
    $id = mysqli_real_escape_string($conn, $_GET["delete_id_rab"]);

    mysqli_begin_transaction($conn);
    try {
        // Hapus detail (isiRAB) dulu — jika FK tidak cascade
        mysqli_query($conn, "DELETE FROM isirab WHERE id_rab = '$id'");

        // Hapus semua versi dari RAB itu
        mysqli_query($conn, "DELETE FROM rab WHERE id_rab = '$id'");

        mysqli_commit($conn);
        echo "<script>alert('RAB dan semua versinya berhasil dihapus!'); window.location='rab.php';</script>";
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Gagal menghapus RAB: " .
            htmlspecialchars($e->getMessage()) .
            "');</script>";
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

$whereClause = "WHERE 1";
if ($search !== "") {
    $safeSearch = mysqli_real_escape_string($conn, $search);
    $whereClause .= " AND r1.projectName LIKE '%$safeSearch%'";
}

// =============================
// 📊 Hitung total data (versi terbaru per id_rab)
// =============================
$safeSearch = $search !== "" ? mysqli_real_escape_string($conn, $search) : "";
$searchSql = $search !== "" ? "WHERE r.project_name LIKE '%$safeSearch%'" : "";

$totalSql = "
  SELECT COUNT(*) AS total 
  FROM rab r
  WHERE r.is_latest = TRUE
  " . ($search !== "" ? " AND r.project_name LIKE '%$safeSearch%'" : "");
$totalQuery = mysqli_query($conn, $totalSql);
$totalRow = mysqli_fetch_assoc($totalQuery);
$total = (int) $totalRow["total"];
$totalPages = max(1, ceil($total / $limit));

// =============================
// 🧾 Ambil data versi terbaru per RAB
// =============================
$query = "
  SELECT 
    r.id_rab,
    r.project_name,
    r.unit,
    r.type,
    r.location,
    r.jumlah_total_budget,
    r.pembulatan_budget,
    r.permeterpersegi_budget,
    r.jumlah_total_additional,
    r.pembulatan_additional,
    r.permeterpersegi_additional,
    r.created_at
  FROM rab r
  WHERE r.is_latest = TRUE
  " . ($search !== "" ? " AND r.project_name LIKE '%$safeSearch%'" : "") . "
  ORDER BY r.created_at DESC
  LIMIT $limit OFFSET $offset
";

$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halaman RAB</title>
  <link href="../assets/css/output.css" rel="stylesheet">
</head>

<body class="bg-white">
<main id="main-content" 
  class="ml-56 mt-[24px] mb-[24px] mr-[24px] transition-all duration-300 ease-in-out p-[40px] min-h-screen bg-zinc-100 rounded-[20px]">

  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-[#FB8E1B]">RAB</h1>
      <p class="text-sm text-gray-600">
        List of all RAB, create new RAB and manage existing ones.
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
        <input autocomplete="off" type="text" name="search" value="<?= htmlspecialchars(
            $search,
        ) ?>"
          class="bg-zinc-100 rounded-tl-[10px] rounded-bl-[10px] px-3 py-2 w-60 outline-none"
          placeholder="Search project...">
        <?php if ($search): ?>
          <a href="?page=1">
                <img src="../assets/image/clear.svg" alt="clear" class="absolute flex-reverse -ml-[30px] -mt-[12px]">
          </a>
        <?php endif; ?>
        <button type="submit" class="bg-[#023936] text-white px-4 py-2 rounded-tr-[10px] rounded-br-[10px] h-[40px] flex items-center">
          <img src="../assets/image/search.svg" alt="search">
        </button>
      </form>

      <a href="create_rab.php">
        <button class="px-5 py-2 bg-[#023936] w-[148px] text-white rounded-[10px] flex items-center justify-center">
          Create RAB
        </button>
      </a>
    </div>

    <!-- Table -->
    <div class="w-full overflow-auto rounded-xl shadow-md">
      <table class="w-full">
        <thead class="border-[#023936] bg-[#E6EBEB] border-b-2">
          <tr>
            <th class="py-4 px-3 text-left hidden">ID RAB</th>
            <th class="py-4 px-3 text-left">Project Name</th>
            <th class="py-4 px-3 text-left">Housing units</th>
            <th class="py-4 px-3 text-left">Type</th>
            <th class="py-4 px-3 text-left">Location</th>
            <th class="py-4 px-3 text-left">Total Cost</th>
            <th class="py-4 px-3 text-left">Rounding</th>
            <th class="py-4 px-3 text-left">Per m²</th>
            <th class="py-4 px-3 text-left">Created At</th>
            <th class="py-4 px-3 text-left">Action</th>
          </tr>
        </thead>
        <tbody class="">
          <?php if (empty($data)): ?>
            <tr><td colspan="10" class="text-center py-4 text-gray-500">Belum ada data RAB.</td></tr>
          <?php else: ?>
            <?php foreach ($data as $row): ?>
              <tr class="hover:bg-[#E6EBEB]">
                <td class="py-6 px-3 hidden"><?= htmlspecialchars(
                    $row["id_rab"],
                ) ?></td>
                <td class="py-6 px-3"><?= htmlspecialchars(
                    $row["project_name"],
                ) ?></td>
                <td class="py-6 px-3 text-center"><?= htmlspecialchars($row["unit"]) ?></td>
                <td class="py-6 px-3"><?= htmlspecialchars($row["type"]) ?></td>
                <td class="py-6 px-3"><?= htmlspecialchars(
                    $row["location"],
                ) ?></td>
                <td class="py-6 px-3">Rp <?= number_format(
                    $row["jumlah_total_additional"],
                    0,
                    ",",
                    ".",
                ) ?></td>
                <td class="py-6 px-3">Rp <?= number_format(
                    $row["pembulatan_additional"],
                    0,
                    ",",
                    ".",
                ) ?></td>
                <td class="py-6 px-3">Rp <?= number_format(
                    $row["permeterpersegi_additional"],
                    0,
                    ",",
                    ".",
                ) ?></td>
                <td class="py-6 px-3"><?= htmlspecialchars(
                    $row["created_at"],
                ) ?></td>
                <td class="py-6 px-3">
                  <div class="flex gap-3 items-center">
                    <!-- Detail: menuju halaman list backup versi -->
                    <a href="listbackuprab.php?id=<?= urlencode(
                        $row["id_rab"],
                    ) ?>" 
                      class="flex items-center justify-center hover:bg-blue-100 transition-colors w-8 h-8 duration-150 rounded-xs" title="Lihat Versi RAB">
                      <img src="../assets/image/edit.svg" alt="Detail" class="w-5 h-5">
                    </a>

                    <!-- Hapus: hapus semua versi RAB -->
                    <a href="?delete_id_rab=<?= urlencode($row["id_rab"]) ?>"
                      onclick=return confirm('Apakah Anda yakin ingin menghapus SEMUA versi dari RAB <?= htmlspecialchars(
                          $row["id_rab"],
                      ) ?>?')
                      class="flex items-center justify-center hover:bg-blue-100 transition-colors w-8 h-8 duration-150 rounded-xs" title="Hapus RAB">
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
</main>
</body>
</html>
