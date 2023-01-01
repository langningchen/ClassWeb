<?php
require_once "NotLogin.php";
require_once "Function.php";
if (!isset($_GET["URI"])) {
    CreateText("非法调用");
    require_once("Footer.php");
    die();
}
$DatabaseQuery = $Database->prepare("INSERT INTO PageCount(URI, UID, Time) VALUES (?, ?, current_timestamp())");
$DatabaseQuery->bind_param("si", $_GET["URI"], $_SESSION["UID"]);
$DatabaseQuery->execute();
echo "OK";
