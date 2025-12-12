<?php
require_once __DIR__ . "/../config/db.php";

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = db_escape($_POST['username']);
  $p = md5($_POST['password']); // matches the INSERT we created

  $row = mysql_fetch_assoc(db_query("SELECT * FROM admins WHERE username='$u' AND password_hash='$p'"));
  if ($row) {
    $_SESSION['admin_id'] = $row['id'];
    $_SESSION['admin_user'] = $row['username'];
    header("Location: /realtech/admin/dashboard.php");
    exit();
  } else {
    $error = "Invalid username or password.";
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin Login - Real-Tech</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">
  <div class="max-w-sm mx-auto px-4 py-16">
    <div class="bg-white border rounded-2xl p-6 shadow-sm">
      <h1 class="text-2xl font-extrabold">Admin Login</h1>
      <?php if($error): ?>
        <div class="mt-3 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg"><?php echo $error; ?></div>
      <?php endif; ?>

      <form method="POST" class="mt-6 space-y-4">
        <div>
          <label class="block text-sm font-medium">Username</label>
          <input class="w-full border rounded-lg px-3 py-2" name="username" required />
        </div>
        <div>
          <label class="block text-sm font-medium">Password</label>
          <input class="w-full border rounded-lg px-3 py-2" type="password" name="password" required />
        </div>
        <button class="w-full rounded-lg bg-indigo-600 text-white py-2 font-semibold" type="submit">Login</button>
      </form>
    </div>
  </div>
</body>
</html>
