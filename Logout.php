<?php
session_start();
session_destroy();
setcookie("UserName", "", time() - 1);
setcookie("Password", "", time() - 1);
setcookie("UID", "", time() - 1);
setcookie("UserType", "", time() - 1);
require_once "Header.php";
echo "<script>window.location=\"Login.php\"</script>";
require_once "Footer.php";
