<?php
require_once "NotLogin.php";
require_once "Header.php";
if ($_SESSION["UserType"] != 2) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
if (isset($_POST["ChangeName"]) && isset($_POST["ClassID"]) && isset($_POST["NewClassName"])) {
    $DatabaseQuery = $Database->prepare("UPDATE ClassList SET ClassName=? WHERE ClassID=?");
    $DatabaseQuery->bind_param("si", $_POST["NewClassName"], $_POST["ClassID"]);
    $DatabaseQuery->execute();
}
if (isset($_POST["Delete"]) && isset($_POST["ClassID"])) {
    $DatabaseQuery = $Database->prepare("DELETE FROM ClassList WHERE ClassID=?");
    $DatabaseQuery->bind_param("i", $_POST["ClassID"]);
    $DatabaseQuery->execute();

    $DatabaseQuery = $Database->prepare("DELETE FROM ClassFileList WHERE ClassID=?");
    $DatabaseQuery->bind_param("i", $_POST["ClassID"]);
    $DatabaseQuery->execute();

    $DatabaseQuery = $Database->prepare("SELECT HomeworkID FROM HomeworkList WHERE ClassID=?");
    $DatabaseQuery->bind_param("i", $_POST["ClassID"]);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    for ($i = 0; $i < $Result->num_rows; $i++) {
        $Result->data_seek($i);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        DeleteHomework($RowData[0]);
    }
    $DatabaseQuery = $Database->prepare("DELETE FROM HomeworkList WHERE ClassID=?");
    $DatabaseQuery->bind_param("i", $_POST["ClassID"]);
    $DatabaseQuery->execute();

    $DatabaseQuery = $Database->prepare("SELECT ClockInID FROM ClockInList WHERE ClassID=?");
    $DatabaseQuery->bind_param("i", $_POST["ClassID"]);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    for ($i = 0; $i < $Result->num_rows; $i++) {
        $Result->data_seek($i);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        DeleteClockIn($RowData[0]);
    }
    $DatabaseQuery = $Database->prepare("DELETE FROM ClockInList WHERE ClassID=?");
    $DatabaseQuery->bind_param("i", $_POST["ClassID"]);
    $DatabaseQuery->execute();
}
$DatabaseQuery = $Database->prepare("SELECT * FROM ClassList");
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
echo "<table>";
echo "<thead>";
echo "<td style=\"width: 5%\">";
CreateText("编号");
echo "</td>";
echo "<td style=\"width: 5%\">";
CreateText("名称");
echo "</td>";
echo "<td style=\"width: 5%\">";
CreateText("管理员");
echo "</td>";
echo "<td style=\"width: 10%\">";
CreateText("教师");
echo "</td>";
echo "<td style=\"width: 70%\">";
CreateText("学生");
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
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"ClassID\" value=\"" . $RowData[0] . "\" />";
    echo "<input class=\"Input\" type=\"text\" name=\"NewClassName\" value=\"" . $RowData[1] . "\" />";
    echo "<input class=\"WarningButton\" type=\"submit\" name=\"ChangeName\" value=\"更改\" />";
    echo "</form>";
    echo "</td>";
    echo "<td>";
    echo GetUserName($RowData[2]);
    echo "</td>";
    echo "<td>";
    $TempArray = mb_split(",", $RowData[3]);
    for ($i = 0; $i < count($TempArray); $i++) {
        if ($TempArray[$i] == "") continue;
        echo GetUserName($TempArray[$i]);
    }
    echo "</td>";
    echo "<td>";
    $TempArray = mb_split(",", $RowData[4]);
    for ($i = 0; $i < count($TempArray); $i++) {
        if ($TempArray[$i] == "") continue;
        echo GetUserName($TempArray[$i]);
    }
    echo "</td>";
    echo "<td>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"ClassID\" value=\"" . $RowData[0] . "\" />";
    echo "<input class=\"DangerousButton\" type=\"submit\" name=\"Delete\" value=\"删除\" />";
    echo "</form>";
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
require_once "Footer.php";
