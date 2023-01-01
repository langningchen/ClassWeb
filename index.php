<?php
require_once "NotLogin.php";
require_once "Header.php";
if (isset($_POST["NewMessageID"])) {
    $DatabaseQuery = $Database->prepare("DELETE FROM NewMessageList WHERE NewMessageID=?");
    $DatabaseQuery->bind_param("i", $_POST["NewMessageID"]);
    $DatabaseQuery->execute();
}
CreateText($_SESSION["UserName"] . "，您好。您的用户编号为" . $_SESSION["UID"] . "。");
echo "<br />";
$DatabaseQuery = $Database->prepare("SELECT * FROM NewMessageList WHERE UID=? ORDER BY Time DESC");
$DatabaseQuery->bind_param("i", $_SESSION["UID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
echo "<h4>新消息</h4>";
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
if (min($Result->num_rows, 3) == 0) {
    echo "<tr><td>";
    CreateText("空");
    echo "</td></tr>";
}
for ($i = 0; $i < min($Result->num_rows, 3); $i++) {
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
echo "<div style=\"text-align: right; position: relative; right: 0px;\">";
CreateLink("NewMessages.php", "查看更多");
echo "</div>";
$DatabaseQuery = $Database->prepare("SELECT * FROM Notice ORDER BY UploadTime DESC");
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
echo "<h4>公告</h4>";
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
if (min($Result->num_rows, 3) == 0) {
    echo "<tr><td>";
    CreateText("空");
    echo "</td></tr>";
}
for ($i = 0; $i < min($Result->num_rows, 3); $i++) {
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
    echo "<input type=\"submit\" class=\"SecondButton\" onclick=\"window.location='Notice.php?NoticeID=" . $RowData[0] . "'\" value=\"查看\">";
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
echo "<div style=\"text-align: right; position: relative; right: 0px;\">";
CreateLink("Notices.php", "查看更多");
echo "</div>";
require_once "Footer.php";
