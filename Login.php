<?php
require_once "Function.php";
$UserHandle = $Password = "";
$RedirectURI = "/index.php";
if (isset($_GET["RedirectURI"])) {
    $RedirectURI = $_GET["RedirectURI"];
}
if (isset($_POST["UserHandle"])) {
    $UserHandle = $_POST["UserHandle"];
}
if (isset($_POST["Password"])) {
    $Password = $_POST["Password"];
}
if (isset($_SESSION["UID"]) && isset($_SESSION["UserName"]) && isset($_SESSION["Password"]) && isset($_SESSION["UserType"]) && isset($_SESSION["UserEmail"])) {
    echo  "<script>window.location = \"" . $RedirectURI . "\";</script>";
    die();
}
require_once "Header.php";
echo "<div class=\"LoginForm\">";
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
echo "<div class=\"LoginInput\">";
CreateText("&emsp;&emsp;登录名");
echo "<input class=\"Input\" type=\"text\" value=\"$UserHandle\" name=\"UserHandle\" />";
echo "</div>";
echo "<div class=\"LoginInput\">";
CreateText("&emsp;&emsp;密&emsp;码");
echo "<input class=\"Input\" type=\"password\" name=\"Password\" />";
echo "</div>";
echo "<div class=\"LoginInput\">";
CreateText("图片验证码");
echo "<input class=\"Input AuthCodeInput\" type=\"text\" name=\"AuthCode\" />";
echo "<img src=\"GetCaptcha.php\" style=\"position: relative; top: 10px;\" class=\"AuthCodePic\" id=\"AuthCodePic\"></img>";
echo "</div>";
echo "<br />";
echo "<input type=\"checkbox\" checked=\"checked\" name=\"AutoLogin\" />";
CreateText("7天内自动登录");
echo "<input class=\"MainButton\" type=\"submit\" value=\"确定\" />";
echo "</form>";
if (
    isset($_COOKIE["UID"]) &&
    isset($_COOKIE["UserName"]) &&
    isset($_COOKIE["Password"]) &&
    isset($_COOKIE["UserType"]) &&
    isset($_COOKIE["UserEmail"])
) {
    $DatabaseQuery = $Database->prepare("SELECT UID FROM UserList WHERE UID=? AND UserName=? AND Password=? AND UserType=? AND UserEmail=?");
    $DatabaseQuery->bind_param("issis", $_COOKIE["UID"], $_COOKIE["UserName"], $_COOKIE["Password"], $_COOKIE["UserType"], $_COOKIE["UserEmail"]);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    if ($Result->num_rows == 1) {
        $_SESSION["UID"] = $_COOKIE["UID"];
        $_SESSION["UserName"] = $_COOKIE["UserName"];
        $_SESSION["Password"] = $_COOKIE["Password"];
        $_SESSION["UserType"] = $_COOKIE["UserType"];
        $_SESSION["UserEmail"] = $_COOKIE["UserEmail"];
        $DatabaseQuery = $Database->prepare("UPDATE UserList SET LastLoginTime=current_timestamp() WHERE UID=?");
        $DatabaseQuery->bind_param("i", $_SESSION["UID"]);
        $DatabaseQuery->execute();
        echo "<script>window.location = \"" . $RedirectURI . "\";</script>";
        die();
    } else {
        setcookie("UID", "", time() - 1);
        setcookie("UserName", "", time() - 1);
        setcookie("Password", "", time() - 1);
        setcookie("UserType", "", time() - 1);
        setcookie("UserEmail", "", time() - 1);
    }
    $Result->close();
} else if (isset($_SESSION["AuthCode"]) && isset($_POST["AuthCode"])) {
    if (strcasecmp($_SESSION["AuthCode"], $_POST["AuthCode"]) != 0) {
        CreateErrorText("图片验证码错误");
        echo "<br />";
    } else if ($UserHandle != "" && $Password != "") {
        $_SESSION["AuthCode"] = rand();
        $DatabaseQuery = $Database->prepare("SELECT UID,UserName,UserType,UserEmail FROM UserList WHERE (UserName=? OR UserEmail=?) AND Password=?");
        $Password = EncodePassword($Password);
        $DatabaseQuery->bind_param("sss", $UserHandle, $UserHandle, $Password);
        $DatabaseQuery->execute();
        $Result = $DatabaseQuery->get_result();
        if ($Result->num_rows == 0) {
            CreateErrorText("用户名或密码错误");
            echo "<br />";
        } else if ($Result->num_rows == 1) {
            $Result->data_seek(0);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            $_SESSION["UID"] = $RowData[0];
            $_SESSION["UserName"] = $RowData[1];
            $_SESSION["Password"] = $Password;
            $_SESSION["UserType"] = $RowData[2];
            $_SESSION["UserEmail"] = $RowData[3];
            if (isset($_POST["AutoLogin"])) {
                setcookie("UID", $RowData[0], time() + 60 * 60 * 24 * 7);
                setcookie("UserName", $RowData[1], time() + 60 * 60 * 24 * 7);
                setcookie("Password", $Password, time() + 60 * 60 * 24 * 7);
                setcookie("UserType", $RowData[2], time() + 60 * 60 * 24 * 7);
                setcookie("UserEmail", $RowData[3], time() + 60 * 60 * 24 * 7);
            } else {
                setcookie("UID", "", time() - 1);
                setcookie("UserName", "", time() - 1);
                setcookie("Password", "", time() - 1);
                setcookie("UserType", "", time() - 1);
                setcookie("UserEmail", "", time() - 1);
            }
            $DatabaseQuery = $Database->prepare("UPDATE UserList SET LastLoginTime=current_timestamp() WHERE UID=?");
            $DatabaseQuery->bind_param("i", $_SESSION["UID"]);
            $DatabaseQuery->execute();
            echo "<script>window.location = \"" . $RedirectURI . "\";</script>";
            die();
        } else {
            CreateErrorText("发生系统错误：有重复用户");
            echo "<br />";
        }
        $Result->close();
    } else {
        CreateErrorText("请填写完整");
        echo "<br />";
    }
}
echo "<div class=\"Tip\">";
CreateLink("GetTempPassword.php", "获取初始密码");
CreateLink("ForgotPassword.php", "忘记密码");
echo "</div>";
echo "</div>";
echo "<script>";
echo "document.getElementById(\"AuthCodePic\").onclick = function () { this.src = \"GetCaptcha.php?r=\" + Math.random(); }";
echo "</script>";
require_once "Footer.php";
