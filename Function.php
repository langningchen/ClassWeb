<?php
session_start();
require_once "DatabaseAndSecrets.php";
$Database = new mysqli($DataBaseHostName, $DataBaseUserName, $DataBasePassWord, "");
if ($Database->connect_error) {
    CreateText("无法连接到数据库！");
    require_once "Footer.php";
    die();
}
$Database->query("USE " . $DataBaseName);
function SanitizeString($String): string
{
    $String = stripslashes($String);
    $String = htmlentities($String);
    return $String;
}
function GetUserName(int $UID): string
{
    $ReturnValue = "<a class=\"";
    global $Database;
    $DatabaseQuery = $Database->prepare("SELECT UserType FROM UserList WHERE UID=?");
    $DatabaseQuery->bind_param("i", $UID);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    if ($Result->num_rows == 1) {
        $Result->data_seek(0);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        if ($RowData[0] == "0")
            $ReturnValue .= "OriginalUser";
        else if ($RowData[0] == "1")
            $ReturnValue .= "TeacherUser";
        else if ($RowData[0] == "2")
            $ReturnValue .= "AdminUser";
        else if ($RowData[0] == "3")
            $ReturnValue .= "BadUser";
    }
    $ReturnValue .= "\" href=\"User.php?UID=" . $UID . "\"><i>@" . GetNoLinkUserName($UID) . "</i></a> ";
    return $ReturnValue;
}
function GetNoLinkUserName(int $UID): string
{
    global $Database;
    $DatabaseQuery = $Database->prepare("SELECT UserName FROM UserList WHERE UID=?");
    $DatabaseQuery->bind_param("i", $UID);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    if ($Result->num_rows != 1) {
        return "没有该用户";
    }
    $Result->data_seek(0);
    $RowData = $Result->fetch_array(MYSQLI_NUM);
    return $RowData[0];
}
function GetFriends(): array
{
    global $Database;
    $AnsArray = array();
    if ($_SESSION["UID"] != 1) {
        array_push($AnsArray, "1");
        $DatabaseQuery = $Database->prepare("SELECT ClassAdmin,ClassTeacher,ClassMember FROM ClassList");
        $DatabaseQuery->execute();
        $Result = $DatabaseQuery->get_result();
        for ($i = 0; $i < $Result->num_rows; $i++) {
            $Result->data_seek($i);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            if (!in_array($RowData[0], $AnsArray)) {
                array_push($AnsArray, $RowData[0]);
            }
            $TempArray = mb_split(",", $RowData[1]);
            for ($i = 0; $i < count($TempArray); $i++) {
                if ($TempArray[$i] == "") continue;
                if (!in_array($TempArray[$i], $AnsArray)) {
                    array_push($AnsArray, $TempArray[$i]);
                }
            }
            if ($_SESSION["UserType"] != 0) {
                $TempArray = mb_split(",", $RowData[2]);
                for ($i = 0; $i < count($TempArray); $i++) {
                    if ($TempArray[$i] == "") continue;
                    if (!in_array($TempArray[$i], $AnsArray)) {
                        array_push($AnsArray, $TempArray[$i]);
                    }
                }
            }
        }
    } else {
        $DatabaseQuery = $Database->prepare("SELECT UID FROM UserList WHERE UID!=?");
        $DatabaseQuery->bind_param("i", $_SESSION["UID"]);
        $DatabaseQuery->execute();
        $Result = $DatabaseQuery->get_result();
        for ($i = 0; $i < $Result->num_rows; $i++) {
            $Result->data_seek($i);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            array_push($AnsArray, $RowData[0]);
        }
    }
    sort($AnsArray);
    return $AnsArray;
}
function GetClassType(int $ClassID): string
{
    global $Database;
    $DatabaseQuery = $Database->prepare("SELECT * FROM ClassList WHERE ClassID=?");
    $DatabaseQuery->bind_param("i", $ClassID);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    if ($Result->num_rows != 1) {
        return "";
    }
    if ($_SESSION["UserType"] == 2) {
        return "超级管理员";
    }
    $Result->data_seek(0);
    $RowData = $Result->fetch_array(MYSQLI_NUM);
    $Member = "";
    if ($RowData[2] == $_SESSION["UID"]) {
        $Member = "管理员";
    } else if (in_array($_SESSION["UID"], mb_split(",", $RowData[3]))) {
        $Member = "教师";
    } else if (in_array($_SESSION["UID"], mb_split(",", $RowData[4]))) {
        $Member = "学生";
    }
    return $Member;
}
function CreateText(string $Value)
{
    echo "<span class=\"Text\">" . $Value . "</span>";
}
function CreateErrorText(string $Value): void
{
    echo "<span class=\"ErrorText\"><i>" . $Value . "</i></span>";
}
function CreateSuccessText(string $Value): void
{
    echo "<span class=\"SuccessText\"><i>" . $Value . "</i></span>";
}
function CreateLink(string $Href, string $Value): void
{
    echo "<a target=\"_blank\" href=\"" . $Href . "\" class=\"Link\">" . $Value . "</a>";
}
function CreateDownload(string $File, string $FileName, string $Value = "下载"): void
{
    $File = bin2hex($File);
    $FileName = bin2hex($FileName);
    $Time = date("U");
    $Sign = md5($File . $Time . $FileName . $_SESSION["UID"] . $_SESSION["UserName"]);
    echo "<input class=\"SecondButton\" type=\"button\" onclick=\"window.open('Download.php?File=" . $File . "&Time=" . $Time . "&FileName=" . $FileName . "&Sign=" . $Sign . "')\" value=\"" . $Value . "\" />";
}
function GetHomeworkStatusName(int $Status): string
{
    switch ($Status) {
        case 0:
            return "未提交";
        case 1:
            return "提交未批改";
        case 2:
            return "需订正";
        case 3:
            return "订正未批改";
        case 4:
            return "通过";
        case 5:
            return "优秀";
            break;
    }
}
function GetClockInStatusName(int $Status): string
{
    switch ($Status) {
        case 0:
            return "未打卡";
        case 1:
            return "已打卡";
        case 2:
            return "优秀";
            break;
    }
}
function DeleteClockIn(int $ClockInID): void
{
    global $Database;
    $DatabaseQuery = $Database->prepare("DELETE FROM ClockInList WHERE ClockInID=?");
    $DatabaseQuery->bind_param("i", $ClockInID);
    $DatabaseQuery->execute();

    $DatabaseQuery = $Database->prepare("SELECT ClockInUploadID FROM ClockInUploadList WHERE ClockInID=?");
    $DatabaseQuery->bind_param("i", $ClockInID);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    for ($i = 0; $i < $Result->num_rows; $i++) {
        $Result->data_seek($i);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        $DeleteTemp = $Database->prepare("DELETE FROM ClockInUploadCheckList WHERE ClockInUploadID=?");
        $DeleteTemp->bind_param("i", $RowData[0]);
        $DeleteTemp->execute();
    }
    $DatabaseQuery = $Database->prepare("DELETE FROM ClockInUploadList WHERE ClockInID=?");
    $DatabaseQuery->bind_param("i", $ClockInID);
    $DatabaseQuery->execute();

    $DatabaseQuery = $Database->prepare("SELECT ClockInUploadFileID, UploadUID, FileName FROM ClockInUploadFileList WHERE ClockInID=?");
    $DatabaseQuery->bind_param("i", $ClockInID);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    for ($i = 0; $i < $Result->num_rows; $i++) {
        $Result->data_seek($i);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        if (file_exists("ClockInUploadFile/" . $Result[0] . "_" . $ClockInID . "_" . $Result[1] . "_" . $RowData[2]))
            unlink("ClockInUploadFile/" . $Result[0] . "_" . $Result[1] . "_" . $Result[2] . "_" . $RowData[3]);
    }
    $DatabaseQuery = $Database->prepare("DELETE FROM ClockInUploadFileList WHERE ClockInID=?");
    $DatabaseQuery->bind_param("i", $ClockInID);
    $DatabaseQuery->execute();
}
function DeleteHomework(int $HomeworkID): void
{
    global $Database;
    $DatabaseQuery = $Database->prepare("DELETE FROM HomeworkList WHERE HomeworkID=?");
    $DatabaseQuery->bind_param("i", $HomeworkID);
    $DatabaseQuery->execute();

    $DatabaseQuery = $Database->prepare("SELECT HomeworkUploadID FROM HomeworkUploadList WHERE HomeworkID=?");
    $DatabaseQuery->bind_param("i", $HomeworkID);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    for ($i = 0; $i < $Result->num_rows; $i++) {
        $Result->data_seek($i);
        $RowData = $Result->fetch_array(MYSQLI_NUM);

        $DeleteTemp = $Database->prepare("SELECT HomeworkUploadCheckID,FileName FROM HomeworkUploadCheckList WHERE HomeworkUploadID=?");
        $DeleteTemp->bind_param("i", $RowData[0]);
        $DeleteTemp->execute();
        $DeleteResult = $DeleteTemp->get_result();
        for ($j = 0; $j < $Result->num_rows; $j++) {
            $DeleteResult->data_seek($j);
            $DeleteRowData = $DeleteResult->fetch_array(MYSQLI_NUM);
            if (file_exists("HomeworkUploadCheckFile/" . $DeleteRowData[0] . "_" . $DeleteRowData[1]))
                unlink("HomeworkUploadCheckFile/" . $DeleteRowData[0] . "_" . $DeleteRowData[1]);
        }
        $DeleteTemp = $Database->prepare("DELETE FROM HomeworkUploadCheckList WHERE HomeworkUploadID=?");
        $DeleteTemp->bind_param("i", $RowData[0]);
        $DeleteTemp->execute();

        $DeleteTemp = $Database->prepare("DELETE FROM HomeworkUploadCheckList WHERE HomeworkUploadID=?");
        $DeleteTemp->bind_param("i", $RowData[0]);
        $DeleteTemp->execute();
    }
    $DatabaseQuery = $Database->prepare("DELETE FROM HomeworkUploadList WHERE HomeworkID=?");
    $DatabaseQuery->bind_param("i", $HomeworkID);
    $DatabaseQuery->execute();

    $DatabaseQuery = $Database->prepare("SELECT HomeworkUploadFileID, UploadUID, FileName FROM HomeworkUploadFileList WHERE HomeworkID=?");
    $DatabaseQuery->bind_param("i", $HomeworkID);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    for ($i = 0; $i < $Result->num_rows; $i++) {
        $Result->data_seek($i);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        if (file_exists("HomeworkUploadFile/" . $RowData[0] . "_" . $HomeworkID . "_" . $RowData[1] . "_" . $RowData[2]))
            unlink("HomeworkUploadFile/" . $RowData[0] . "_" . $HomeworkID . "_" . $RowData[1] . "_" . $RowData[2]);
    }
    $DatabaseQuery = $Database->prepare("DELETE FROM HomeworkUploadFileList WHERE HomeworkID=?");
    $DatabaseQuery->bind_param("i", $HomeworkID);
    $DatabaseQuery->execute();
}
function CreateRandPassword(): string
{
    $RandPassword = "";
    $CharSet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()-=[]\\;',./_+{}|:\"<>?";
    for ($i = 0; $i < 16; $i++) {
        $RandPassword .= substr($CharSet, rand(0, strlen($CharSet) - 1), 1);
    }
    return $RandPassword;
}
function AddMessage(int $UID, string $MessageData, string $URL): void
{
    global $Database;
    $DatabaseQuery = $Database->prepare("INSERT INTO NewMessageList(UID, Data, URL, Time) VALUES (?, ?, ?, current_timestamp())");
    $DatabaseQuery->bind_param("iss", $UID, $MessageData, $URL);
    $DatabaseQuery->execute();
}
function GetCaptcha(): string
{
    $image = imagecreatetruecolor(200, 60);
    imagefill($image, 0, 0, imagecolorallocate($image, rand(127, 255), rand(127, 255), rand(127, 255)));
    $_SESSION["AuthCode"] = "";
    $files = array();
    $dir = opendir($_SERVER["DOCUMENT_ROOT"] . "/Fonts");
    while ($file = readdir($dir))
        if ($file != ".." && $file != ".")
            $files[] = $file;
    closedir($dir);
    $TTF = $_SERVER["DOCUMENT_ROOT"] . "/Fonts/" . $files[rand(0, count($files) - 1)];
    $ChineseTTF = $_SERVER["DOCUMENT_ROOT"] . "/Fonts/Dengb.ttf";
    $StringSet = "abcdefghjkmnpqrstuvwxyz23456789";
    switch (rand(0, 7)) {
        case 0:
            for ($i = 0; $i < 4; $i++) {
                $fontcontent = substr($StringSet, rand(0, strlen($StringSet) - 1), 1);
                $_SESSION["AuthCode"] .= $fontcontent;
                imagettftext($image, rand(20, 30), rand(-60, 60), ($i * 50) + rand(10, 30), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $TTF, $fontcontent);
            }
            break;
        case 1:
            $a = rand(1, 20);
            $b = rand(1, 20);
            $_SESSION["AuthCode"] = $a + $b;
            imagettftext($image, rand(20, 30), rand(-10, 10), 0   + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $TTF, $a);
            imagettftext($image, rand(20, 30), rand(-10, 10), 50  + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $ChineseTTF, "＋");
            imagettftext($image, rand(20, 30), rand(-10, 10), 100 + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $TTF, $b);
            imagettftext($image, rand(20, 30), rand(-10, 10), 150 + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $ChineseTTF, "＝");
            break;
        case 2:
            $a = rand(10, 20);
            $b = rand(1, 10);
            $_SESSION["AuthCode"] = $a - $b;
            imagettftext($image, rand(20, 30), rand(-10, 10), 0   + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $TTF, $a);
            imagettftext($image, rand(20, 30), rand(-10, 10), 50  + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $ChineseTTF, "－");
            imagettftext($image, rand(20, 30), rand(-10, 10), 100 + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $TTF, $b);
            imagettftext($image, rand(20, 30), rand(-10, 10), 150 + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $ChineseTTF, "＝");
            break;
        case 3:
            $a = rand(1, 10);
            $b = rand(1, 20);
            $_SESSION["AuthCode"] = $a * $b;
            imagettftext($image, rand(20, 30), rand(-10, 10), 0   + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $TTF, $a);
            imagettftext($image, rand(20, 30), rand(-10, 10), 50  + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $ChineseTTF, "×");
            imagettftext($image, rand(20, 30), rand(-10, 10), 100 + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $TTF, $b);
            imagettftext($image, rand(20, 30), rand(-10, 10), 150 + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $ChineseTTF, "＝");
            break;
        case 4:
            $b = rand(1, 10);
            $_SESSION["AuthCode"] = rand(1, 10);
            $a = $b * $_SESSION["AuthCode"];
            imagettftext($image, rand(20, 30), rand(-10, 10), 0   + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $TTF, $a);
            imagettftext($image, rand(20, 30), rand(-10, 10), 50  + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $ChineseTTF, "÷");
            imagettftext($image, rand(20, 30), rand(-10, 10), 100 + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $TTF, $b);
            imagettftext($image, rand(20, 30), rand(-10, 10), 150 + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $ChineseTTF, "＝");
            break;
        case 5:
            $a = rand(1, 9);
            $b = rand(1, 3);
            $_SESSION["AuthCode"] = 1;
            for ($i = 0; $i < $b; $i++)
                $_SESSION["AuthCode"] *= $a;
            imagettftext($image, rand(20, 30), rand(-10, 10), 0, rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $TTF, $a);
            imagettftext($image, rand(20, 30), rand(-10, 10), 30, rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $ChineseTTF, "的");
            imagettftext($image, rand(20, 30), rand(-10, 10), 80, rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $TTF, $b);
            imagettftext($image, rand(20, 30), rand(-10, 10), 110, rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $ChineseTTF, "次方");
            break;
        case 6:
            $a = rand(1, 9);
            $fontcontent = substr($StringSet, rand(0, strlen($StringSet) - 1), 1);
            for ($i = 0; $i < $a; $i++)
                $_SESSION["AuthCode"] .= $fontcontent;
            imagettftext($image, rand(20, 30), rand(-10, 10), 0   + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $ChineseTTF, "敲");
            imagettftext($image, rand(20, 30), rand(-10, 10), 50  + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $TTF, $a);
            imagettftext($image, rand(20, 30), rand(-10, 10), 100 + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $ChineseTTF, "个");
            imagettftext($image, rand(20, 30), rand(-10, 10), 150 + rand(10, 20), rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $TTF, $fontcontent);
            break;
        case 7:
            $a = rand(1, 9);
            $b = rand(1, 8);
            if ($b >= $a)
                $b++;
            for ($i = $a; ($a < $b && $i <= $b) || ($a > $b && $i >= $b); $i += ($a < $b ? 1 : -1))
                $_SESSION["AuthCode"] .= $i;
            imagettftext($image, rand(20, 30), rand(-10, 10), 0, rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $ChineseTTF, "从");
            imagettftext($image, rand(20, 30), rand(-10, 10), 50, rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $TTF, $a);
            imagettftext($image, rand(20, 30), rand(-10, 10), 80, rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $ChineseTTF, "数到");
            imagettftext($image, rand(20, 30), rand(-10, 10), 160, rand(30, 50), imagecolorallocate($image, rand(0, 127), rand(0, 127), rand(0, 127)), $TTF, $b);
            break;
    }
    for ($i = 0; $i < 200; $i++)
        imagesetpixel($image, rand(0, 200), rand(0, 60), imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255)));
    for ($i = 0; $i < 10; $i++)
        imageline($image, rand(0, 200), rand(0, 60), rand(0, 200), rand(0, 60), imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255)));
    $TempPictureName = rand() . ".png";
    imagepng($image, $TempPictureName);
    $Output = file_get_contents($TempPictureName);
    unlink($TempPictureName);
    return $Output;
}
function FlushOutput(): void
{
    ob_flush();
    flush();
}
function StringToRegex(string $Data): string
{
    $Data = str_replace("\\", "\\\\", $Data);
    $Data = str_replace(".", "\\.", $Data);
    $Data = str_replace("?", "\\?", $Data);
    $Data = str_replace("*", "\\*", $Data);
    $Data = str_replace("+", "\\+", $Data);
    $Data = str_replace("{", "\\{", $Data);
    $Data = str_replace("}", "\\}", $Data);
    $Data = str_replace("(", "\\(", $Data);
    $Data = str_replace(")", "\\)", $Data);
    $Data = str_replace("[", "\\[", $Data);
    $Data = str_replace("]", "\\]", $Data);
    $Data = str_replace("^", "\\^", $Data);
    $Data = str_replace("$", "\\$", $Data);
    $Data = str_replace("|", "\\|", $Data);
    $Data = str_replace("/", "\\/", $Data);
    return $Data;
}
function CheckPassword(string $password): bool
{
    $OK = true;
    if (
        !preg_match("/[0-9]+/", $password) ||
        !preg_match("/[a-z]+/", $password) ||
        !preg_match("/[A-Z]+/", $password) ||
        !preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/", $password) ||
        preg_match("/(.{2,})\\1/", $password) ||
        preg_match("/(.+)\\1{2,}/", $password) ||
        preg_match("/(19\d{2}|20[0-1]\d|202[0-2])( ||-|\/|\.)(((1[0-2]|0[1-9])( ||-|\/|\.)(0[1-9]|[1-2][0-9]|3[0-1]))|((1[0-2]|[1-9])( ||-|\/|\.)([1-9]|[1-2][0-9]|3[0-1])))/", $password) ||
        strlen($password) < 8 ||
        strlen($password) > 128
    )
        $OK = false;
    $WeekPassword = ["123456789012", "abcdefghijklmnopqrstuvwxyzab", "qwertyuiop", "asdfghjkl", "zxcvbnm", "!@#$%^&*()", "147258369"];
    for ($i = 0; $i < count($WeekPassword); $i++) {
        for ($j = 0; $j < strlen($WeekPassword[$i]) - 2; $j++) {
            if (
                preg_match("/" . StringToRegex(substr($WeekPassword[$i], $j, 3)) . "/", $password) ||
                preg_match("/" . StringToRegex(strrev(substr($WeekPassword[$i], $j, 3))) . "/", $password) ||
                preg_match(
                    "/" . StringToRegex($WeekPassword[$i][$j]) . "." .
                        StringToRegex($WeekPassword[$i][$j + 1]) . "." .
                        StringToRegex($WeekPassword[$i][$j + 2]) . "/",
                    $password
                ) ||
                preg_match(
                    "/" . StringToRegex($WeekPassword[$i][$j + 2]) . "." .
                        StringToRegex($WeekPassword[$i][$j + 1]) . "." .
                        StringToRegex($WeekPassword[$i][$j]) . "/",
                    $password
                )
            )
                $OK = false;
        }
    }
    return $OK;
}
function StartMailTiming(): void
{
    echo "<script>";
    echo "var Element = document.getElementsByClassName(\"ResendEmailButton\")[0];";
    echo "Element.className = \"BadButton\";";
    echo "var TimeOut = 120;";
    echo "var IntervalID = setInterval(function() {";
    echo "    TimeOut--;";
    echo "    if (TimeOut < 0) {";
    echo "        Element.className = \"SecondButton\";";
    echo "        Element.value = \"重新发送动态验证码\";";
    echo "        clearInterval(IntervalID);";
    echo "    }";
    echo "    else";
    echo "        Element.value = \"重新发送动态验证码（\" + TimeOut + \"）\";";
    echo "}, 1000);";
    echo "</script>";
    $_SESSION["LastSendMail"] = time();
}
function IsPicture(string $FileName): bool
{
    return (strpos($FileName, "jpg") === false &&
        strpos($FileName, "jpeg") === false &&
        strpos($FileName, "png") === false &&
        strpos($FileName, "ico") === false &&
        strpos($FileName, "bmp") === false &&
        strpos($FileName, "gif") === false &&
        strpos($FileName, "webp") === false
    );
}
function ErrorHandler($ErrorNumber, $ErrorString, $ErrorFile, $ErrorLine): bool
{
    global $DebugMode;
    $ErrorName = array();
    $ErrorName[E_WARNING] = "运行时警告";
    $ErrorName[E_NOTICE] = "运行时通知";
    $ErrorName[E_CORE_ERROR] = "初始启动期间发生的致命错误";
    $ErrorName[E_CORE_WARNING] = "初始启动期间发生的警告";
    $ErrorName[E_USER_ERROR] = "用户生成的错误消息";
    $ErrorName[E_USER_WARNING] = "用户生成的警告消息";
    $ErrorName[E_USER_NOTICE] = "用户生成的通知消息";
    $ErrorName[E_STRICT] = "运行时产生的提醒信息";
    $ErrorName[E_RECOVERABLE_ERROR] = "可恢复的致命错误";
    if ($DebugMode) {
        CreateErrorText($ErrorName[$ErrorNumber] . "：" . $ErrorString);
        echo "<br />";
        echo "<br />";
        CreateText("错误文件：" . $ErrorFile . ":" . $ErrorLine);
        echo "<br />";
        CreateText("错误堆栈：");
        echo "<br />";
        echo "<pre>";
        print_r(debug_backtrace());
        echo "</pre>";
    } else {
        CreateErrorText("系统出现了" . $ErrorName[$ErrorNumber]);
        echo "<br />";
        echo "<br />";
        global $Database;
        $DatabaseQuery = $Database->prepare("SELECT ErrorLogID FROM ErrorLog WHERE ErrorString=? AND ErrorFile=? AND ErrorLine=? AND ErrorIP=?");
        $DatabaseQuery->bind_param("ssss", $ErrorString, $ErrorFile, $ErrorLine, $_SERVER["REMOTE_ADDR"]);
        $DatabaseQuery->execute();
        $Result = $DatabaseQuery->get_result();
        $ErrorID = 0;
        if ($Result->num_rows != 0) {
            $Result->data_seek(0);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            CreateSuccessText("相同的内容在之前已经被记录");
            echo "<br />";
            echo "<br />";
            CreateText("请不要重复提交同样的内容以避免浪费服务器资源并且造成不必要的麻烦");
            $ErrorID = $RowData[0];
        } else {
            $BackTrace = json_encode(debug_backtrace());
            $RecordUID = isset($_SESSION["UID"]) ? $_SESSION["UID"] : 0;
            $ErrorType = $ErrorName[$ErrorNumber];
            $DatabaseQuery = $Database->prepare("INSERT INTO ErrorLog (ErrorType, ErrorString, ErrorFile, ErrorLine, ErrorContext, ErrorTime, ErrorUID, ErrorIP, ErrorURI) VALUES (?, ?, ?, ?, ?, current_timestamp(), ?, ?, ?)");
            $DatabaseQuery->bind_param("ssssssss", $ErrorType, $ErrorString, $ErrorFile, $ErrorLine, $BackTrace, $RecordUID, $_SERVER["REMOTE_ADDR"], $_SERVER["REQUEST_URI"]);
            $DatabaseQuery->execute();
            CreateSuccessText("该内容已被记录，管理员会在看到后第一时间处理");
            $ErrorID = $DatabaseQuery->insert_id;
        }
        echo "<br />";
        echo "<br />";
        CreateText("您也可以");
        CreateLink("mailto:langningc2009.ml@outlook.com?subject=请修复错误&body=错误编号：" . $ErrorID . "%0D%0A错误类型：" . $ErrorName[$ErrorNumber] . "%0D%0A错误网址：" . $_SERVER["REQUEST_URI"], "点击这里");
        CreateText("提醒管理员修复此信息/警告/错误");
    }
    echo "<br />";
    echo "<br />";
    return true;
}
set_error_handler('ErrorHandler');
