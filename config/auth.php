<?php
require_once __DIR__ . "/db.php";

function require_admin() {
  if (!isset($_SESSION['admin_id'])) {
    header("Location: /realtech/admin/login.php");
    exit();
  }
}
?>
