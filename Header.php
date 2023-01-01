<?php
require_once "Function.php";
function is_mobile(): bool
{
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $mobile_browser = array(
        "mqqbrowser", //手机QQ浏览器
        "opera mobi", //手机opera
        "juc", "iuc", //uc浏览器
        "fennec", "ios", "applewebKit/420", "applewebkit/525", "applewebkit/532", "ipad", "iphone", "ipaq", "ipod",
        "iemobile", "windows ce", //windows phone
        "240×320",
        "480×640",
        "acer",
        "android", "anywhereyougo.com", "asus", "audio", "blackberry", "blazer", "coolpad", "dopod", "etouch", "hitachi", "htc", "huawei", "jbrowser", "lenovo", "lg", "lg-", "lge-", "lge", "mobi", "moto", "nokia", "phone", "samsung", "sony", "symbian", "tablet", "tianyu", "wap", "xda", "xde", "zte"
    );
    $is_mobile = false;
    foreach ($mobile_browser as $device) {
        if (stristr($user_agent, $device)) {
            $is_mobile = true;
            break;
        }
    }
    return $is_mobile;
}
echo "<!DOCTYPE html>";
echo "<html lang=\"zh\">";
echo "<head>";
echo "<meta charset=\"utf-8\" />";
echo "<meta name=\"viewport\" content=\"width=device-width\" />";
if (!isset($_SERVER["HTTP_X_REQUESTED_WITH"]) || $_SERVER["HTTP_X_REQUESTED_WITH"] != "Debug") {
    echo "<script src=\"NoConsole.js\"></script>";
}
// echo "<script>while(1){console.log(\"BUG\")}</script>";
// echo "<style>*{filter:grayscale(100%);}</style>";
if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") || strpos($_SERVER['HTTP_USER_AGENT'], "Triden")) {
    echo "<style>@import \"css/Ordinary.css\";</style>";
    echo "<style>@import \"Main.css\";</style>";
    echo "</head>";
    echo "<body>";
    CreateText("请不要使用IE浏览器，因为IE浏览器无法提供所需的支持。推荐使用的浏览器：");
    CreateLink("http://www.firefox.com.cn", "Mozilla火狐浏览器");
    CreateLink("http://www.microsoft.com/zh-cn/edge", "微软Edge浏览器");
    CreateLink("http://www.google.cn/intl/zh-CN/chrome", "谷歌Chrome浏览器");
    echo "</body>";
    echo "</html>";
    die();
}
// if (!isset($_SERVER['HTTPS'])) {
//     echo "<script>";
//     echo "window.location='https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "';";
//     echo "</script>";
//     die();
// }
echo "<style>@import \"css/Ordinary.css\";</style>";
if (is_mobile())
    echo "<style>@import \"Main-Mobile.css\";</style>";
else
    echo "<style>@import \"Main.css\";</style>";
echo "<script src=\"Count.js\"></script>";
echo "<title>";
$TitleList = array();
$TitleList["About.php"] = "关于";
$TitleList["Chat.php"] = "私聊";
$TitleList["Chats.php"] = "私聊列表";
$TitleList["Class.php"] = "班级";
$TitleList["Classes.php"] = "班级列表";
$TitleList["ClassFile.php"] = "班级文件";
$TitleList["ClockIn.php"] = "打卡";
$TitleList["ClockIns.php"] = "打卡列表";
$TitleList["Config.php"] = "管理";
$TitleList["ConfigCaptcha.php"] = "管理图片验证码";
$TitleList["ConfigChat.php"] = "管理私聊";
$TitleList["ConfigClass.php"] = "管理班级";
$TitleList["ConfigDatabase.php"] = "管理数据库";
$TitleList["ConfigNotice.php"] = "管理公告";
$TitleList["ConfigPageCount.php"] = "管理页面访问量";
$TitleList["ConfigPassword.php"] = "管理密码";
$TitleList["ConfigPHPInfo.php"] = "管理PHP信息";
$TitleList["ConfigUI.php"] = "管理界面";
$TitleList["ConfigUser.php"] = "管理用户";
$TitleList["ConfirmEmail.php"] = "确认邮箱";
$TitleList["CreateClockIn.php"] = "创建打卡";
$TitleList["CreateHomework.php"] = "创建作业";
$TitleList["Download.php"] = "下载";
$TitleList["DownloadClockIn.php"] = "下载打卡";
$TitleList["DownloadHomework.php"] = "下载作业";
$TitleList["ForgotPassword.php"] = "忘记密码";
$TitleList["GetTempPassword.php"] = "获取初始密码";
$TitleList["Homework.php"] = "作业";
$TitleList["Homeworks.php"] = "作业列表";
$TitleList["index.php"] = "主页";
$TitleList["Login.php"] = "登录";
$TitleList["Logout.php"] = "登出";
$TitleList["NewMessages.php"] = "新消息";
$TitleList["Notice.php"] = "公告";
$TitleList["Notices.php"] = "公告列表";
$TitleList["PasswordRequirement.php"] = "密码设置要求";
$TitleList["Settings.php"] = "设置";
$TitleList["Upload.php"] = "上传文件";
$TitleList["UploadClockIn.php"] = "提交打卡";
$TitleList["UploadHomework.php"] = "提交作业";
$TitleList["User.php"] = "用户";
$TitleList["ViewClockIn.php"] = "查看打卡";
$TitleList["ViewHomework.php"] = "查看作业";
echo $TitleList[basename($_SERVER['PHP_SELF'])];
echo "</title>";
echo "</head>";
echo "<body>";
echo "<div id=\"menu\">";
echo "<ul>";
if (isset($_SESSION["UID"]) && isset($_SESSION["UserName"]) && isset($_SESSION["UserType"]) && isset($_SESSION["UserEmail"])) {
    echo "<li onclick=\"window.location='index.php'\"><a>主页</a></li>";
    echo "<li onclick=\"window.location='Classes.php'\"><a>班级</a></li>";
    if ($_SESSION["UserType"] != "0") {
        echo "<li onclick=\"window.location='Upload.php'\"><a>文件</a></li>";
    }
    if ($_SESSION["UserType"] == "2") {
        echo "<li onclick=\"window.location='Config.php'\"><a>管理</a></li>";
    }
    echo "<li onclick=\"window.location='Chats.php'\"><a>私聊</a></li>";
    echo "<li onclick=\"window.location='Settings.php'\"><a>设置</a></li>";
    echo "<li onclick=\"window.location='Logout.php'\"><a>退出</a></li>";
} else {
    echo "<li onclick=\"window.location='Login.php'\"><a>登录</a></li>";
}
echo "</ul>";
echo "</div>";
echo "<header>";
echo "<p onclick=\"document.getElementById('menu').style['display']=(document.getElementById('menu').style['display']=='block'?'none':'block')\">";
echo "<span></span>";
echo "<span></span>";
echo "<span></span>";
echo "</p>";
echo "<div>";
echo "</div>";
echo "<span id=\"Title\">建平西校初二23班</span>";
echo "<ul>";
if (isset($_SESSION["UID"]) && isset($_SESSION["UserName"]) && isset($_SESSION["UserType"]) && isset($_SESSION["UserEmail"])) {
    echo "<li onclick=\"window.location='index.php'\"><a>主页</a></li>";
    echo "<li onclick=\"window.location='Classes.php'\"><a>班级</a></li>";
    if ($_SESSION["UserType"] != "0") {
        echo "<li onclick=\"window.location='Upload.php'\"><a>文件</a></li>";
    }
    if ($_SESSION["UserType"] == "2") {
        echo "<li onclick=\"window.location='Config.php'\"><a>管理</a></li>";
    }
    echo "<li onclick=\"window.location='Chats.php'\"><a>私聊</a></li>";
    echo "<li onclick=\"window.location='Settings.php'\"><a>设置</a></li>";
    echo "<li onclick=\"window.location='Logout.php'\"><a>退出</a></li>";
} else {
    echo "<li onclick=\"window.location='Login.php'\"><a>登录</a></li>";
}
echo "</ul>";
echo "</header>";
echo "<div class=\"Main\">";
echo "<pre>";
echo "\$_COOKIE = ";
print_r($_COOKIE);
echo "\$_SESSION = ";
print_r($_SESSION);
echo "\$_POST = ";
print_r($_POST);
echo "\$_GET = ";
print_r($_GET);
echo "</pre>";
echo "<br />";
echo "<h3>" . $TitleList[basename($_SERVER['PHP_SELF'])] . "</h3>";
