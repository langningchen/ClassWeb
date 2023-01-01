<?php
require_once "Function.php";
if (!isset($_GET["URI"])) {
    CreateText("非法调用");
    die();
}
$DatabaseQuery = $Database->prepare("INSERT INTO PageCount(URI, UID, IP, Time) VALUES (?, ?, ?, current_timestamp())");
$DatabaseQuery->bind_param("sis", $_GET["URI"], $_SERVER["REMOTE_ADDR"], $_SESSION["UID"]);
$DatabaseQuery->execute();
echo "OK";
