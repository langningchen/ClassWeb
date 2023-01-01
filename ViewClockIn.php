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
if ($Member == "学生") {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
if (isset($_POST["Check"]) && isset($_POST["Data"])) {
    $_POST["Data"] = SanitizeString($_POST["Data"]);
    $DatabaseQuery = $Database->prepare("INSERT INTO ClockInUploadCheckList(ClockInUploadID, UploadUID, Data, CheckTime) VALUES (?, ?, ?, current_timestamp())");
    $DatabaseQuery->bind_param("iis", $_POST["ClockInUploadID"], $_SESSION["UID"], $_POST["Data"]);
    $DatabaseQuery->execute();
}
$DatabaseQuery = $Database->prepare("SELECT * FROM ClockInUploadList WHERE ClockInID=? ORDER BY UploadTime DESC");
$DatabaseQuery->bind_param("i", $_GET["ClockInID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
for ($i = 0; $i < $Result->num_rows; $i++) {
    echo "<div class=\"CheckClockInDiv\">";
    $Result->data_seek($i);
    $RowData = $Result->fetch_array(MYSQLI_NUM);
    echo GetUserName($RowData[2]);
    CreateText("提交日期：" . $RowData[5]);
    CreateText("提交时间：" . $RowData[6]);
    echo "<br />";
    if ($RowData[3] != "") {
        CreateText($RowData[3]);
        echo "<br />";
    }
    $FileList = array();
    if ($RowData[4] != "") {
        $TempArray = mb_split(",", $RowData[4]);
        for ($j = 0; $j < count($TempArray); $j++) {
            if ($TempArray[$j] == "") continue;
            $FileIndex = $TempArray[$j];
            $DatabaseFileQuery = $Database->prepare("SELECT * FROM ClockInUploadFileList WHERE ClockInUploadFileID=?");
            $DatabaseFileQuery->bind_param("i", $FileIndex);
            $DatabaseFileQuery->execute();
            $FileResult = $DatabaseFileQuery->get_result();
            if ($FileResult->num_rows == 1) {
                $FileResult->data_seek(0);
                $FileRowData = $FileResult->fetch_array(MYSQLI_NUM);
                CreateDownload("ClockInUploadFile/" . $FileRowData[0] . "_" . $FileRowData[1] . "_" . $FileRowData[2] . "_" . $FileRowData[4], $FileRowData[4], "文件：" . $FileRowData[4]);
            } else {
                CreateText("系统错误：找不到此文件");
            }
        }
    }
    echo "<br />";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"ClockInUploadID\" value=\"" . $RowData[0] . "\" />";
    echo "<input type=\"input\" class=\"Input\" required name=\"Data\" />";
    echo "<input type=\"submit\" class=\"SecondButton\" name=\"Check\" value=\"评论\" />";
    echo "</form>";
    $CheckTemp = $Database->prepare("SELECT * FROM ClockInUploadCheckList WHERE ClockInUploadID=? ORDER BY CheckTime DESC");
    $CheckTemp->bind_param("i", $RowData[0]);
    $CheckTemp->execute();
    $CheckResult = $CheckTemp->get_result();
    for ($j = 0; $j < $CheckResult->num_rows; $j++) {
        $CheckResult->data_seek($j);
        $CheckRowData = $CheckResult->fetch_array(MYSQLI_NUM);
        echo GetUserName($CheckRowData[2]);
        CreateText($CheckRowData[3]);
        CreateText($CheckRowData[4]);
        echo "<br />";
    }
    echo "</div>";
}
require_once "Footer.php";
