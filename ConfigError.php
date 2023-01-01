<?php
require_once "NotLogin.php";
require_once "Header.php";
if ($_SESSION["UserType"] != 2) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
if (!isset($_GET["ErrorLogID"])) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
$DatabaseQuery = $Database->prepare("SELECT * FROM ErrorLog WHERE ErrorLogID=?");
$DatabaseQuery->bind_param("i", $_GET["ErrorLogID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
if ($Result->num_rows == 0) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
$Result->data_seek(0);
$RowData = $Result->fetch_array(MYSQLI_NUM);
echo "<h4>错误类型</h4>";
CreateText($RowData[1]);
echo "<h4>错误报告</h4>";
CreateText("<pre>" . date("Y年d月m日H时i分s秒", strtotime($RowData[6])) . "</pre>" .
    "，IP为<pre>" . $RowData[8] . "</pre>" . ($RowData[7] == 0 ? "" : "、用户ID为<pre>" . $RowData[7] . "</pre>") .
    "的用户在访问<pre>" . $RowData[9] . "</pre>" .
    "时，在<pre>" . $RowData[3] . "</pre>" .
    "文件的第<pre>" . $RowData[4] . "</pre>行发生了编号为" .
    "<pre>" . $RowData[0] . "</pre>的" .
    "<pre>" . $RowData[1] . "</pre>：<pre>" . $RowData[2] . "</pre>。");
echo "<h4>错误文件</h4>";
echo "<pre>";
$FileContent = mb_split("\n", SanitizeString(file_get_contents($RowData[3])));
for ($i = $RowData[4] - 5; $i < $RowData[4] + 5; $i++) {
    if ($i < 0) {
        continue;
    }
    if ($i >= count($FileContent)) {
        break;
    }
    echo str_pad($i, 3, " ", STR_PAD_LEFT) . "  ";
    if ($i == $RowData[4] - 1)
        echo "<span style=\"color: red;\">" . $FileContent[$i] . "</span>";
    else
        echo $FileContent[$i];
    echo "\n";
}
echo "</pre>";
echo "<h4>错误堆栈</h4>";
echo "<pre>";
print_r(json_decode($RowData[5]));
echo "</pre>";
echo "<br />";
require_once "Footer.php";
