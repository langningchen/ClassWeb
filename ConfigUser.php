<?php
require_once "NotLogin.php";
require_once "Header.php";
if ($_SESSION["UserType"] != 2) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
$RandPassword = "";
$EncryptedPassword = "";
if (isset($_POST["Calc"])) {
    $RandPassword = $_POST["Password"];
    $EncryptedPassword = EncodePassword($_POST["Password"]);
}
if (isset($_POST["New"])) {
    $DatabaseQuery = $Database->prepare("INSERT INTO UserList(UserName, Password, UserType) VALUES (?,?,?)");
    $DatabaseQuery->bind_param("ssi", $_POST["Username"], $_POST["Password"], $_POST["Type"]);
    $DatabaseQuery->execute();
}
if (isset($_POST["Delete"])) {
    $DatabaseQuery = $Database->prepare("DELETE FROM UserList WHERE UID=?");
    $DatabaseQuery->bind_param("i", $_POST["UID"]);
    $DatabaseQuery->execute();
}
if (isset($_POST["ChangePassword"])) {
    $DatabaseQuery = $Database->prepare("UPDATE UserList SET Password=? WHERE UID=?");
    $DatabaseQuery->bind_param("si", $_POST["Password"], $_POST["UID"]);
    $DatabaseQuery->execute();
}
if (isset($_POST["ChangeType"])) {
    $DatabaseQuery = $Database->prepare("UPDATE UserList SET UserType=? WHERE UID=?");
    $DatabaseQuery->bind_param("ii", $_POST["Type"], $_POST["UID"]);
    $DatabaseQuery->execute();
}
if (isset($_POST["RandPassword"])) {
    $RandPassword = CreateRandPassword();
}
$DatabaseQuery = $Database->prepare("SELECT * FROM UserList");
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
echo "<h4>新增用户</h4>";
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
CreateText("加密前的密码");
echo "<input class=\"Input\" type=\"text\" name=\"Password\" value=\"" . $RandPassword . "\" />";
echo "<input class=\"MainButton\" type=\"submit\" name=\"Calc\" value=\"计算\" />";
echo "<input class=\"SecondButton\" type=\"submit\" name=\"RandPassword\" value=\"随机生成密码\" />";
CreateText($EncryptedPassword);
echo "</form>";
echo "<h4>新增用户</h4>";
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
CreateText("用户名");
echo "<input class=\"Input\" type=\"text\" required name=\"Username\" />";
CreateText("加密后的密码");
echo "<input class=\"Input\" type=\"password\" required name=\"Password\" />";
CreateText("用户类型");
echo "<input class=\"Input\" type=\"number\" required min=\"0\" max=\"3\" value=\"0\" name=\"Type\" />";
echo "<input class=\"MainButton\" type=\"submit\" name=\"New\" value=\"增加\" />";
echo "</form>";
echo "<h4>用户列表</h4>";
echo "<table>";
echo "<thead>";
echo "<td style=\"width: 5%\">";
CreateText("UID");
echo "</td>";
echo "<td style=\"width: 10%\">";
CreateText("用户名");
echo "</td>";
echo "<td style=\"width: 25%\">";
CreateText("密码");
echo "</td>";
echo "<td style=\"width: 15%\">";
CreateText("用户类型");
echo "</td>";
echo "<td style=\"width: 20%\">";
CreateText("绑定的邮箱");
echo "</td>";
echo "<td style=\"width: 15%\">";
CreateText("最后登录时间");
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
    CreateText($RowData[1]);
    echo "</td>";
    echo "<td>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"UID\" value=\"$RowData[0]\" />";
    echo "<input class=\"Input\" type=\"text\" required name=\"Password\" value=\"$RowData[2]\" />";
    echo "<input class=\"DangerousButton\" type=\"submit\" name=\"ChangePassword\" value=\"更改密码\" />";
    echo "</form>";
    echo "</td>";
    echo "<td>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"UID\" value=\"$RowData[0]\" />";
    echo "<input class=\"Input\" type=\"number\" required min=\"0\" max=\"3\" value=\"$RowData[3]\" name=\"Type\" />";
    echo "<input class=\"WarningButton\" type=\"submit\" name=\"ChangeType\" value=\"更改权限\" />";
    echo "</form>";
    echo "</td>";
    echo "<td>";
    CreateText($RowData[4]);
    echo "</td>";
    echo "<td>";
    CreateText($RowData[5]);
    echo "</td>";
    echo "<td>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"UID\" value=\"$RowData[0]\" />";
    echo "<input class=\"DangerousButton\" type=\"submit\" name=\"Delete\" value=\"删除\" />";
    echo "</form>";
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
require_once "Footer.php";
