<?php
require_once "NotLogin.php";
require_once "Header.php";
if ($_SESSION["UserType"] != 2) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
if (isset($_POST["ID"])) {
    $DatabaseQuery = $Database->prepare("DELETE FROM ChatList WHERE ID=?");
    $DatabaseQuery->bind_param("i", $_POST["ID"]);
    $DatabaseQuery->execute();
}
if (isset($_POST["Send"]) && isset($_POST["From"]) && isset($_POST["To"]) && isset($_POST["Value"])) {
    $DatabaseQuery = $Database->prepare("INSERT INTO ChatList(UID, SendUID, ReceiveUID, Data, SendTime) VALUES (?,?,?,?,current_timestamp())");
    $DatabaseQuery->bind_param("iiis", $_POST["To"], $_POST["From"], $_POST["To"], $_POST["Value"]);
    $DatabaseQuery->execute();
    $TempIndex = $DatabaseQuery->insert_id;
    $DatabaseQuery = $Database->prepare("INSERT INTO ChatList(UID, SendUID, ReceiveUID, Data, SendTime, TheOther) VALUES (?,?,?,?,current_timestamp(),?)");
    $DatabaseQuery->bind_param("iiisi", $_POST["From"], $_POST["From"], $_POST["To"], $_POST["Value"], $TempIndex);
    $DatabaseQuery->execute();
}
$DatabaseQuery = $Database->prepare("SELECT * FROM ChatList ORDER BY SendTime DESC");
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
echo "<h4>新建私聊</h4>";
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
CreateText("发送者：");
echo "<input class=\"Input\" min=\"1\" value=\"1\" type=\"number\" name=\"From\" />";
echo "&emsp;&emsp;";
CreateText("接收者：");
echo "<input class=\"Input\" min=\"1\" value=\"2\" type=\"number\" name=\"To\" />";
echo "<br />";
CreateText("内容：");
echo "<br />";
echo "<textarea class=\"Input\" name=\"Value\"></textarea>";
echo "<br />";
echo "<input class=\"MainButton\" type=\"submit\" value=\"发送\" name=\"Send\" />";
echo "</form>";
echo "<h4>私聊列表</h4>";
echo "<table>";
echo "<thead>";
echo "<td style=\"width: 10%\">";
CreateText("编号");
echo "</td>";
echo "<td style=\"width: 10%\">";
CreateText("记录人");
echo "</td>";
echo "<td style=\"width: 10%\">";
CreateText("发送");
echo "</td>";
echo "<td style=\"width: 10%\">";
CreateText("接收");
echo "</td>";
echo "<td style=\"width: 10%\">";
CreateText("发送时间");
echo "</td>";
echo "<td style=\"width: 30%\">";
CreateText("内容");
echo "</td>";
echo "<td style=\"width: 10%\">";
CreateText("另一个");
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
    echo GetUserName($RowData[2]);
    echo "</td>";
    echo "<td>";
    echo GetUserName($RowData[3]);
    echo "</td>";
    echo "<td>";
    CreateText($RowData[5]);
    echo "</td>";
    echo "<td>";
    CreateText($RowData[4]);
    echo "</td>";
    echo "<td>";
    if ($RowData[6] != null)
        CreateText($RowData[6]);
    echo "</td>";
    echo "<td>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"ID\" value=\"$RowData[0]\" />";
    echo "<input class=\"DangerousButton\" type=\"submit\" value=\"删除\" />";
    echo "</form>";
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
require_once "Footer.php";
