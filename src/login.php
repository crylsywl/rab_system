<?php
session_start();
include "./config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = $_POST["password"];

    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["id_user"] = $user["id_user"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["nama"] = $user["nama"];

        if ($user["role"] === "admin" || $user["role"] === "user_rab") {
            header("Location: ./views/rab.php");
        } else {
            header("Location: ./views/material.php");
        }
        exit();
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Sistem RAB</title>
  <link rel="stylesheet" href="./assets/css/output.css">
</head>

<body class="bg-gray-200 w-screen h-screen overflow-hidden">

  <div class="flex flex-col md:flex-row w-screen h-screen bg-white">

    <!-- Bagian Kiri: Form -->
    <div class="flex justify-center items-center min-h-screen w-full md:w-1/2 px-8 py-12 bg-white">
      <div class="w-full max-w-md">
        <h1 class="text-3xl font-bold text-[#FB8E1B] mb-1 text-center">Login</h1>
        <p class="text-center text-gray-600 mb-8 text-sm">Enter your account details to continue!</p>

        <?php if (isset($error)): ?>
          <div class="bg-red-100 text-red-600 text-sm p-2 mb-3 rounded"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="flex flex-col gap-4">
          <div>
            <label class="block text-sm font-semibold mb-1">Username</label>
            <input type="text" name="username" required
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-[#023936] outline-none">
          </div>

          <div>
            <label class="block text-sm font-semibold mb-1">Password</label>
            <div class="relative">
              <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-[#023936] outline-none pr-10"
                    id="passwordInput">
              <button type="button" 
                      class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600 hover:text-gray-800 transition-colors"
                      onclick="togglePasswordVisibility()">
                <img src="./assets/image/notvisible.svg" alt="Show Password" class="w-5 h-5" id="passwordIcon">
              </button>
            </div>
          </div>

          <button type="submit"
                  class="bg-[#023936] text-white font-medium px-4 py-2 rounded-md hover:bg-[#035949] transition">
            Login Now
          </button>
        </form>
      </div>
    </div>

    <!-- Bagian Kanan: Gambar + Logo -->
    <div class="relative hidden md:flex justify-center items-center w-1/2 h-screen">
      <img src="./assets/image/login.jpg" alt="Background" class="object-cover w-full h-full">
      <div class="absolute inset-0 flex justify-center items-center bg-black/10">
        <img src="./assets/image/logo.svg" alt="Logo" class="w-56 h-auto drop-shadow-lg">
      </div>
    </div>

  </div>

  <script>
    function togglePasswordVisibility() {
      const passwordInput = document.getElementById('passwordInput');
      const passwordIcon = document.getElementById('passwordIcon');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.src = './assets/image/visible.svg';
        passwordIcon.alt = 'Hide Password';
      } else {
        passwordInput.type = 'password';
        passwordIcon.src = './assets/image/notvisible.svg';
        passwordIcon.alt = 'Show Password';
      }
    }
  </script>
</body>
</html>
