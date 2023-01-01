<?php
require_once "NotLogin.php";
require_once "Header.php";
if ($_SESSION["UserType"] != 2) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
echo "<input class=\"Input\" value=\"输入框\" /><img class=\"AuthCodePic\" src=\"GetCaptcha.php\"></img><br />";
echo "<input class=\"MainButton\" type=\"button\" value=\"主要按钮\" />";
echo "<input class=\"SecondButton\" type=\"button\" value=\"次要按钮\" />";
echo "<input class=\"DangerousButton\" type=\"button\" value=\"危险按钮\" />";
echo "<input class=\"WarningButton\" type=\"button\" value=\"警告按钮\" />";
echo "<input class=\"GoodButton\" type=\"button\" value=\"好的按钮\" />";
echo "<input class=\"BadButton\" type=\"button\" value=\"禁用按钮\" />";
echo "<textarea class=\"Input\">多行输入框</textarea><br />";
CreateLink("", "链接");
echo GetUserName(1);
echo GetUserName(2);
echo GetUserName(3);
echo GetUserName(4);
echo "<h3>H3标题</h3>";
CreateText("内容");
echo "<h4>H4标题</h4>";
CreateText("内容");
echo "<br />";
echo "<pre>代码块</pre>";
require_once "Footer.php";
