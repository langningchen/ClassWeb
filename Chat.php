<?php
require_once "NotLogin.php";
require_once "Header.php";
$WithDrawMessage = "<span style=\"color: var(--Color-Black-Brighter) !important; \"><i>该内容已撤回</i></span>";
if (!isset($_GET["UID"]) || $_GET["UID"] == "") {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
if ($_GET["UID"] == $_SESSION["UID"]) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
if ($_SESSION["UserType"] != 2 && !in_array($_GET["UID"], GetFriends())) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
if (isset($_POST["ID"])) {
    if (isset($_POST["Delete"])) {
        $DatabaseQuery = $Database->prepare("DELETE FROM ChatList WHERE ID=?");
        $DatabaseQuery->bind_param("i", $_POST["ID"]);
        $DatabaseQuery->execute();
    } else if (isset($_POST["Withdraw"])) {
        $DatabaseQuery = $Database->prepare("SELECT TheOther FROM ChatList WHERE ID=?");
        $DatabaseQuery->bind_param("i", $_POST["ID"]);
        $DatabaseQuery->execute();
        $RowData = $DatabaseQuery->get_result()->fetch_array(MYSQLI_NUM);
        $TheOther = $RowData[0];
        $DatabaseQuery = $Database->prepare("UPDATE ChatList SET Data=? WHERE ID=?");
        $DatabaseQuery->bind_param("si", $WithDrawMessage, $_POST["ID"]);
        $DatabaseQuery->execute();
        $DatabaseQuery = $Database->prepare("UPDATE ChatList SET Data=? WHERE ID=?");
        $DatabaseQuery->bind_param("si", $WithDrawMessage, $TheOther);
        $DatabaseQuery->execute();
    }
}
if (isset($_POST["Send"]) && isset($_POST["Value"])) {
    if ($_POST["Value"] != "") {
        $_POST["Value"] = SanitizeString($_POST["Value"]);
        $DatabaseQuery = $Database->prepare("INSERT INTO ChatList(UID, SendUID, ReceiveUID, Data, SendTime) VALUES (?, ?, ?, ?, current_timestamp())");
        $DatabaseQuery->bind_param("iiis", $_GET["UID"], $_SESSION["UID"], $_GET["UID"], $_POST["Value"]);
        $DatabaseQuery->execute();
        $TempIndex = $DatabaseQuery->insert_id;
        $DatabaseQuery = $Database->prepare("INSERT INTO ChatList(UID, SendUID, ReceiveUID, Data, SendTime, TheOther) VALUES (?, ?, ?, ?, current_timestamp(),?)");
        $DatabaseQuery->bind_param("iiisi", $_SESSION["UID"], $_SESSION["UID"], $_GET["UID"], $_POST["Value"], $TempIndex);
        $DatabaseQuery->execute();
        AddMessage($_GET["UID"], GetUserName($_SESSION["UID"]) . "给您发来了私聊", "Chat.php?UID=" . $_SESSION["UID"]);
    }
}
$DatabaseQuery = $Database->prepare("SELECT ID, SendUID, ReceiveUID, Data, SendTime, TheOther FROM ChatList WHERE UID=? AND ((SendUID=? AND ReceiveUID=?) OR (SendUID=? AND ReceiveUID=?)) ORDER BY SendTime DESC");
$DatabaseQuery->bind_param("iiiii", $_SESSION["UID"], $_GET["UID"], $_SESSION["UID"], $_SESSION["UID"], $_GET["UID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
echo "<h4>与" . GetUserName($_GET["UID"]) . "私聊</h4>";
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
echo "<textarea class=\"Input\" name=\"Value\"></textarea>";
echo "<br />";
echo "<input class=\"MainButton\" type=\"submit\" value=\"发送\" name=\"Send\" />";
echo "</form>";
echo "<br />";
echo "<input class=\"SecondButton\" type=\"button\" value=\"刷新\" onclick=\"window.location = window.location\" />";
echo "<br />";
echo "<table>";
echo "<thead>";
echo "<td style=\"width: 10%\">";
CreateText("发送");
echo "</td>";
echo "<td style=\"width: 10%\">";
CreateText("接收");
echo "</td>";
echo "<td style=\"width: 20%\">";
CreateText("发送时间");
echo "</td>";
echo "<td style=\"width: 50%\">";
CreateText("内容");
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
    echo "<td>" . GetUserName($RowData[1]) . "</td>";
    echo "<td>" . GetUserName($RowData[2]) . "</td>";
    echo "<td><span class=\"Text\">" . $RowData[4] . "</span></td>";
    echo "<td><span class=\"Text\">" . $RowData[3] . "</span></td>";
    echo "<td>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"ID\" value=\"" . $RowData[0] . "\" />";
    echo "<input class=\"DangerousButton\" type=\"submit\" name=\"Delete\" value=\"删除\" />";
    if ($RowData[3] != $WithDrawMessage && $RowData[1] == $_SESSION["UID"]) {
        echo "<input class=\"WarningButton\" type=\"submit\" name=\"Withdraw\" value=\"撤回\" />";
    }
    echo "</form>";
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
require_once "Footer.php";
