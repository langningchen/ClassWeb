<?php
require_once "NotLogin.php";
require_once "Header.php";
if ($_SESSION["UserType"] != 2) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
if (isset($_POST["NoticeID"])) {
    $DatabaseQuery = $Database->prepare("DELETE FROM Notice WHERE NoticeID=?");
    $DatabaseQuery->bind_param("i", $_POST["NoticeID"]);
    $DatabaseQuery->execute();
}
if (isset($_POST["Title"]) && isset($_POST["Data"])) {
    $DatabaseQuery = $Database->prepare("INSERT INTO Notice(UploadUID, Title, Data, UploadTime) VALUES (?, ?, ?, current_timestamp())");
    $DatabaseQuery->bind_param("iss", $_SESSION["UID"], $_POST["Title"], $_POST["Data"]);
    $DatabaseQuery->execute();
}
echo "<h4>新增公告</h4>";
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
CreateText("标题：");
echo "<input class=\"Input\" type=\"text\" required name=\"Title\" />";
echo "<br />";
CreateText("内容：");
echo "<br />";
echo "<textarea class=\"Input\" type=\"text\" required name=\"Data\"></textarea>";
echo "<br />";
echo "<input class=\"MainButton\" type=\"submit\" name=\"New\" value=\"增加\" />";
echo "</form>";
echo "<h4>公告列表</h4>";
$DatabaseQuery = $Database->prepare("SELECT * FROM Notice");
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
echo "<table>";
echo "<thead>";
echo "<td style=\"width: 5%\">";
CreateText("公告编号");
echo "</td>";
echo "<td style=\"width: 5%\">";
CreateText("发布者");
echo "</td>";
echo "<td style=\"width: 10%\">";
CreateText("发布时间");
echo "</td>";
echo "<td style=\"width: 10%\">";
CreateText("标题");
echo "</td>";
echo "<td style=\"width: 50%\">";
CreateText("内容");
echo "</td>";
echo "<td style=\"width: 5%\">";
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
    CreateText($RowData[2]);
    echo "</td>";
    echo "<td>";
    CreateText($RowData[3]);
    echo "</td>";
    echo "<td>";
    CreateText($RowData[4]);
    echo "</td>";
    echo "<td>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"NoticeID\" value=\"$RowData[0]\" />";
    echo "<input class=\"DangerousButton\" type=\"submit\" value=\"删除\" />";
    echo "</form>";
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
require_once "Footer.php";
