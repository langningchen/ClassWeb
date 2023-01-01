<?php
require_once "NotLogin.php";
require_once "Header.php";
if (!isset($_GET["ClassID"])) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
$DatabaseQuery = $Database->prepare("SELECT * FROM ClassList WHERE ClassID=?");
$DatabaseQuery->bind_param("i", $_GET["ClassID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
if ($Result->num_rows == 0) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
$Result->data_seek(0);
$RowData = $Result->fetch_array(MYSQLI_NUM);
$Member = GetClassType($_GET["ClassID"]);
if ($Member == "") {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
echo "<h4>" . $RowData[1] . "</h4>";
echo "<h4>班级管理员</h4>";
echo GetUserName($RowData[2]);
echo "<h4>班级教师</h4>";
$TempArray = mb_split(",", $RowData[3]);
for ($i = 0; $i < count($TempArray); $i++) {
    if ($TempArray[$i] == "") continue;
    echo GetUserName($TempArray[$i]) . " ";
}
echo "<h4>班级学生</h4>";
$TempArray = mb_split(",", $RowData[4]);
for ($i = 0; $i < count($TempArray); $i++) {
    if ($TempArray[$i] == "") continue;
    echo GetUserName($TempArray[$i]) . " ";
}
echo "<h4>班级功能</h4>";
CreateLink("ClassFile.php?ClassID=" . $_GET["ClassID"], "班级文件");
echo "<br />";
CreateLink("Homeworks.php?ClassID=" . $_GET["ClassID"], "班级作业");
echo "<br />";
CreateLink("ClockIns.php?ClassID=" . $_GET["ClassID"], "班级打卡");
require_once "Footer.php";
