<?php
require_once "NotLogin.php";
require_once "Header.php";
if (!isset($_GET["NoticeID"])) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
$DatabaseQuery = $Database->prepare("SELECT * FROM Notice WHERE NoticeID=?");
$DatabaseQuery->bind_param("i", $_GET["NoticeID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
if ($Result->num_rows == 0) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
} else if ($Result->num_rows != 1) {
    CreateText("系统错误：有重复公告");
    require_once "Footer.php";
    die();
}
$Result->data_seek(0);
$RowData = $Result->fetch_array(MYSQLI_NUM);
echo "<h3>" . $RowData[3] . "</h3>";
echo "<h4>发布者</h4>";
echo GetUserName($RowData[1]);
echo "<h4>发布时间</h4>";
CreateText($RowData[2]);
echo "<h4>内容</h4>";
CreateText($RowData[4]);
require_once "Footer.php";
