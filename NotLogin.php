<?php
require_once "Function.php";
if (!isset($_SESSION["UID"]) || !isset($_SESSION["UserName"]) || !isset($_SESSION["Password"]) || !isset($_SESSION["UserType"]) || !isset($_SESSION["UserEmail"])) {
    echo "<script>";
    echo "window.location = \"Login.php?RedirectURI=\" + String(new window.URL(window.location).pathname) + String(new window.URL(window.location).search)";
    echo "</script>";
    require_once("Footer.php");
    die();
}
$DatabaseQuery = $Database->prepare("SELECT UID FROM UserList WHERE UID=? AND UserName=? AND Password=? AND UserType=? AND UserEmail=?");
$DatabaseQuery->bind_param("issis", $_SESSION["UID"], $_SESSION["UserName"], $_SESSION["Password"], $_SESSION["UserType"], $_SESSION["UserEmail"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
if ($Result->num_rows == 0) {
    require_once("Header.php");
    CreateText("您或管理员修改了您的个人信息或权限，请重新登录");
    require_once("Footer.php");
    die();
}
if ($_SESSION["UserType"] == 3) {
    require_once("Header.php");
    CreateText("您已被封号，如需有疑问或申请解封，请");
    CreateLink("mailto:langningc2009.ml@outlook.com", "点击此处");
    CreateText("发送邮件至管理员，谢谢");
    require_once("Footer.php");
    die();
}
