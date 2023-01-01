<?php
require_once "NotLogin.php";
require_once "Header.php";
$DatabaseQuery = $Database->prepare("SELECT ClassID, ClassName, ClassAdmin FROM ClassList");
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
echo "<table>";
echo "<thead>";
echo "<td style=\"width: 10%\">";
CreateText("编号");
echo "</td>";
echo "<td style=\"width: 30%\">";
CreateText("名称");
echo "</td>";
echo "<td style=\"width: 20%\">";
CreateText("管理员");
echo "</td>";
echo "<td style=\"width: 20%\">";
CreateText("您的在班中的身份");
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
    $Member = GetClassType($RowData[0]);
    if ($Member != "") {
        echo "<tr>";
        echo "<td>";
        CreateText($RowData[0]);
        echo "</td>";
        echo "<td>";
        CreateText($RowData[1]);
        echo "</td>";
        echo "<td>";
        echo GetUserName($RowData[2]);
        echo "</td>";
        echo "<td>";
        CreateText($Member);
        echo "</td>";
        echo "<td>";
        CreateLink("Class.php?ClassID=" . $RowData[0], "进入");
        echo "</td>";
        echo "</tr>";
    }
}
echo "</tbody>";
echo "</table>";
require_once "Footer.php";
