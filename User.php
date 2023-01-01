<?php
require_once "NotLogin.php";
require_once "Header.php";
if (!isset($_GET["UID"])) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
$DatabaseQuery = $Database->prepare("SELECT UserName,UserType,UserEmail FROM UserList WHERE UID=?");
$DatabaseQuery->bind_param("i", $_GET["UID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
if ($Result->num_rows == 0) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
} else if ($Result->num_rows == 1) {
    $Result->data_seek(0);
    $RowData = $Result->fetch_array(MYSQLI_NUM);
    CreateText("用户编号：");
    CreateText($_GET["UID"]);
    echo "<br />";
    CreateText("用户名：");
    CreateText($RowData[0]);
    echo "<br />";
    CreateText("用户类型：");
    switch ($RowData[1]) {
        case "0":
            CreateText("普通用户");
            break;
        case "1":
            CreateText("教师");
            break;
        case "2":
            CreateText("管理员");
            break;
        case "3":
            CreateText("封禁用户");
            break;
    }
    echo "<br />";
    CreateText("邮箱：");
    CreateText($RowData[2] == "" ? "未绑定" : $RowData[2]);
}
require_once "Footer.php";
