<?php
require_once "NotLogin.php";
if ($_SESSION["UserType"] != 2) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
phpinfo();
