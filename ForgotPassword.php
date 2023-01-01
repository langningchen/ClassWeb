<?php
require_once "Header.php";
$UserName = $Email = $CorrectEmail = $AuthCode = $RandomToken = $CorrectRandomToken = $CanSetPassword = $NewPassword = $ConfirmPassword = "";
if (isset($_SESSION["UserName"])) $UserName = $_SESSION["UserName"];
if (isset($_POST["UserName"])) $UserName = $_POST["UserName"];
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
CreateText("请输入您的用户名");
echo "<input name=\"UserName\" value=\"" . $UserName . "\" class=\"Input\" />";
echo "<input class=\"SecondButton\" name=\"Step1\" type=\"submit\" value=\"确定\" />";
echo "</form>";
if (
    isset($_POST["Step1"]) &&
    $UserName != ""
) {
    $_SESSION["UserName"] = $UserName;
    $DatabaseQuery = $Database->prepare("SELECT UserEmail FROM UserList WHERE UserName=?");
    $DatabaseQuery->bind_param("s", $UserName);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    if ($Result->num_rows == 0) {
        CreateErrorText("没有此用户或者该用户没有绑定邮箱");
        $_SESSION["Email"] =
            $_SESSION["CorrectEmail"] =
            $_SESSION["RandomToken"] =
            $_SESSION["CorrectRandomToken"] =
            $_SESSION["CanSetPassword"] =
            $_SESSION["NewPassword"] =
            $_SESSION["ConfirmPassword"] = "";
    } else {
        $Result->data_seek(0);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        if ($RowData[0] == "N/A") {
            CreateErrorText("没有此用户或者该用户没有绑定邮箱");
            $_SESSION["Email"] =
                $_SESSION["CorrectEmail"] =
                $_SESSION["RandomToken"] =
                $_SESSION["CorrectRandomToken"] =
                $_SESSION["CanSetPassword"] =
                $_SESSION["NewPassword"] =
                $_SESSION["ConfirmPassword"] = "";
        } else {
            $_SESSION["CorrectEmail"] = $CorrectEmail = $RowData[0];
        }
    }
}
if (isset($_SESSION["CorrectEmail"])) $CorrectEmail = $_SESSION["CorrectEmail"];
if (isset($_SESSION["Email"])) $Email = $_SESSION["Email"];
if (isset($_POST["Email"])) $Email = $_POST["Email"];
if (isset($_POST["AuthCode"])) $AuthCode = $_POST["AuthCode"];
if ($CorrectEmail != "") {
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    CreateText("请验证您的邮箱地址");
    echo "<input name=\"Email\" class=\"Input\" placeholder=\"" .
        substr($CorrectEmail, 0, 2) . "***" . substr($CorrectEmail, strpos($CorrectEmail, "@") - 2, 2) .
        "@***.***\" value=\"" . $Email . "\" />";
    echo "<br />";
    CreateText("请输入图片验证码");
    echo "<input class=\"Input\" type=\"text\" name=\"AuthCode\" required />";
    echo "<img src=\"GetCaptcha.php\" style=\"position: relative; top: 10px;\" class=\"AuthCodePic\" id=\"AuthCodePic\"></img>";
    echo "<input class=\"SecondButton ResendEmailButton\" name=\"Step2\" type=\"submit\" value=\"发送动态验证码\" />";
    echo "</form>";
}
if (
    isset($_POST["Step2"]) &&
    $UserName != "" &&
    $Email != "" &&
    $CorrectEmail != "" &&
    $AuthCode != ""
) {
    $_SESSION["Email"] = $Email;
    if ($Email != $CorrectEmail) {
        CreateErrorText("邮箱不匹配");
        $_SESSION["RandomToken"] =
            $_SESSION["CorrectRandomToken"] =
            $_SESSION["CanSetPassword"] =
            $_SESSION["NewPassword"] =
            $_SESSION["ConfirmPassword"] = "";
    } else if (strcasecmp($_SESSION["AuthCode"], $AuthCode) != 0) {
        CreateErrorText("图片验证码错误");
        $_SESSION["RandomToken"] =
            $_SESSION["CorrectRandomToken"] =
            $_SESSION["CanSetPassword"] =
            $_SESSION["NewPassword"] =
            $_SESSION["ConfirmPassword"] = "";
    } else if (isset($_SESSION["LastSendMail"]) && time() - $_SESSION["LastSendMail"] <= 120) {
        CreateErrorText("请" . ($_SESSION["LastSendMail"] + 120 - time()) . "秒后再申请发送邮件");
        echo "<br />";
    } else {
        $_SESSION["AuthCode"] = rand();
        $Mailer = InitMailer();
        try {
            $Mailer->addAddress($_POST["Email"], "");
            $Mailer->Subject = "[建平西校初二23班] 找回密码";
            $_SESSION["CorrectRandomToken"] = $CorrectRandomToken = random_int(1000, 999999);
            $Mailer->Body = "<center><p style=\"font-size: 20px;\"><b>忘记密码？不用担心</b></p><p>您的密码找回动态验证码是</p><p style=\"font-size: 50px;\">" . $CorrectRandomToken . "</p></center>";
            $Mailer->AltBody = "您的密码找回动态验证码是" . $CorrectRandomToken;
            $Mailer->send();
            StartMailTiming();
        } catch (Exception $Error) {
            CreateErrorText("邮件发送失败");
            $_SESSION["RandomToken"] =
                $_SESSION["CorrectRandomToken"] =
                $_SESSION["CanSetPassword"] =
                $_SESSION["NewPassword"] =
                $_SESSION["ConfirmPassword"] = "";
        }
    }
}
if (isset($_SESSION["CorrectRandomToken"])) $CorrectRandomToken = $_SESSION["CorrectRandomToken"];
if (isset($_SESSION["RandomToken"])) $RandomToken = $_SESSION["RandomToken"];
if (isset($_POST["RandomToken"])) $RandomToken = $_POST["RandomToken"];
if ($CorrectRandomToken != "") {
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    CreateText("请输入动态验证码");
    echo "<input name=\"RandomToken\" value=\"" . $RandomToken . "\" class=\"Input\" />";
    echo "<input class=\"SecondButton\" name=\"Step3\" type=\"submit\" value=\"验证\" />";
    echo "</form>";
}
if (
    isset($_POST["Step3"]) &&
    $UserName != "" &&
    $Email != "" &&
    $CorrectEmail != "" &&
    $RandomToken != "" &&
    $CorrectRandomToken != ""
) {
    $_SESSION["RandomToken"] = $RandomToken;
    if ($RandomToken != $CorrectRandomToken) {
        CreateErrorText("动态验证码错误");
        $_SESSION["CanSetPassword"] =
            $_SESSION["NewPassword"] =
            $_SESSION["ConfirmPassword"] = "";
    } else {
        $_SESSION["CanSetPassword"] = $CanSetPassword = "1";
    }
}
if (isset($_SESSION["CanSetPassword"])) $CanSetPassword = $_SESSION["CanSetPassword"];
if (isset($_SESSION["NewPassword"])) $NewPassword = $_SESSION["NewPassword"];
if (isset($_POST["NewPassword"])) $NewPassword = $_POST["NewPassword"];
if (isset($_SESSION["ConfirmPassword"])) $ConfirmPassword = $_SESSION["ConfirmPassword"];
if (isset($_POST["ConfirmPassword"])) $ConfirmPassword = $_POST["ConfirmPassword"];
if ($CanSetPassword != "") {
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    CreateText("请输入新密码");
    echo "<input name=\"NewPassword\" class=\"Input\" value=\"" . $NewPassword . "\" type=\"password\" />";
    echo "<br />";
    CreateText("请确认新密码");
    echo "<input name=\"ConfirmPassword\" class=\"Input\" value=\"" . $ConfirmPassword . "\" type=\"password\" />";
    echo "<input class=\"SecondButton\" name=\"Step4\" type=\"submit\" value=\"确定\" />";
    echo "</form>";
}
if (
    isset($_POST["Step4"]) &&
    $UserName != "" &&
    $Email != "" &&
    $CorrectEmail != "" &&
    $RandomToken != "" &&
    $CorrectRandomToken != "" &&
    $CanSetPassword != "" &&
    $NewPassword != "" &&
    $ConfirmPassword != ""
) {
    $_SESSION["NewPassword"] = $NewPassword;
    $_SESSION["ConfirmPassword"] = $ConfirmPassword;
    if ($NewPassword != $ConfirmPassword) {
        CreateErrorText("密码不匹配");
    } else {
        if (!CheckPassword($NewPassword)) {
            CreateErrorText("密码不符合要求");
            CreateLink("PasswordRequirement.php", "密码设置要求");
        } else {
            $EncodedPassword = EncodePassword($NewPassword);
            $DatabaseQuery = $Database->prepare("UPDATE Userlist SET Password=? WHERE UserName=?");
            $DatabaseQuery->bind_param("ss", $EncodedPassword, $_SESSION["UserName"]);
            $DatabaseQuery->execute();
            CreateSuccessText("更改成功");
            $_SESSION["UserName"] =
                $_SESSION["Email"] =
                $_SESSION["CorrectEmail"] =
                $_SESSION["RandomToken"] =
                $_SESSION["CorrectRandomToken"] =
                $_SESSION["CanSetPassword"] =
                $_SESSION["NewPassword"] =
                $_SESSION["ConfirmPassword"] = "";
        }
    }
}
require_once "Footer.php";
