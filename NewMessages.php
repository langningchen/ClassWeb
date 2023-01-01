<?php
require_once "NotLogin.php";
require_once "Header.php";
$DatabaseQuery = $Database->prepare("SELECT * FROM NewMessageList WHERE UID=? ORDER BY Time DESC");
$DatabaseQuery->bind_param("i", $_SESSION["UID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
echo "<table>";
echo "<thead>";
echo "<td style=\"width: 70%\">";
CreateText("内容");
echo "</td>";
echo "<td style=\"width: 20%\">";
CreateText("时间");
echo "</td>";
echo "<td style=\"width: 10%\">";
CreateText("操作");
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
    CreateText($RowData[2]);
    echo "</td>";
    echo "<td>";
    CreateText($RowData[4]);
    echo "</td>";
    echo "<td>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"submit\" class=\"SecondButton\" onclick=\"window.location='" . $RowData[3] . "'\" value=\"查看\">";
    echo "<input type=\"hidden\" name=\"NewMessageID\" value=\"" . $RowData[0] . "\" />";
    echo "<input type=\"submit\" class=\"DangerousButton\" value=\"删除\">";
    echo "</form>";
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
require_once "Footer.php";
