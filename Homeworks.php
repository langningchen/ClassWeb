<?php
require_once "NotLogin.php";
require_once "Header.php";
if (!isset($_GET["ClassID"])) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
$Member = GetClassType($_GET["ClassID"]);
if ($Member == "") {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
if (isset($_POST["HomeworkID"]) && isset($_POST["Delete"])) {
    DeleteHomework($_POST["HomeworkID"]);
}
if ($Member != "学生") {
    echo "<input class=\"MainButton\" type=\"button\" onclick=\"window.location='CreateHomework.php?ClassID=" . $_GET["ClassID"] . "'\" value=\"布置作业\" />";
    echo "<br />";
}
$DatabaseQuery = $Database->prepare("SELECT HomeworkID,UploadUID,Title,CreateTime,EndTime,NeedUpload FROM HomeworkList WHERE ClassID=? ORDER BY HomeworkList.CreateTime DESC");
$DatabaseQuery->bind_param("i", $_GET["ClassID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
CreateText("作业数量：");
CreateText($Result->num_rows);
echo "<br />";
echo "<table>";
echo "<thead>";
echo "<td width=\"10%\">";
CreateText("作业编号");
echo "</td>";
echo "<td width=\"10%\">";
CreateText("布置人");
echo "</td>";
echo "<td width=\"20%\">";
CreateText("布置时间");
echo "</td>";
echo "<td width=\"20%\">";
CreateText("截止时间");
echo "</td>";
echo "<td width=\"20%\">";
CreateText("标题");
echo "</td>";
echo "<td width=\"10%\">";
CreateText("状态");
echo "</td>";
echo "<td width=\"10%\">";
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
    if ($RowData[5]) {
        CreateText($RowData[4]);
    } else {
        CreateText("无需提交");
    }
    echo "</td>";
    echo "<td>";
    CreateText($RowData[2]);
    echo "</td>";
    $DatabaseStatusQuery = $Database->prepare("SELECT Status FROM HomeworkUploadList WHERE HomeworkID=? AND UploadUID=?");
    $DatabaseStatusQuery->bind_param("ii", $RowData[0], $_SESSION["UID"]);
    $DatabaseStatusQuery->execute();
    $StatusResult = $DatabaseStatusQuery->get_result();
    echo "<td>";
    if ($StatusResult->num_rows == 0) {
        CreateText(GetHomeworkStatusName(0));
    } else {
        $StatusResult->data_seek(0);
        $StatusRowData = $StatusResult->fetch_array(MYSQLI_NUM);
        CreateText(GetHomeworkStatusName($StatusRowData[0]));
    }
    echo "</td>";
    echo "<td>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"HomeworkID\" value=\"" . $RowData[0] . "\" />";
    echo "<input class=\"SecondButton\" type=\"button\" onclick=\"window.location='Homework.php?HomeworkID=" . $RowData[0] . "'\" value=\"查看\" />";
    if ($Member != "学生" && $RowData[1] == $_SESSION["UID"]) {
        echo "<input class=\"DangerousButton\" type=\"submit\" name=\"Delete\" value=\"删除\"/>";
    }
    echo "</form>";
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
require_once "Footer.php";
