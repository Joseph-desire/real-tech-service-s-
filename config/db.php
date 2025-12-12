<?php
session_start();

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "realtech";

$cn = @mysql_connect($DB_HOST, $DB_USER, $DB_PASS) or die("DB connect failed: " . mysql_error());
@mysql_select_db($DB_NAME, $cn) or die("DB selection failed: " . mysql_error());

function db_query($sql) {
  $res = mysql_query($sql);
  if (!$res) die("SQL Error: " . mysql_error());
  return $res;
}

function db_escape($str) {
  return mysql_real_escape_string($str);
}
?>
