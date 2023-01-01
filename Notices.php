<?php
require_once "NotLogin.php";
require_once "Header.php";
$DatabaseQuery = $Database->prepare("SELECT * FROM Notice ORDER BY UploadTime DESC");
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
echo "<table>";
echo "<thead>";
echo "<td style=\"width: 10%\">";
CreateText("编号");
echo "</td>";
echo "<td style=\"width: 20%\">";
CreateText("发布者");
echo "</td>";
echo "<td style=\"width: 40%\">";
CreateText("标题");
echo "</td>";
echo "<td style=\"width: 20%\">";
CreateText("上传时间");
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
    CreateText($RowData[0]);
    echo "</td>";
    echo "<td>";
    echo GetUserName($RowData[1]);
    echo "</td>";
    echo "<td>";
    CreateText($RowData[3]);
    echo "</td>";
    echo "<td>";
    CreateText($RowData[2]);
    echo "</td>";
    echo "<td>";
    CreateLink("Notice.php?NoticeID=" . $RowData[0], "查看");
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
require_once "Footer.php";
