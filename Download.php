<?php
require_once "NotLogin.php";
require_once "Function.php";
if (!isset($_GET["File"])) {
    CreateText("非法调用");
    die();
}
if (!isset($_GET["FileName"])) {
    CreateText("非法调用");
    die();
}
if (!isset($_GET["Time"])) {
    CreateText("非法调用");
    die();
}
if (!isset($_GET["Sign"])) {
    CreateText("非法调用");
    die();
}
if (strlen($_GET["File"]) % 2 != 0) {
    CreateText("非法调用");
    die();
}
if (strlen($_GET["FileName"]) % 2 != 0) {
    CreateText("非法调用");
    die();
}
$File = $_GET["File"];
$Time = $_GET["Time"];
$FileName = $_GET["FileName"];
if (abs(date("U") - $Time) > 5 * 60) {
    CreateText("非法调用");
    die();
}
$Sign = $_GET["Sign"];
if ($Sign != md5($File . $Time . $FileName . $_SESSION["UID"] . $_SESSION["UserName"])) {
    CreateText("非法调用");
    die();
}
$File = hex2bin($File);
$FileName = hex2bin($FileName);
if (!file_exists($File)) {
    CreateText("非法调用" . $File);
    die();
}
header("Accept-Length: " . filesize($File));
header("Content-Transfer-Encoding: binary");
header("Content-Disposition: attachment; filename=" . $FileName);
header("Content-Type: application/octet-stream; name=" . $FileName);
echo file_get_contents($File);
