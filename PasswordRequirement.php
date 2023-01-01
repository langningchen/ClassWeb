<?php
require_once "NotLogin.php";
require_once "Header.php";
echo "<h4>规则</h4>";
CreateText("1. 密码不能不包含数字、大小写字母和特殊字符");
echo "<br />";
CreateText("2. 密码不能出现重复的字母或数字组合或大于两次地多次重复出现字母或数字");
echo "<br />";
CreateText("3. 密码不能包含生日");
echo "<br />";
CreateText("4. 密码不能少于8位或多于128位");
echo "<br />";
CreateText("5. 密码不能连续或间隔使用字母表中或键盘上连续的数字或字母");
echo "<br />";

echo "<h4>错误范例</h4>";

echo "<pre>";
CreateText("20030925ZhangSan");
echo "</pre>";
CreateText("不符合1.和3.");
echo "<br />";

echo "<pre>";
CreateText("2*HaHa!");
echo "</pre>";
CreateText("不符合2.");
echo "<br />";


echo "<pre>";
CreateText("GF^H+I4HHHhvPo8");
echo "</pre>";
CreateText("不符合2.");
echo "<br />";

echo "<pre>";
CreateText("4G$5<k@");
echo "</pre>";
CreateText("不符合4.");
echo "<br />";

echo "<pre>";
CreateText("1q2w3e!Q@W#E");
echo "</pre>";
CreateText("不符合5.");
echo "<br />";

echo "<h4>正确范例</h4>";
echo "<pre>";
CreateText(SanitizeString("_^[NG34G$5<k@PjF"));
echo "</pre>";
echo "<br />";

echo "<pre>";
CreateText(SanitizeString("B)y(11H5d2PPLn#k"));
echo "</pre>";
echo "<br />";

echo "<pre>";
CreateText(SanitizeString("4X3eO]js4$+=NwbN"));
echo "</pre>";
echo "<br />";

echo "<pre>";
CreateText(SanitizeString("-},}a3kH9a#v2ka."));
echo "</pre>";
echo "<br />";

echo "<pre>";
CreateText(SanitizeString(">Eq&7zd36r078hiz"));
echo "</pre>";
echo "<br />";

require_once "Footer.php";
