<?php
require_once "NotLogin.php";
require_once "Header.php";

echo "<h4>更改密码</h4>";
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
CreateText("旧密码");
echo "<input class=\"Input\" type=\"password\" required name=\"OldPassword\" />";
echo "<br />";
CreateText("新密码");
echo "<input class=\"Input\" type=\"password\" required name=\"Password\" />";
echo "<br />";
CreateText("重复一遍新密码");
echo "<input class=\"Input\" type=\"password\" required name=\"CopyPassword\" />";
echo "<br />";
echo "<input class=\"DangerousButton\" type=\"submit\" name=\"ChangePassword\" value=\"更改\" />";
echo "</form>";
if (isset($_POST["ChangePassword"]) && isset($_POST["OldPassword"]) && isset($_POST["Password"]) && isset($_POST["CopyPassword"])) {
    if (EncodePassword($_POST["OldPassword"]) != $_SESSION["Password"]) {
        CreateErrorText("旧密码错误");
        echo "<br />";
    } else if ($_POST["Password"] != $_POST["CopyPassword"]) {
        CreateErrorText("两次输入的密码不同");
        echo "<br />";
    } else {
        if (!CheckPassword($_POST["Password"])) {
            CreateErrorText("密码不符合要求");
            CreateLink("PasswordRequirement.php", "密码设置要求");
        } else {
            $DatabaseQuery = $Database->prepare("UPDATE UserList SET Password=? WHERE UID=?");
            $TempPassword = EncodePassword($_POST["Password"]);
            $DatabaseQuery->bind_param("si", $TempPassword, $_SESSION["UID"]);
            $DatabaseQuery->execute();
            CreateSuccessText("更改成功");
            echo "<br />";
        }
    }
}
echo "<h4>更改绑定的邮箱（绑定邮箱后可以使用邮箱+密码登录）</h4>";
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
CreateText("新邮箱");
echo "<input class=\"Input\" type=\"text\" required name=\"NewEmail\" pattern=\".+@.+\\..+\" value=\"" . $_SESSION["UserEmail"] . "\" required />";
echo "<br />";
CreateText("图片验证码");
echo "<input class=\"Input\" type=\"text\" name=\"AuthCode\" required />";
echo "<img src=\"GetCaptcha.php\" style=\"position: relative; top: 10px;\" class=\"AuthCodePic\" id=\"AuthCodePic\"></img>";
echo "<br />";
echo "<input class=\"WarningButton\" type=\"submit\" name=\"ChangeEmail\" value=\"发送更改请求\" />";
echo "</form>";
if (isset($_POST["ChangeEmail"]) && isset($_POST["NewEmail"]) && isset($_POST["AuthCode"])) {
    if (strcasecmp($_SESSION["AuthCode"], $_POST["AuthCode"]) != 0) {
        CreateErrorText("图片验证码错误");
        echo "<br />";
    } else if (preg_match("/.+@.+\\..+/", $_POST["NewEmail"]) == false) {
        CreateErrorText("邮箱不合法");
        echo "<br />";
    } else {
        $Email = $_POST["NewEmail"];
        $_SESSION["AuthCode"] = rand();
        $DatabaseQuery = $Database->prepare("SELECT UID FROM UserList WHERE UserEmail=?");
        $DatabaseQuery->bind_param("s", $_POST["NewEmail"]);
        $DatabaseQuery->execute();
        if ($DatabaseQuery->get_result()->num_rows != 0) {
            CreateErrorText("该邮箱已被绑定");
            echo "<br />";
        } else if (isset($_SESSION["LastSendMail"]) && time() - $_SESSION["LastSendMail"] <= 120) {
            CreateErrorText("请" . ($_SESSION["LastSendMail"] + 120 - time()) . "秒后再申请发送邮件");
            echo "<br />";
        } else {
            $Mailer = InitMailer();
            try {
                $Mailer->addAddress($Email, "");
                $Mailer->Subject = "[建平西校初二23班] 邮箱确认";
                $_SESSION["Email"] = $Email;
                $_SESSION["RandomToken"] = random_bytes(64);
                $CurrentTime = time();
                $ActiveURL = "https://" . $_SERVER["HTTP_HOST"] . "/ConfirmEmail.php?Email=" . bin2hex($Email) . "&Time=" . $CurrentTime . "&Sign=" . md5($Email . $CurrentTime . $_SESSION["RandomToken"]) . "&RandomToken=" . bin2hex($_SESSION["RandomToken"]);
                $Mailer->Body = "<center><p style=\"font-size: 20px;\" ><b>欢迎绑定邮箱！</b></p><p>现在您可以点击下方按钮激活您的邮箱</p><a style=\"background-color: rgb(0,128,255);color:white;\" href=\"" . $ActiveURL . "\">激活您的邮箱</a><br><br><div style=\"font-color:rgb(128,128,128);font-size:10px;\">链接点不了？您也可以直接把 " . $ActiveURL . " 粘贴到您的浏览器中以激活您的邮箱。</div><br><div style=\"width:100%;background-color:rgb(0,0,0);color:rgb(255,255,255);\">Developed by Langning Chen. All rights reserved.</div></center>";
                $Mailer->AltBody = "请把 " . $ActiveURL . " 粘贴到您的浏览器中以激活您的邮箱。";
                $Mailer->send();
                StartMailTiming();
                CreateSuccessText("验证邮件发送成功，请检查收件箱和<b>垃圾邮件箱</b>");
                echo "<br />";
            } catch (Exception $Error) {
                CreateErrorText("验证邮件发送失败");
                echo "<br />";
            }
        }
    }
}
require_once "Footer.php";
