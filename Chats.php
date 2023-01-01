<?php
require_once "NotLogin.php";
require_once "Function.php";
require_once "Header.php";
$TempArray = GetFriends();
echo "<table>";
echo "<thead>";
echo "<td width=\"10%\">";
CreateText("用户名");
echo "</td>";
echo "<td>";
CreateText("操作");
echo "</td>";
echo "</thead>";
echo "<tbody>";
if (count($TempArray) == 0) {
    echo "<tr><td>";
    CreateText("空");
    echo "</td></tr>";
}
for ($i = 0; $i < count($TempArray); $i++) {
    echo "<tr>";
    echo "<td>";
    echo GetUserName($TempArray[$i]);
    echo "</td>";
    echo "<td>";
    CreateLink("Chat.php?UID=" . $TempArray[$i], "进入");
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
require_once "Footer.php";
