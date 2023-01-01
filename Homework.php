<?php
require_once "NotLogin.php";
require_once "Header.php";
if (!isset($_GET["HomeworkID"])) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
$DatabaseQuery = $Database->prepare("SELECT * FROM HomeworkList WHERE HomeworkID=?");
$DatabaseQuery->bind_param("i", $_GET["HomeworkID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
if ($Result->num_rows != 1) {
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
echo "<h4>布置人</h4>";
CreateText(GetUserName($RowData[2]));
echo "<h4>布置时间</h4>";
CreateText($RowData[5]);
echo "<h4>需要提交</h4>";
CreateText($RowData[7] ? "需要" : "不需要");
if ($RowData[7]) {
    echo "<h4>截止时间</h4>";
    CreateText($RowData[6]);
    echo "<h4>允许补交</h4>";
    CreateText($RowData[8] ? "允许" : "不允许");
}
echo "<h4>内容</h4>";
CreateText($RowData[4]);
if ($RowData[7]) {
    if ($RowData[2] == $_SESSION["UID"] || $_SESSION["UserType"] == 2) {
        echo "<br />";
        echo "<input type=\"button\" class=\"MainButton\" onclick=\"window.location='ViewHomework.php?HomeworkID=" . $RowData[0] . "'\" value=\"在线查看学生作业\" />";
        echo "<br />";
        echo "<input class=\"SecondButton\" onclick=\"window.location='DownloadHomework.php?HomeworkID=" . $RowData[0] . "'\" type=\"submit\" value=\"下载学生作业记录\" />";
        echo "<br />";
    }
    $DatabaseQuery = $Database->prepare("SELECT HomeworkUploadID, FileList, UploadTime, Status, Data FROM HomeworkUploadList WHERE HomeworkID=? and UploadUID=?");
    $DatabaseQuery->bind_param("ii", $RowData[0], $_SESSION["UID"]);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    if ($Result->num_rows == 0) {
        if (strcmp($RowData[6], date("Y-m-d") . " " . date("H:i:s")) >= 0) {
            echo "<input class=\"MainButton\" type=\"button\" onclick=\"window.location='UploadHomework.php?HomeworkID=" . $RowData[0] . "'\" value=\"提交\" />";
        } else if ($RowData[8]) {
            echo "<input class=\"WarningButton\" type=\"button\" onclick=\"window.location='UploadHomework.php?HomeworkID=" . $RowData[0] . "'\" value=\"补交\" />";
        } else {
            echo "<input class=\"BadButton\" type=\"button\" disabled value=\"已过截止时间\" />";
        }
        echo "<br />";
    } else {
        $Result->data_seek(0);
        $StatusRowData = $Result->fetch_array(MYSQLI_NUM);
        echo "<h4>状态</h4>";
        CreateText(GetHomeworkStatusName($StatusRowData[3]));
        echo "<br />";
        echo "<h4>提交时间</h4>";
        CreateText($StatusRowData[2]);
        echo "<h4>提交内容</h4>";
        CreateText($StatusRowData[4]);
        echo "<br />";
        $FileList = array();
        if ($StatusRowData[1] != "") {
            $TempArray = mb_split(",", $StatusRowData[1]);
            for ($j = 0; $j < count($TempArray); $j++) {
                if ($TempArray[$j] == "") continue;
                $FileIndex = $TempArray[$j];
                $DatabaseFileQuery = $Database->prepare("SELECT HomeworkUploadFileID, HomeworkID, UploadUID, FileName FROM HomeworkUploadFileList WHERE HomeworkUploadFileID=?");
                $DatabaseFileQuery->bind_param("i", $FileIndex);
                $DatabaseFileQuery->execute();
                $FileResult = $DatabaseFileQuery->get_result();
                if ($FileResult->num_rows == 1) {
                    $FileResult->data_seek(0);
                    $FileRowData = $FileResult->fetch_array(MYSQLI_NUM);
                    if (IsPicture($FileRowData[3]))
                        array_push($FileList, array("HomeworkUploadFile/" . $FileRowData[0] . "_" . $FileRowData[1] . "_" . $FileRowData[2] . "_", $FileRowData[3]));
                    else
                        echo "<img onclick=\"Draw(this, " . $StatusRowData[0] . ")\" class=\"HomeworkImage\" src=\"" . "HomeworkUploadFile/" . $FileRowData[0] . "_" . $FileRowData[1] . "_" . $FileRowData[2] . "_" . $FileRowData[3] . "\" />";
                } else
                    echo "<input type=\"button\" class=\"BadButton\" value=\"系统错误：找不到此文件\" />";
            }
        }
        for ($j = 0; $j < sizeof($FileList); $j++) {
            echo "<br />";
            CreateDownload($FileList[$j][0] . $FileList[$j][1], $FileList[$j][1], "文件：" . $FileList[$j][1]);
        }
        echo "<br />";
        if ($StatusRowData[3] == 1)
            echo "<input class=\"GoodButton\" type=\"button\" value=\"提交未批改\" />";
        else if ($StatusRowData[3] == 2)
            echo "<input class=\"WarningButton\" type=\"button\" onclick=\"window.location='UploadHomework.php?HomeworkID=" . $RowData[0] . "'\" value=\"订正\" />";
        else if ($StatusRowData[3] == 3)
            echo "<input class=\"GoodButton\" type=\"button\" value=\"订正未批改\" />";
        else if ($StatusRowData[3] == 4 || $StatusRowData[3] == 5)
            echo "<input class=\"GoodButton\" type=\"button\" value=\"已通过\" />";
    }
    echo "<br />";
    echo "<br />";
    CreateText("作业评论：");
    echo "<br />";
    $DatabaseQuery = $Database->prepare("SELECT * FROM HomeworkUploadCheckList WHERE HomeworkUploadID=? ORDER BY CheckTime DESC");
    $DatabaseQuery->bind_param("i", $StatusRowData[0]);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    if ($Result->num_rows == 0) {
        CreateText("无");
    }
    for ($i = 0; $i < $Result->num_rows; $i++) {
        $Result->data_seek($i);
        $CheckRowData = $Result->fetch_array(MYSQLI_NUM);
        echo GetUserName($CheckRowData[2]);
        if ($CheckRowData[3] != "") {
            CreateText($CheckRowData[3]);
        }
        if ($CheckRowData[4] != "") {
            echo "<br />";
            echo "<img class=\"HomeworkImage\" onclick=\"window.open(this.src)\" src=\"HomeworkUploadCheckFile/" . $CheckRowData[0] . "_" . $CheckRowData[4] . "\"></img>";
        }
        CreateText($CheckRowData[5]);
        echo "<br />";
    }
    echo "<br />";
}
require_once "Footer.php";
