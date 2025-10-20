<?php
session_start();
include "../partials/sidebar.php";
include "../config/database.php";

// ==============================
// 🧾 FUNCTION CREATE RAB (NEW VERSION) - TIMESTAMP BASED
// ==============================
if (isset($_POST["create"])) {
    // Helper untuk generate IDs based on timestamp + random
    function generateTimestampId($prefix)
    {
        $microtime = round(microtime(true) * 1000); // timestamp in milliseconds
        $random = mt_rand(1000, 9999);
        return $prefix . "_" . $microtime . "_" . $random;
    }

    function generateRabId()
    {
        return generateTimestampId("rab");
    }

    function generateVersionId()
    {
        return generateTimestampId("ver");
    }

    function generateMaterialId()
    {
        $microtime = round(microtime(true) * 1000); // timestamp in milliseconds
        $random1 = mt_rand(1000, 9999);
        $random2 = mt_rand(1000, 9999);
        return "mat_" . $microtime . "_" . $random1 . "_" . $random2;
    }

    $id_rab = generateRabId();
    $id_version = generateVersionId();
    $id_user = $_SESSION["id_user"];
    $projectName = mysqli_real_escape_string($conn, $_POST["project_name"]);
    $unit = (int) $_POST["unit"];
    $type = (int) $_POST["type"];
    $location = mysqli_real_escape_string($conn, $_POST["location"]);

    // Ambil total dari input
    $jumlahTotalBudget = str_replace(
        ".",
        "",
        $_POST["grandTotalBudget"] ?? "0",
    );
    $pembulatanBudget = str_replace(".", "", $_POST["pembulatanBudget"] ?? "0");
    $permeterBudget = str_replace(".", "", $_POST["permeterBudget"] ?? "0");

    $jumlahTotalAdditional = str_replace(
        ".",
        "",
        $_POST["grandTotalAdditional"] ?? "0",
    );
    $pembulatanAdditional = str_replace(
        ".",
        "",
        $_POST["pembulatanAdditional"] ?? "0",
    );
    $permeterAdditional = str_replace(
        ".",
        "",
        $_POST["permeterAdditional"] ?? "0",
    );

    try {
        $conn->begin_transaction();

        // ===== Simpan ke tabel rab (NEW SCHEMA)
        $queryRAB = "
              INSERT INTO rab (
                  id_rab, id_version, id_user, project_name, unit, type, location,
                  jumlah_total_budget, pembulatan_budget, permeterpersegi_budget,
                  jumlah_total_additional, pembulatan_additional, permeterpersegi_additional,
                  version_description, is_latest
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE)
          ";

        $stmt_rab = $conn->prepare($queryRAB);
        $version_description = "Initial Version";
        $stmt_rab->bind_param(
            "ssssiisiiiiiss",
            $id_rab,
            $id_version,
            $id_user,
            $projectName,
            $unit,
            $type,
            $location,
            $jumlahTotalBudget,
            $pembulatanBudget,
            $permeterBudget,
            $jumlahTotalAdditional,
            $pembulatanAdditional,
            $permeterAdditional,
            $version_description,
        );
        $stmt_rab->execute();

        // ==============================
        // Simpan semua material dari form - DENGAN DEBUG
        // ==============================
        if (!empty($_POST["material_name"])) {
            $materials = $_POST["material_name"];
            $units = $_POST["unit_material"] ?? [];
            $qtys = $_POST["quantity"] ?? [];
            $prices = $_POST["unit_price"] ?? [];
            $categories = $_POST["category"] ?? [];
            $material_ids = $_POST["id_material"] ?? [];
            $bagians = $_POST["bagian"] ?? [];
            $material_category_ids = $_POST["material_category_id"] ?? [];
            $material_bagians = $_POST["material_bagian"] ?? [];

            // Build category mapping
            $categoryMap = [];
            foreach ($categories as $idx => $categoryName) {
                $categoryId = $_POST["category_id"][$idx] ?? $idx;
                $categoryMap[$categoryId] = mysqli_real_escape_string(
                    $conn,
                    $categoryName,
                );
            }

            $stmt_item = $conn->prepare("
                  INSERT INTO isirab (
                      id_rab, id_version, id_material, bagian, category, material_name,
                      unit, quantity, unit_price, total_cost
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
              ");

            foreach ($materials as $idx => $materialName) {
                if (empty(trim($materialName))) {
                    continue;
                }

                $materialName = mysqli_real_escape_string($conn, $materialName);
                $unitVal = mysqli_real_escape_string($conn, $units[$idx] ?? "");
                $qty = (int) ($qtys[$idx] ?? 0);
                $price = (int) str_replace(".", "", $prices[$idx] ?? "0");
                $total = (int) str_replace(
                    ".",
                    "",
                    $_POST["total_cost"][$idx] ?? "0",
                );

                // Get category from mapping
                $categoryId = $material_category_ids[$idx] ?? $idx;
                $category = $categoryMap[$categoryId] ?? "-";
                if (empty(trim($category))) {
                    $category = "-";
                }

                // Gunakan material_bagian jika ada, fallback ke kategori bagian
                $bagian = mysqli_real_escape_string(
                    $conn,
                    $material_bagians[$idx] ??
                        ($bagians[$categoryId] ?? "Budget"),
                );

                // MODIFIKASI: Cek apakah id_material sudah ada dari modal
                if (!empty($material_ids[$idx])) {
                    // Gunakan ID yang sudah ada dari modal material
                    $id_material = mysqli_real_escape_string($conn, $material_ids[$idx]);
                    error_log("✅ USING EXISTING material ID: " . $id_material . " for material: " . $materialName);
                } else {
                    // Generate new ID hanya jika tidak ada ID dari modal
                    $id_material = generateMaterialId();
                    error_log("🆕 GENERATING NEW material ID: " . $id_material . " for material: " . $materialName);
                }
                
                $stmt_item->bind_param(
                    "sssssssiii",
                    $id_rab,
                    $id_version,
                    $id_material,
                    $bagian,
                    $category,
                    $materialName,
                    $unitVal,
                    $qty,
                    $price,
                    $total,
                );

                if (!$stmt_item->execute()) {
                    error_log(
                        "❌ FAILED to execute statement: " . $stmt_item->error,
                    );
                    error_log(
                        "Data: " .
                            print_r(
                                [
                                    "material" => $materialName,
                                    "unit" => $unitVal,
                                    "qty" => $qty,
                                    "price" => $price,
                                ],
                                true,
                            ),
                    );
                } else {
                    error_log(
                        "✅ SUCCESS saving material: " .
                            $materialName .
                            " with unit: " .
                            $unitVal,
                    );
                }
            }
        }

        $conn->commit();
        echo "<script>alert('RAB berhasil dibuat!'); window.location='rab.php';</script>";
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Gagal menyimpan RAB: " . $e->getMessage());
    }
}

// Konfigurasi search & pagination untuk modal material
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

// Ambil data per halaman
if ($role === "admin") {
    $query = "SELECT m.*, u.nama AS supplier_name 
                FROM material m
                LEFT JOIN users u ON m.id_user = u.id_user
                $whereClause
                ORDER BY m.id_material ASC";
} else {
    $query = "SELECT * FROM material $whereClause ORDER BY id_material ASC";
}
$result = mysqli_query($conn, $query);
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create RAB</title>
  <link href="../assets/css/output.css" rel="stylesheet">
</head>
<body class="bg-white">

<main id="main-content" 
      class="ml-56 mt-[24px] mb-[24px] mr-[24px] transition-all duration-300 ease-in-out p-[40px] min-h-screen bg-zinc-100 rounded-[20px]">

  <!-- Header -->
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-[#FB8E1B]">Add RAB</h1>
      <p class="text-sm text-gray-600">Create a new RAB for a project.</p>
    </div>
    <div class="flex items-center gap-3">
      <p class="text-sm font-semibold text-gray-800">
        Welcome back, <?= htmlspecialchars($_SESSION["nama"]) ?>
      </p>
      <img src="../assets/image/profile.svg" alt="Profile Icon" class="rounded-full cursor-pointer w-7 h-7">
    </div>
  </div>

  <!-- Tabs -->
  <div>
    <div class="flex mb-4 bg-[#e7e7e7] p-1 shadow-inner rounded-[10px] w-fit">
      <button id="tabInfo" class="px-4 py-1 font-semibold rounded-[10px] transition bg-white shadow-sm hover:text-black">Info</button>
      <button id="tabBudget" class="px-4 py-1 font-semibold rounded-[10px] transition text-gray-500 hover:text-black ">Budget</button>
      <button id="tabAdditional" class="px-4 py-1 font-semibold rounded-[10px] transition text-gray-500 hover:text-black ">Additional</button>
    </div>

    <form action="" method="POST">
        <div class="bg-white rounded-[10px] p-6 shadow-md min-w-[450px] max-w-screen w-full">
            
            <!-- TAB 1: Info -->
            <div id="contentInfo" class="w-70 flex flex-col gap-4">
                <div>
                    <h1 class="text-xl font-semibold text-[#FB8E1B]">Information</h1>
                    <p class="text-sm text-gray-600">Input info for this RAB.</p>
                </div>
                <div>
                  <label class="block mb-1 text-sm font-medium">Project Name</label>
                  <input type="text" name="project_name" required
                         class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none"
                         required
                         >
                </div>
        
                <div>
                  <label class="block mb-1 text-sm font-medium">Number of units (Jumlah Rumah)</label>
                  <input type="number" name="unit" id="unit" required min="0"
                         class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none"
                         required
                         >
                </div>
        
                <div>
                  <label class="block mb-1 text-sm font-medium">Type</label>
                  <input type="number" name="type" id="type" required min="0"
                         class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none"
                         required
                         >
                </div>
        
                <div>
                  <label class="block mb-1 text-sm font-medium">Location</label>
                  <input type="text" name="location" required
                         class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none"
                         required
                         >
                </div>
            </div>
        
            <!-- TAB 2: Budget -->
            <div id="contentBudget" class="hidden">
                <div id="budget-container" class="w-full"></div>
                <button type="button" id="addCategoryBudget" class="bg-[#023936] text-white font-medium px-4 py-2 rounded-md hover:bg-[#035949] transition flex">
                <img src="../assets/image/add.svg" alt="add" class="mr-[8px]"> Add Category
                </button>

                <hr class="w-full my-4">
                <div class="flex flex-col gap-3 w-fit">
                    <!-- Total Keseluruhan -->
                    <div class="flex items-center justify-between w-full gap-4">
                        <label for="grandTotalBudget" class="text-sm font-medium text-gray-700 w-44">Total Keseluruhan</label>
                        <input 
                        type="text" id="grandTotalBudget" name="grandTotalBudget" readonly
                        class="w-56 border border-gray-300 rounded-md p-2 text-right focus:ring-2 focus:ring-[#023936] outline-none"
                        >
                        <input type="hidden" name="grandTotalBudget" id="hiddenGrandBudget">
                    </div>

                    <!-- Pembulatan -->
                    <div class="flex items-center justify-between w-full gap-4">
                        <label for="pembulatanBudget" class="text-sm font-medium text-gray-700 w-44">Pembulatan</label>
                        <input 
                        type="text" id="pembulatanBudget" name="pembulatanBudget"
                        class="w-56 border border-gray-300 rounded-md p-2 text-right focus:ring-2 focus:ring-[#023936] outline-none"
                        required
                        >
                    </div>

                    <!-- Per Meter Persegi -->
                    <div class="flex items-center justify-between w-full gap-4">
                        <label for="permeterBudget" class="text-sm font-medium text-gray-700 w-44">Per Meter Persegi</label>
                        <input 
                        type="text" id="permeterBudget" name="permeterBudget" readonly
                        class="w-56 border border-gray-300 rounded-md p-2 text-right focus:ring-2 focus:ring-[#023936] outline-none"
                        >
                    </div>
                </div>
            </div>
            
            <!-- TAB 3: Additional -->
            <div id="contentAdditional" class="hidden">
              <div id="additional-container" class="w-full"></div>
                <button type="button" id="addCategoryAdditional" class="bg-[#023936] text-white font-medium px-4 py-2 rounded-md hover:bg-[#035949] transition flex">
                <img src="../assets/image/add.svg" alt="add" class="mr-[8px]"> Add Category
                </button>

                <hr class="w-full my-4">
                <div class="flex flex-col gap-3 w-fit">
                    <!-- Total Keseluruhan -->
                    <div class="flex items-center justify-between w-full gap-4">
                        <label for="grandTotalAdditional" class="text-sm font-medium text-gray-700 w-44">Total Keseluruhan</label>
                        <input 
                        type="text" id="grandTotalAdditional" name="grandTotalAdditional" readonly
                        class="w-56 border border-gray-300 rounded-md p-2 text-right focus:ring-2 focus:ring-[#023936] outline-none"
                        >
                        <input type="hidden" name="grandTotalAdditional" id="hiddenGrandAdditional">
                    </div>

                    <!-- Pembulatan -->
                    <div class="flex items-center justify-between w-full gap-4">
                        <label for="pembulatanAdditional" class="text-sm font-medium text-gray-700 w-44">Pembulatan</label>
                        <input 
                        type="text" id="pembulatanAdditional" name="pembulatanAdditional"
                        class="w-56 border border-gray-300 rounded-md p-2 text-right focus:ring-2 focus:ring-[#023936] outline-none"
                        required
                        >
                    </div>

                    <!-- Per Meter Persegi -->
                    <div class="flex items-center justify-between w-full gap-4">
                        <label for="permeterAdditional" class="text-sm font-medium text-gray-700 w-44">Per Meter Persegi</label>
                        <input 
                        type="text" id="permeterAdditional" name="permeterAdditional" readonly
                        class="w-56 border border-gray-300 rounded-md p-2 text-right focus:ring-2 focus:ring-[#023936] outline-none"
                        >
                    </div>
                </div>
            </div>
            
        </div>
        <div class="flex justify-end gap-3 mt-4">
            <a href="rab.php" type="button" id="closeModal"
                    class="bg-gray-200 text-[#023936] font-medium px-4 py-2 rounded-md hover:bg-gray-300 transition">
            Cancel
            </a>
            <button type="submit" name="create"
                    class="bg-[#023936] text-white font-medium px-4 py-2 rounded-md hover:bg-[#035949] transition">
            Create RAB
            </button>
      </div>
    </form>
  </div>
</main>

<!-- Modal Pilih Material -->
<div id="materialModal"
     class="fixed inset-0 flex items-center justify-center bg-black/40 opacity-0 pointer-events-none transition-opacity duration-300 z-[70]">
  <div id="modalBox"
       class="bg-white rounded-xl p-8 w-[85%] h-fit min-h-[60%] max-h-[90%] shadow-lg relative transform scale-95 transition-transform duration-300">
    <h2 class="text-2xl font-semibold text-center text-[#FB8E1B] mb-6">Daftar Material</h2>
    <div class="w-full mx-auto h-auto max-h-[60vh]">

      <!-- Search dan tombol tambah -->
      <div class="flex flex-col-reverse gap-3 lg:flex-row lg:items-center lg:justify-between mb-[24px]">
        <div class="flex items-center">
          <input 
            id="searchMaterial" 
            type="text" 
            autocomplete="off" 
            class="bg-zinc-100 rounded-tl-[10px] rounded-bl-[10px] px-3 py-2 w-60 outline-none focus:zinc-100"
            placeholder="Search by name..."
          >
          <button type="button" class="bg-[#023936] text-white px-4 py-2 rounded-tr-[10px] rounded-br-[10px] inline-flex h-[40px] justify-center items-center">
            <img src="../assets/image/search.svg" alt="">
          </button>
        </div>
      </div>


      <!-- Table Data -->
      <div class="w-full max-h-[53vh] overflow-y-auto overflow-x-auto shadow-md rounded-xl">
        <table class="w-full">
          <thead class="border-[#023936] bg-[#E6EBEB] border-b-2 sticky top-0 z-10">
            <tr>
              <th class="px-3 py-2 text-left">ID</th>
              <th class="px-3 py-2 text-left">Nama</th>
              <th class="px-3 py-2 text-left">Specification</th>
              <th class="px-3 py-2 text-left">Unit</th>
              <th class="px-3 py-2 text-left">Quantity</th>
              <th class="px-3 py-2 text-left">Price</th>
              <?php if ($role === "admin") {
                  echo '<th class="px-3 py-2 text-left">Supplier</th>';
              } ?>
              <th class="px-3 py-2 text-left">Select</th>
            </tr>
          </thead>
          <tbody id="tableBody" class="divide-y divide-gray-300">
            <?php if (empty($data)): ?>
              <tr><td colspan="<?= $role === "admin"
                  ? "8"
                  : "7" ?>" class="py-4 text-center text-gray-500">Data material kosong.</td></tr>
            <?php else: ?>
              <?php foreach ($data as $row): ?>
                <tr class="hover:bg-[#E6EBEB]">
                    <td class="px-3 py-2"><?= htmlspecialchars(
                        $row["id_material"],
                    ) ?></td>
                    <td class="px-3 py-2"><?= htmlspecialchars(
                        $row["name"],
                    ) ?></td>
                    <td class="px-3 py-2"><?= htmlspecialchars(
                        $row["specification"],
                    ) ?></td>
                    <td class="px-3 py-2">
                        <?= htmlspecialchars($row["unit"]) ?>
                    </td>
                    <td class="px-3 py-2"><?= htmlspecialchars(
                        $row["quantity"],
                    ) ?></td>
                    <td class="px-3 py-2">Rp <?= number_format(
                        $row["price"],
                        0,
                        ",",
                        ".",
                    ) ?></td>
                    <?php if ($role === "admin"): ?>
                        <td class="px-3 py-2"><?= htmlspecialchars(
                            $row["supplier_name"] ?? "-",
                        ) ?></td>
                    <?php endif; ?>
                    <td>
                        <button type="button" 
                                class="bg-[#023936] cursor-pointer text-white px-4 py-1 rounded-[10px] inline-flex justify-center items-center selectMaterial"
                                data-id="<?= htmlspecialchars(
                                    $row["id_material"],
                                ) ?>"
                                data-name="<?= htmlspecialchars(
                                    $row["name"],
                                ) ?>"
                                data-unit="<?= htmlspecialchars(
                                    $row["unit"],
                                ) ?>"
                                data-price="<?= htmlspecialchars(
                                    $row["price"],
                                ) ?>"
                                >
                            Pilih
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      
      </div>
    
  </div>
</div>



<!-- Script Tab Switching -->
<script>
  const tabInfo = document.getElementById('tabInfo');
  const tabBudget = document.getElementById('tabBudget');
  const tabAdditional = document.getElementById('tabAdditional');

  const contentInfo = document.getElementById('contentInfo');
  const contentBudget = document.getElementById('contentBudget');
  const contentAdditional = document.getElementById('contentAdditional');

  // Fungsi untuk reset semua tab
  function resetTabs() {
    [tabInfo, tabBudget, tabAdditional].forEach(tab => {
      tab.classList.remove('bg-white', 'shadow-sm', 'text-black');
      tab.classList.add('text-gray-500');
    });

    [contentInfo, contentBudget, contentAdditional].forEach(content => {
      content.classList.add('hidden');
    });
  }

  // Tab Info
  tabInfo.addEventListener('click', () => {
    resetTabs();
    tabInfo.classList.add('bg-white', 'shadow-sm', 'text-black');
    tabInfo.classList.remove('text-gray-500');
    contentInfo.classList.remove('hidden');
  });

  // Tab Budget
  tabBudget.addEventListener('click', () => {
    resetTabs();
    tabBudget.classList.add('bg-white', 'shadow-sm', 'text-black');
    tabBudget.classList.remove('text-gray-500');
    contentBudget.classList.remove('hidden');
  });

  // Tab Additional (baru)
  tabAdditional.addEventListener('click', () => {
    resetTabs();
    tabAdditional.classList.add('bg-white', 'shadow-sm', 'text-black');
    tabAdditional.classList.remove('text-gray-500');
    contentAdditional.classList.remove('hidden');
  });
</script>

<!-- Script Hitung Otomatis -->
<script>
  // ========================
  // 🧮 FORMAT & KONVERSI
  // ========================
  function formatRupiah(value) {
    if (typeof value === 'number') value = value.toString();
    return value
      .replace(/\D/g, '') // hanya angka
      .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // titik setiap 3 digit
  }

  function getNumber(value) {
    return parseInt(value.toString().replace(/\./g, '')) || 0;
  }

  // ========================
  // 💰 HITUNG PER-BARIS
  // ========================
  function updateRowTotal(row) {
    const qtyInput = row.querySelector('[name="quantity[]"]');
    const priceInput = row.querySelector('[name="unit_price[]"]');
    const totalInput = row.querySelector('[name="total_cost[]"]');

    if (!qtyInput || !priceInput || !totalInput) return;

    const qty = parseFloat(qtyInput.value) || 0;
    const price = getNumber(priceInput.value);
    const typeVal = parseFloat(document.getElementById('unit')?.value) || 1;

    // ✅ Tentukan konteks tab
    const isAdditional = !!row.closest('#additional-container');
    const total = isAdditional ? (qty * price) / typeVal : (qty * price);

    totalInput.value = total > 0 ? formatRupiah(String(Math.floor(total))) : '0';

    updateCategorySubtotal(row.closest('.category-block'));
  }



  // ========================
  // 💰 UTIL: hitung total per kontainer
  // ========================
  function sumCatTotalsIn(containerSelector) {
    let sum = 0;
    document.querySelectorAll(`${containerSelector} .catTotal`).forEach(el => {
      sum += getNumber(el.textContent);
    });
    return sum;
  }

  // ========================
  // 💸 HITUNG ULANG TOTAL (idempotent)
  // ========================

  // Hitung & render grand total TAB Budget saja
  function calcBudgetTotal() {
    const totalBudget = sumCatTotalsIn('#budget-container');
    const budgetInput = document.getElementById('grandTotalBudget');
    if (budgetInput) budgetInput.value = formatRupiah(String(totalBudget));
    renderGrandTotals(); // render gabungan
  }

  // Hitung subtotal seluruh kategori di TAB Additional (tanpa budget)
  function calcAdditionalSubtotal() {
    return sumCatTotalsIn('#additional-container');
  }

  // Render hasil gabungan: Budget + AdditionalSubtotal
  function renderGrandTotals() {
    const budget = getNumber(document.getElementById('grandTotalBudget')?.value || '0');
    const additionalSubtotal = calcAdditionalSubtotal(); // selalu hitung ulang dari nol
    const totalGabungan = budget + additionalSubtotal;

    const addInput = document.getElementById('grandTotalAdditional');
    if (addInput) addInput.value = formatRupiah(String(totalGabungan));
  }

  // ========================
  // 🧾 SUBTOTAL PER KATEGORI
  // ========================
  function updateCategorySubtotal(categoryBlock) {
    if (!categoryBlock) return;

    let subtotal = 0;
    categoryBlock.querySelectorAll('[name="total_cost[]"]').forEach(input => {
      subtotal += getNumber(input.value);
    });

    const catTotal = categoryBlock.querySelector('.catTotal');
    if (catTotal) catTotal.textContent = formatRupiah(String(subtotal));

    // 🔄 cek tab mana kategori ini berada
    if (categoryBlock.closest('#budget-container')) {
      calcBudgetTotal(); // update total budget + gabungan
    } else if (categoryBlock.closest('#additional-container')) {
      renderGrandTotals(); // update gabungan saja
    }
  }




  // ========================
  // ⌨️ EVENT HANDLER (Qty & Price)
  // ========================
  document.addEventListener('input', e => {
    if (e.target.matches('[name="quantity[]"], [name="unit_price[]"]')) {
      const row = e.target.closest('.row');
      updateRowTotal(row);
    }

    // Format harga saat diketik
    if (e.target.matches('[name="unit_price[]"]')) {
      const el = e.target;
      const start = el.selectionStart;
      const beforeLength = el.value.length;

      el.value = formatRupiah(el.value);

      const diff = el.value.length - beforeLength;
      el.setSelectionRange(start + diff, start + diff);
    }
  });

  // ========================
  // 🔁 INTEGRASI DENGAN PEMBULATAN & PER METER
  // ========================
  const pembulatan = document.getElementById('pembulatanBudget');
  const permeter = document.getElementById('permeterBudget');
  const type = document.getElementById('type');

  function hitungPerMeter() {
    const pemb = getNumber(pembulatan.value);
    const u = parseFloat(type.value) || 0;
    const hasil = u > 0 ? Math.floor(pemb / u) : 0;
    permeter.value = hasil > 0 ? formatRupiah(String(hasil)) : '0';
  }

  let timer;
  pembulatan.addEventListener('input', (e) => {
    const el = e.target;
    const start = el.selectionStart;
    const beforeLength = el.value.length;
    el.value = formatRupiah(el.value);
    const diff = el.value.length - beforeLength;
    el.setSelectionRange(start + diff, start + diff);
    clearTimeout(timer);
    timer = setTimeout(hitungPerMeter, 400);
  });
  type.addEventListener('input', hitungPerMeter);

  // ========================
  // 🔁 Pembulatan & Per Meter TAB ADDITIONAL
  // ========================
  const pembulatanAdd = document.getElementById('pembulatanAdditional');
  const permeterAdd = document.getElementById('permeterAdditional');

  function hitungPerMeterAdditional() {
    const pemb = getNumber(pembulatanAdd.value);
    const u = parseFloat(type.value) || 0;
    const hasil = u > 0 ? Math.floor(pemb / u) : 0;
    permeterAdd.value = hasil > 0 ? formatRupiah(String(hasil)) : '0';
  }

  let timerAdd;
  pembulatanAdd.addEventListener('input', (e) => {
    const el = e.target;
    const start = el.selectionStart;
    const beforeLength = el.value.length;
    el.value = formatRupiah(el.value);
    const diff = el.value.length - beforeLength;
    el.setSelectionRange(start + diff, start + diff);
    clearTimeout(timerAdd);
    timerAdd = setTimeout(hitungPerMeterAdditional, 400);
  });
  type.addEventListener('input', hitungPerMeterAdditional);


  // sinkronisasi input hidden
  function syncHiddenTotals() {
    document.getElementById('hiddenGrandBudget').value = getNumber(document.getElementById('grandTotalBudget').value);
    document.getElementById('hiddenGrandAdditional').value = getNumber(document.getElementById('grandTotalAdditional').value);
  }

  setInterval(syncHiddenTotals, 1000);
</script>


  <!-- Script Tambah Kategori & Material -->
<script>
    // ========================
    // 🔧 Tambah Kategori (Reusable) - FIXED VERSION
    // ========================
    function handleAddCategory(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const categoryId = 'cat_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        
        const block = document.createElement('div');
        block.className = 'category-block flex flex-col mb-4 bg-[#e7e7e7] p-[24px] shadow-inner rounded-[10px] w-full';
        block.dataset.categoryId = categoryId;
        block.innerHTML = `
        <div class="flex items-center justify-between w-full gap-4 mb-2">
            <input type="text" name="category[]" 
            class="flex-1 bg-white border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none category-name" 
            placeholder="Nama Kategori"
            required
            >
            <button type="button" 
            class="flex bg-[#B3261E] text-white font-medium px-4 py-2 rounded-md hover:bg-[#C65C56] transition removeCategory">
            <img src="../assets/image/delete_white.svg" alt="delete" class="mr-[8px]"> Delete Kategori
            </button>
        </div>

        <div class="category-items" data-category-id="${categoryId}"></div>

        <div class="mt-2">
            <button type="button" 
            class="bg-[#023936] text-white font-medium px-4 py-2 rounded-md hover:bg-[#035949] transition addItem">
            Add Material
            </button>
        </div>

        <div class="mt-3 text-end"><b>Subtotal:</b> <span class="catTotal">0</span></div>

        <input type="hidden" name="bagian[]" value="${containerId === 'budget-container' ? 'Budget' : 'Additional'}">
        <input type="hidden" name="category_id[]" value="${categoryId}">
        `;

        container.appendChild(block);
    }

  // ========================
  // 🎯 Event Listener untuk tiap tab
  // ========================
  document.getElementById('addCategoryBudget')
    .addEventListener('click', () => handleAddCategory('budget-container'));

  document.getElementById('addCategoryAdditional')
    .addEventListener('click', () => handleAddCategory('additional-container'));


    // ========================
    // ➕ Tambah Material - FIXED BAGIAN VERSION
    // ========================
    document.addEventListener('click', e => {
        if (e.target.classList.contains('addItem')) {
            const categoryBlock = e.target.closest('.category-block');
            const categoryId = categoryBlock.dataset.categoryId;
            const categoryItems = categoryBlock.querySelector('.category-items');
            
            // DETEKSI BAGIAN BERDASARKAN CONTAINER
            const container = categoryBlock.closest('#budget-container, #additional-container');
            const bagian = container.id === 'budget-container' ? 'Budget' : 'Additional';
            
            const row = document.createElement('div');
            row.className = 'row grid grid-cols-12 gap-2 items-center w-full mt-2';
            row.dataset.categoryId = categoryId;
            row.innerHTML = `
                <!-- Material -->
                <div class="flex items-center col-span-5 gap-2">
                    <input name="material_name[]" 
                        class="w-full bg-white border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none material-input" 
                        placeholder="Material"
                        required
                        >
                    <button type="button" 
                            class="bg-[#023936] text-white flex items-center justify-center p-2 rounded-md hover:bg-[#035949] transition openMaterialModal"
                            data-bs-toggle="modal" data-bs-target="#materialModal">
                        <img src="../assets/image/search.svg" alt="search" class="w-4 h-4">
                    </button>
                </div>

                <!-- Unit - PASTIKAN NAME ATTRIBUTE BENAR -->
                <div class="col-span-1">
                    <input name="unit_material[]" 
                        class="w-full bg-white border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none unit-material" 
                        placeholder="Unit"
                        required
                        >
                </div>

                <!-- Qty -->
                <div class="col-span-1">
                    <input name="quantity[]" 
                        type="number" 
                        class="w-full bg-white border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none qty" 
                        placeholder="Qty" min="1"
                        required
                        >
                </div>

                <!-- Harga -->
                <div class="col-span-2">
                    <input name="unit_price[]" 
                        type="text" 
                        class="w-full bg-white border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none unitprice" 
                        placeholder="Harga"
                        required
                        >
                </div>

                <!-- Total -->
                <div class="flex items-center col-span-3 gap-2">
                    <input name="total_cost[]" 
                        type="text" 
                        class="w-full bg-white border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-[#023936] outline-none total text-end" 
                        placeholder="Total" readonly>
                    <button type="button" 
                            class="bg-[#B3261E] text-white font-medium px-4 py-2 rounded-md hover:bg-[#C65C56] transition removeItem">
                        <img src="../assets/image/delete_white.svg" alt="delete">
                    </button>
                </div>

                <input type="hidden" name="id_material[]" value="">
                <input type="hidden" name="material_category_id[]" value="${categoryId}">
                <!-- NEW: Hidden input untuk bagian per material -->
                <input type="hidden" name="material_bagian[]" value="${bagian}">
            `;
            
            categoryItems.appendChild(row);
        }
    });


  // ========================
  // 🗑️ Hapus Kategori / Material
  // ========================
  document.addEventListener('click', e => {
    // 🗑️ Hapus kategori
    if (e.target.classList.contains('removeCategory') || e.target.closest('.removeCategory')) {
      const btn = e.target.closest('.removeCategory');
      const block = btn.closest('.category-block');
      if (confirm("Apakah Anda yakin ingin menghapus kategori ini beserta seluruh material di dalamnya?")) {
        const isBudget = !!block.closest('#budget-container');
        const isAdditional = !!block.closest('#additional-container');

        block.remove(); // hapus dulu

        // 🔄 Hitung ulang sesuai tab
        if (isBudget) {
          calcBudgetTotal();          // update total budget + global
        } else if (isAdditional) {
          renderGrandTotals();        // update global saja (budget + additional)
        }
      }
    }

    // 🗑️ Hapus item/material
    if (e.target.classList.contains('removeItem') || e.target.closest('.removeItem')) {
      const row = e.target.closest('.row');
      if (!row) return;
      const categoryBlock = row.closest('.category-block');
      row.remove();
      updateCategorySubtotal(categoryBlock); // ini otomatis update total sesuai tab
    }
  });
</script>


<!-- Script Modal -->
<!-- Script Modal - FIXED VERSION -->
<script>
  const modal = document.getElementById('materialModal');
  const modalBox = document.getElementById('modalBox');
  let currentRow = null;

  // Fungsi buka modal
  function openModal() {
    modal.classList.remove('pointer-events-none', 'opacity-0');
    modal.classList.add('opacity-100');
    modalBox.classList.remove('scale-95');
    modalBox.classList.add('scale-100');
  }

  // Fungsi tutup modal
  function closeModal() {
    modal.classList.add('opacity-0');
    modal.classList.remove('opacity-100');
    modalBox.classList.add('scale-95');
    modalBox.classList.remove('scale-100');
    setTimeout(() => {
      modal.classList.add('pointer-events-none');
    }, 300);
    currentRow = null;
  }

  // Event delegation untuk buka modal
  document.addEventListener('click', (e) => {
    if (e.target.closest('.openMaterialModal')) {
      currentRow = e.target.closest('.row');
      console.log('🔍 OPEN MODAL - Current row:', currentRow);
      openModal();
    }
    
    if (e.target.closest('#closeModal')) {
      closeModal();
    }
  });

  // 🎯 FIXED: Event delegation untuk pilih material dari modal
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.selectMaterial');
    if (!btn) return;

    if (!currentRow) {
      console.error('❌ No current row selected');
      closeModal();
      return;
    }

    // Ambil data dari tombol
    const id_material = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    const unit = btn.getAttribute('data-unit');
    const price = btn.getAttribute('data-price');

    // Temukan input fields di current row
    const nameInput = currentRow.querySelector('[name="material_name[]"]');
    const unitInput = currentRow.querySelector('[name="unit_material[]"]');
    const priceInput = currentRow.querySelector('[name="unit_price[]"]');
    const qtyInput = currentRow.querySelector('[name="quantity[]"]');
    const totalInput = currentRow.querySelector('[name="total_cost[]"]');


    // Set hidden input untuk id_material
    let idInput = currentRow.querySelector('[name="id_material[]"]');
    if (!idInput) {
      idInput = document.createElement('input');
      idInput.type = 'hidden';
      idInput.name = 'id_material[]';
      currentRow.appendChild(idInput);
    }
    idInput.value = id_material;

    // 🎯 SET VALUE KE INPUT - FOCUS ON UNIT
    if (nameInput) {
      nameInput.value = name || '';
      console.log('✅ Name set to:', nameInput.value);
    }
    
    if (unitInput) {
      unitInput.value = unit || ''; // INI YANG PENTING!
      console.log('✅ Unit set to:', unitInput.value, '(raw:', unit, ')');
    }
    
    if (priceInput) {
      priceInput.value = formatRupiah(price || '0');
      console.log('✅ Price set to:', priceInput.value);
    }
    
    // Set quantity default
    if (qtyInput && (!qtyInput.value || qtyInput.value === '0')) {
      qtyInput.value = '1';
      console.log('✅ Qty set to 1');
    }

    // Hitung total otomatis
    updateRowTotal(currentRow);


    closeModal();
  });

  // Tutup jika klik area gelap
  modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
  });
</script>

<!-- Script Search Material dengan Fetch API -->
<script>
  const searchInput = document.getElementById('searchMaterial');
  const tableBody = document.getElementById('tableBody');
  let debounceTimer;

  searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      const query = searchInput.value.trim();

      fetch(`get_materials.php?search=${encodeURIComponent(query)}`)
        .then(res => res.text())
        .then(html => {
          tableBody.innerHTML = html;
        })
        .catch(err => {
          console.error("Gagal mengambil data:", err);
        });
    }, 400); // debounce 400ms
  });
</script>




</body>
</html>