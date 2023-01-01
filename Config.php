<?php
require_once "NotLogin.php";
require_once "Header.php";
if ($_SESSION["UserType"] != 2) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
CreateLink("ConfigUser.php", "管理用户");
echo "<br />";
CreateLink("ConfigClass.php", "管理班级");
echo "<br />";
CreateLink("ConfigChat.php", "管理私聊");
echo "<br />";
CreateLink("ConfigNotice.php", "管理公告");
echo "<br />";
CreateLink("ConfigUI.php", "管理界面");
echo "<br />";
CreateLink("ConfigPageCount.php", "管理页面访问量");
echo "<br />";
CreateLink("ConfigCaptcha.php", "管理图片验证码");
echo "<br />";
CreateLink("ConfigDatabase.php", "管理数据库");
echo "<br />";
CreateLink("ConfigPHPInfo.php", "管理PHP信息");
require_once "Footer.php";
