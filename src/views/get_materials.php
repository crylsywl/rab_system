<?php
include "../config/database.php";
session_start();

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$role = $_SESSION['role'] ?? 'user';

$whereClause = "WHERE 1";
if ($search !== "") {
    $safeSearch = mysqli_real_escape_string($conn, $search);
    $whereClause .= " AND m.name LIKE '%$safeSearch%'";
}

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

if (mysqli_num_rows($result) === 0) {
    echo '<tr><td colspan="' . ($role === "admin" ? "8" : "7") . '" class="text-center py-4 text-gray-500">Data material kosong.</td></tr>';
} else {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr class="hover:bg-[#E6EBEB]">
                <td class="px-3 py-2">' . htmlspecialchars($row["id_material"]) . '</td>
                <td class="px-3 py-2">' . htmlspecialchars($row["name"]) . '</td>
                <td class="px-3 py-2">' . htmlspecialchars($row["specification"]) . '</td>
                <td class="px-3 py-2">' . htmlspecialchars($row["unit"]) . '</td>
                <td class="px-3 py-2">' . htmlspecialchars($row["quantity"]) . '</td>
                <td class="px-3 py-2">Rp ' . number_format($row["price"], 0, ",", ".") . '</td>';
        if ($role === "admin") {
            echo '<td class="px-3 py-2">' . htmlspecialchars($row["supplier_name"] ?? "-") . '</td>';
        }
        echo '<td class="px-3 py-2">
                <div class="flex items-center gap-3">
                    <a href="detailmaterial.php?id=' . $row["id_material"] . '" class="text-[#FB8E1B] hover:text-amber-600" title="Edit Material">
                        <img src="../assets/image/edit.svg" alt="Edit" class="w-5 h-5">
                    </a>
                    <a href="?delete=' . $row["id_material"] . '" onclick="return confirm(\'Apakah Anda yakin ingin menghapus material ini?\')" class="text-red-600 hover:text-red-800" title="Hapus Material">
                        <img src="../assets/image/delete.svg" alt="Delete" class="w-5 h-5">
                    </a>
                </div>
              </td>
            </tr>';
    }
}
?>
