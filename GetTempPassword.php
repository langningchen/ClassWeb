<?php
require_once "Function.php";
require_once "Header.php";
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
CreateText("请输入您的学号：");
echo "<input class=\"Input\" type=\"number\" require=\"required\" min=\"1\" max=\"50\" name=\"StudentNumber\" />";
echo "<br />";
CreateText("请输入您的学籍号（仅作为身份验证）：");
echo "<input class=\"Input\" type=\"number\" require=\"required\" style=\"width: 20%\" name=\"Number\" />";
echo "<br />";
echo "<input class=\"MainButton\" type=\"submit\" value=\"查询\" />";
echo "</form>";
if (isset($_POST["StudentNumber"]) && isset($_POST["Number"])) {
    $UserName = "23" . $_POST["StudentNumber"];
    $DatabaseQuery = $Database->prepare("SELECT Password FROM TempPassword WHERE UserName=? AND Number=?");
    $DatabaseQuery->bind_param("ss", $UserName, $_POST["Number"]);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    if ($Result->num_rows == 0) {
        CreateErrorText("没有查找到符合条件的用户");
        echo "<br />";
    } else if ($Result->num_rows > 1) {
        CreateErrorText("系统错误：有多个信息重复的用户");
        echo "<br />";
    } else {
        $Result->data_seek(0);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        CreateText("您的用户名是：“" . $UserName . "”。");
        echo "<br />";
        CreateText("您的密码是：“" . SanitizeString($RowData[0]) . "”。");
        echo "<br />";
        CreateText("注意：用户名和密码均不包括最外层的双引号！");
        echo "<br />";
        echo "<br />";
        CreateText("请在登录后进入设置并修改密码。");
        echo "<br />";
        CreateText("为了您的信息安全，服务器不会储存您的密码（除了初始密码），您的密码将会经过不可逆的加密后储存进入数据库。当您更改了密码以后，在此处再次查询时查询到的密码仍然是初始密码，不会是您更改后的密码。");
        echo "<br />";
        echo "<br />";
        echo "<a class=\"Link\" href=\"Login.php\">点我去登录</a>";
    }
}
require_once "Footer.php";
