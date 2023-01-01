<?php
require_once "NotLogin.php";
require_once "Header.php";
if ($_SESSION["UserType"] != 2) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
if (isset($_POST["Clear"])) {
    $DatabaseQuery = $Database->prepare("TRUNCATE PageCount");
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
}
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
echo "<input type=\"button\" class=\"GoodButton\" onclick=\"window.location.href='ConfigPageCount.php'\" value=\"清除筛选\" />";
echo "<input class=\"WarningButton\" name=\"Clear\" type=\"submit\" value=\"清空\" />";
echo "</form>";
$QueryString = "SELECT * FROM PageCount WHERE 1=1";
if (isset($_GET["User"]))
    $QueryString .= " AND UID=" . $_GET["User"];
$QueryString .= " ORDER BY PageCountID DESC";
$DatabaseQuery = $Database->prepare($QueryString);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
echo "<h4>页面访问记录</h4>";
echo "<table>";
echo "<thead>";
echo "<td style=\"width: 10%; \">";
CreateText("编号");
echo "</td>";
echo "<td style=\"width: 30%; \">";
CreateText("页面");
echo "</td>";
echo "<td style=\"width: 20%; \">";
CreateText("用户");
echo "</td>";
echo "<td style=\"width: 20%; \">";
CreateText("IP");
echo "</td>";
echo "<td style=\"width: 20%; \">";
CreateText("时间");
echo "</td>";
echo "</thead>";
echo "<tbody>";
if ($Result->num_rows == 0) {
    echo "<tr><td>";
    CreateText("空");
    echo "</td></tr>";
}
for ($i = 0; $i < $Result->num_rows; $i++) {
    $Result->data_seek($i);
    $RowData = $Result->fetch_array(MYSQLI_NUM);
    echo "<tr>";
    echo "<td>";
    CreateText($RowData[0]);
    echo "</td>";
    echo "<td>";
    CreateText(SanitizeString($RowData[1]));
    echo "</td>";
    echo "<td>";
    echo GetUserName($RowData[2]);
    echo "<input type=\"button\" class=\"SecondButton\" id=\"Search\" onclick=\"window.location.href='ConfigPageCount.php?User=" . $RowData[2] . "'\" value=\"筛选\" />";
    echo "</td>";
    echo "<td>";
    CreateText($RowData[3]);
    echo "</td>";
    echo "<td>";
    CreateText($RowData[4]);
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
