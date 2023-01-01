<?php
require_once "NotLogin.php";
require_once "Header.php";
if (!isset($_GET["Email"]) || !isset($_GET["Time"]) || !isset($_GET["Sign"]) || !isset($_GET["RandomToken"])) {
    CreateErrorText("非法调用");
    require_once("Footer.php");
    die();
}
if (abs(time() - $_GET["Time"]) > 5 * 60) {
    CreateErrorText("邮件验证已超时，请重新发送邮件重试");
    require_once("Footer.php");
    die();
}
if ($_GET["Sign"] != md5(hex2bin($_GET["Email"]) . $_GET["Time"] . hex2bin($_GET["RandomToken"]))) {
    CreateErrorText("非法调用");
    require_once("Footer.php");
    die();
}
if (hex2bin($_GET["Email"]) != $_SESSION["Email"]) {
    CreateErrorText("非法调用");
    require_once("Footer.php");
    die();
}
if (hex2bin($_GET["RandomToken"]) != $_SESSION["RandomToken"]) {
    CreateErrorText("非法调用");
    require_once("Footer.php");
    die();
}
$DatabaseQuery = $Database->prepare("UPDATE UserList SET UserEmail=? WHERE UID=?");
$DatabaseQuery->bind_param("si", $_SESSION["Email"], $_SESSION["UID"]);
$DatabaseQuery->execute();
CreateSuccessText("绑定成功");
