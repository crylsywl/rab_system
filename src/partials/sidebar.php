<?php
$role = $_SESSION['role'] ?? 'admin';

const SIDEBAR_ADMIN = [
  ["key" => "user", "label" => "User", "href" => "/rab-system/src/views/user.php", "icon_green" => "../assets/image/user_green.svg", "icon_white" => "../assets/image/user_white.svg"],
  ["key" => "supplier", "label" => "Supplier", "href" => "/rab-system/src/views/supplier.php", "icon_green" => "../assets/image/supplier_green.svg", "icon_white" => "../assets/image/supplier_white.svg"],
  ["key" => "rab", "label" => "RAB", "href" => "/rab-system/src/views/rab.php", "icon_green" => "../assets/image/rab_green.svg", "icon_white" => "../assets/image/rab_white.svg"],
  ["key" => "material", "label" => "Material", "href" => "/rab-system/src/views/material.php", "icon_green" => "../assets/image/material_green.svg", "icon_white" => "../assets/image/material_white.svg"],
];

const SIDEBAR_USER = [
  ["key" => "rab", "label" => "RAB", "href" => "/rab-system/src/views/rab.php", "icon_green" => "../assets/image/rab_green.svg", "icon_white" => "../assets/image/rab_white.svg"],
  ["key" => "material", "label" => "Material", "href" => "/rab-system/src/views/material.php", "icon_green" => "../assets/image/material_green.svg", "icon_white" => "../assets/image/material_white.svg"],
];

const SIDEBAR_SUPPLIER = [
  ["key" => "profile", "label" => "Profile", "href" => "/rab-system/src/views/detailsupplier.php", "icon_green" => "../assets/image/material_green.svg", "icon_white" => "../assets/image/material_white.svg"],
  ["key" => "material", "label" => "Material", "href" => "/rab-system/src/views/material.php", "icon_green" => "../assets/image/material_green.svg", "icon_white" => "../assets/image/material_white.svg"],
];

$sidebar_items = match($role) {
  'user_rab' => SIDEBAR_USER,
  'supplier' => SIDEBAR_SUPPLIER,
  default => SIDEBAR_ADMIN
};

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<aside id="sidebar"
       class="fixed top-0 left-0 z-50 flex flex-col justify-between w-56 h-screen transition-all duration-300 ease-in-out bg-white">
  
  <div>
    <!-- Logo + Toggle -->
    <div class="flex items-center justify-center px-4 mt-4 mb-6">
      <img src="../assets/image/logo.png" alt="Logo" class="items-center justify-center w-20 transition-all duration-300 ease-in-out">
      <button id="toggleSidebar" class="absolute -right-3 top-1/10 -translate-y-1/2 bg-white border border-gray-200 shadow-sm p-1 rounded-full hover:bg-[#BFCDCC] transition z-[60]">
        
      <img src="../assets/image/close_sidebar.svg" alt="toggle" class="w-5 h-5">
      </button>
    </div>

    <!-- Menu -->
    <nav class="flex flex-col px-4 space-y-2">
      <?php foreach ($sidebar_items as $item):
        $isActive = str_contains($item["href"], $current_page);
        $bgClass = $isActive ? "bg-[#023936] text-white" : "text-gray-700 hover:bg-[#E6EBEB] hover:text-[#023936]";
        $icon = $isActive ? $item["icon_white"] : $item["icon_green"];
      ?>
        <a href="<?= $item['href'] ?>"
           class="flex items-center gap-3 py-2 px-3 rounded-md transition <?= $bgClass ?>">
          <img src="<?= $icon ?>" alt="<?= $item['label'] ?>" class="w-5 h-5">
          <span class="sidebar-label whitespace-nowrap"><?= $item['label'] ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
  </div>

  <!-- Logout -->
  <div class="px-4 mb-6">
    <a href="/rab-system/src/logout.php"
       class="flex items-center gap-3 px-3 py-2 text-red-600 transition rounded-md hover:text-red-700 hover:bg-red-50">
      <img src="../assets/image/logout.svg" alt="" class="w-5 h-5">
      <span class="sidebar-label">Logout</span>
    </a>
  </div>
</aside>

<script>
  const sidebar = document.getElementById('sidebar');
  const toggleBtn = document.getElementById('toggleSidebar');
  const labels = document.querySelectorAll('.sidebar-label');
  const logo = sidebar.querySelector('img[alt="Logo"]');
  const toggleIcon = toggleBtn.querySelector('img');

  let isCollapsed = false;

  toggleBtn.addEventListener('click', () => {
    isCollapsed = !isCollapsed;
    sidebar.classList.toggle('w-56', !isCollapsed);
    sidebar.classList.toggle('w-20', isCollapsed);

    labels.forEach(label => label.classList.toggle('hidden', isCollapsed));
    logo.classList.toggle('mx-auto', isCollapsed);

    // Ubah margin konten utama
    const content = document.getElementById('main-content');
    if (content) {
      content.classList.toggle('ml-56', !isCollapsed);
      content.classList.toggle('ml-20', isCollapsed);
    }

    if (isCollapsed) {
      toggleIcon.src = "../assets/image/open_sidebar.svg";   // ikon ketika tertutup
    } else {
      toggleIcon.src = "../assets/image/close_sidebar.svg";  // ikon ketika terbuka
    }
  });
</script>



