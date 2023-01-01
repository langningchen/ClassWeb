<?php
require_once "NotLogin.php";
require_once "Header.php";
if (!isset($_GET["ClockInID"])) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
$DatabaseQuery = $Database->prepare("SELECT * FROM ClockInList WHERE ClockInID=?");
$DatabaseQuery->bind_param("i", $_GET["ClockInID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
if ($Result->num_rows == 0) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
$Result->data_seek(0);
$RowData = $Result->fetch_array(MYSQLI_NUM);
$Member = GetClassType($RowData[1]);
if ($Member == "") {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
echo "<h4>" . $RowData[3] . "</h4>";
echo "<h4>起止日期</h4>";
CreateText($RowData[5] . "~" . $RowData[6]);
echo "<h4>布置人</h4>";
CreateText(GetUserName($RowData[2]));
echo "<h4>允许补打卡</h4>";
CreateText($RowData[7] ? "允许" : "不允许");
echo "<h4>内容</h4>";
CreateText($RowData[4]);
if ($RowData[2] == $_SESSION["UID"] || $_SESSION["UserType"] == 2) {
    echo "<br />";
    echo "<input type=\"button\" class=\"MainButton\" onclick=\"window.location='ViewClockIn.php?ClockInID=" . $RowData[0] . "'\" value=\"在线查看学生打卡\" />";
    echo "<br />";
    echo "<input class=\"SecondButton\" onclick=\"window.location='DownloadClockIn.php?ClockInID=" . $RowData[0] . "'\" type=\"submit\" value=\"下载学生打卡记录\" />";
    echo "<br />";
}
echo "<table>";
echo "<thead>";
echo "<td>";
CreateText("日期");
echo "</td>";
echo "<td>";
CreateText("时间");
echo "</td>";
echo "<td>";
CreateText("状态");
echo "</td>";
echo "<td>";
CreateText("操作");
echo "</td>";
echo "<td>";
CreateText("内容");
echo "</td>";
echo "<td>";
CreateText("评论");
echo "</td>";
echo "</thead>";
echo "<tbody>";
$Year = date("Y");
$Month = date("m");
$Day = date("d");
$Date = $Year . "-" . $Month . "-" . $Day;
if (strcmp($Date, $RowData[5]) < 0) {
    $Date = $RowData[5];
}
while (strcmp($Date, $RowData[5]) >= 0) {
    echo "<tr>";
    $DatabaseQuery = $Database->prepare("SELECT * FROM ClockInUploadList WHERE ClockInID=? and UploadUID=? and UploadDate=?");
    $DatabaseQuery->bind_param("iis", $RowData[0], $_SESSION["UID"], $Date);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    echo "<td>";
    CreateText($Date);
    echo "</td>";
    if ($Result->num_rows == 0) {
        echo "<td>";
        echo "</td>";
        echo "<td>";
        CreateText(GetClockInStatusName(0));
        echo "</td>";
        echo "<td>";
        if (strcmp($Date, date("Y-m-d")) != 0) {
            if ($RowData[7]) {
                echo "<input class=\"MainButton\" type=\"button\" onclick=\"window.location='UploadClockIn.php?ClockInID=" . $RowData[0] . "&UploadDate=" . $Date . "'\" value=\"补卡\" />";
            } else {
                echo "<input class=\"BadButton\" type=\"button\" disabled value=\"已错过\" />";
            }
        } else {
            echo "<input class=\"MainButton\" type=\"button\" onclick=\"window.location='UploadClockIn.php?ClockInID=" . $RowData[0] . "&UploadDate=" . $Date . "'\" value=\"打卡\" />";
        }
        echo "</td>";
        echo "<td>";
        echo "</td>";
        echo "<td>";
        echo "</td>";
    } else {
        $Result->data_seek(0);
        echo "<br />";
        $StatusRowData = $Result->fetch_array(MYSQLI_NUM);
        echo "<td>";
        CreateText($StatusRowData[6]);
        echo "</td>";
        echo "<td>";
        CreateText(GetClockInStatusName($StatusRowData[7]));
        echo "</td>";
        echo "<td>";
        echo "<input class=\"GoodButton\" type=\"button\" disabled value=\"已打卡\" />";
        echo "</td>";
        echo "<td>";
        if ($StatusRowData[3] != "") {
            CreateText($StatusRowData[3]);
            echo "<br />";
        }
        if ($StatusRowData[4] != "") {
            $TempArray = mb_split(",", $StatusRowData[4]);
            for ($j = 0; $j < count($TempArray); $j++) {
                if ($TempArray[$j] == "") continue;
                $FileIndex = $TempArray[$j];
                $DatabaseFileQuery = $Database->prepare("SELECT ClockInUploadFileID, ClockInID, UploadUID, FileName FROM ClockInUploadFileList WHERE ClockInUploadFileID=?");
                $DatabaseFileQuery->bind_param("i", $FileIndex);
                $DatabaseFileQuery->execute();
                $FileResult = $DatabaseFileQuery->get_result();
                if ($FileResult->num_rows == 1) {
                    $FileResult->data_seek(0);
                    $FileRowData = $FileResult->fetch_array(MYSQLI_NUM);
                    CreateDownload("ClockInUploadFile/" . $FileRowData[0] . "_" . $FileRowData[1] . "_" . $FileRowData[2] . "_" . $FileRowData[3], $FileRowData[3], "文件：" . $FileRowData[3]);
                    echo "<br />";
                } else {
                    echo "<input type=\"button\" class=\"BadButton\" value=\"系统错误：找不到此文件\" />";
                    echo "<br />";
                }
            }
        }
        echo "</td>";
        echo "<td>";
        $DatabaseQuery = $Database->prepare("SELECT * FROM ClockInUploadCheckList WHERE ClockInUploadID=? ORDER BY CheckTime DESC");
        $DatabaseQuery->bind_param("i", $StatusRowData[0]);
        $DatabaseQuery->execute();
        $Result = $DatabaseQuery->get_result();
        for ($i = 0; $i < $Result->num_rows; $i++) {
            $Result->data_seek($i);
            $CheckRowData = $Result->fetch_array(MYSQLI_NUM);
            echo GetUserName($CheckRowData[2]);
            CreateText($CheckRowData[3]);
            CreateText($CheckRowData[4]);
            echo "<br />";
        }
        echo "</td>";
    }
    echo "</td>";
    echo "</tr>";
    $Day--;
    if ($Day == 0) {
        $Month--;
        if ($Month == 1 || $Month == 3 || $Month == 5 || $Month == 7 || $Month == 8 || $Month == 10 || $Month == 12) $Day = 31;
        else if ($Month != 2) $Day = 30;
        else if ($Year % 4 == 0 && $Year % 100 != 0 && $Year % 1000 == 0) $Day = 29;
        else $Day = 28;
    }
    if ($Month == 0) {
        $Year--;
        $Month = 12;
    }
    $Day *= 1;
    $Month *= 1;
    $Year *= 1;
    $Date = $Year . "-";
    if ($Month < 10) {
        $Date .= "0";
    }
    $Date .= $Month . "-";
    if ($Day < 10) {
        $Date .= "0";
    }
    $Date .= $Day;
}
echo "</tbody>";
echo "</table>";
echo "<br />";
require_once "Footer.php";
