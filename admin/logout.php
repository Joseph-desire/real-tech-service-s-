<?php
session_start();
session_unset();
session_destroy();
header("Location: /realtech/admin/login.php");
exit();
