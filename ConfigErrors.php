<?php
require_once "NotLogin.php";
require_once "Header.php";
if ($_SESSION["UserType"] != 2) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
if (isset($_POST["ErrorLogID"])) {
    $DatabaseQuery = $Database->prepare("DELETE FROM ErrorLog WHERE ErrorLogID=?");
    $DatabaseQuery->bind_param("i", $_POST["ErrorLogID"]);
    $DatabaseQuery->execute();
}
$DatabaseQuery = $Database->prepare("SELECT * FROM ErrorLog ORDER BY ErrorLogID DESC");
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<td>错误编号</td>";
echo "<td>错误类型</td>";
echo "<td>错误文件</td>";
echo "<td>错误时间</td>";
echo "<td>错误用户</td>";
echo "<td>操作</td>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
for ($i = 0; $i < $Result->num_rows; $i++) {
    $Result->data_seek($i);
    $RowData = $Result->fetch_array(MYSQLI_NUM);
    echo "<tr>";
    echo "<td>$RowData[0]</td>";
    echo "<td>$RowData[1]</td>";
    echo "<td>$RowData[3]:$RowData[4]</td>";
    echo "<td>$RowData[6]</td>";
    echo "<td>$RowData[8]($RowData[7])</td>";
    echo "<td>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"ErrorLogID\" value=\"$RowData[0]\">";
    echo "<input type=\"button\" class=\"SecondButton\" value=\"查看\" onclick=\"window.location.href='ConfigError.php?ErrorLogID=" . $RowData[0] . "';\" />";
    echo "<input type=\"submit\" class=\"WarningButton\" value=\"删除\" name=\"Delete\" />";
    echo "</form>";
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
