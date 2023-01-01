<?php
require_once "NotLogin.php";
require_once "Header.php";
require_once "Classes/PHPExcel.php";
if (!isset($_GET["ClockInID"])) {
    CreateErrorText("非法调用");
    require_once "Footer.php";
    die();
}
$DatabaseQuery = $Database->prepare("SELECT * FROM ClockInList WHERE ClockInID=?");
$DatabaseQuery->bind_param("i", $_GET["ClockInID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
if ($Result->num_rows != 1) {
    CreateErrorText("非法调用");
    require_once "Footer.php";
    die();
}
$Result->data_seek(0);
$RowData = $Result->fetch_array(MYSQLI_NUM);
$Member = GetClassType($RowData[1]);
if ($Member == "") {
    CreateErrorText("非法调用");
    require_once "Footer.php";
    die();
}
if (($Member == "学生" || $RowData[2] != $_SESSION["UID"]) && $_SESSION["UserType"] != 2) {
    CreateErrorText("非法调用");
    require_once "Footer.php";
    die();
}
$DatabaseQuery = $Database->prepare("SELECT * FROM ClockInUploadList WHERE ClockInID=?");
$DatabaseQuery->bind_param("i", $_GET["ClockInID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
CreateText("正在打包数据……");
echo "<br />";
FlushOutput();

$ZipFileName = $_GET["ClockInID"] . "号打卡数据包.zip";
if (file_exists("ClockInDownloadFile/" . $ZipFileName)) {
    unlink("ClockInDownloadFile/" . $ZipFileName);
}
$ZipFile = new ZipArchive;
$ZipFile->open("ClockInDownloadFile/" . $ZipFileName, ZipArchive::CREATE);

$ExcelObject = new PHPExcel;
$ExcelSheet = $ExcelObject->getActiveSheet();
$ExcelSheet->setTitle("数据");
$ExcelSheet->setCellValue("A1", "用户编号");
$ExcelSheet->setCellValue("B1", "用户名");
$ExcelSheet->setCellValue("C1", "提交日期");
$ExcelSheet->setCellValue("D1", "提交时间");

for ($i = 0; $i < $Result->num_rows; $i++) {
    $Result->data_seek($i);
    $RowData = $Result->fetch_array(MYSQLI_NUM);
    if ($RowData[3] != "") {
        $ZipFile->addFromString("用户" . $RowData[2] . "（" . GetNoLinkUserName($RowData[2]) . "）打卡内容.txt", $RowData[3]);
    }

    $TempArray = mb_split(",", $RowData[4]);
    for ($i = 0; $i < count($TempArray); $i++) {
        if ($TempArray[$i] == "") continue;
        $FileIndex = $TempArray[$i];
        $DatabaseFileQuery = $Database->prepare("SELECT * FROM ClockInUploadFileList WHERE ClockInUploadFileID=?");
        $DatabaseFileQuery->bind_param("i", $FileIndex);
        $DatabaseFileQuery->execute();
        $FileResult = $DatabaseFileQuery->get_result();
        if ($Result->num_rows == 1) {
            $FileResult->data_seek(0);
            $FileRowData = $FileResult->fetch_array(MYSQLI_NUM);
            $ZipFile->addFile(
                "ClockInUploadFile/" . $FileRowData[0] . "_" . $FileRowData[1] . "_" . $FileRowData[2] . "_" . $FileRowData[4],
                "用户" . $RowData[2] . "（" . GetNoLinkUserName($RowData[2]) . "）上传的文件：" . $FileRowData[4]
            );
        }
    }

    $ExcelSheet->setCellValue("A" . ($i + 1), $RowData[2]);
    $ExcelSheet->setCellValue("B" . ($i + 1), GetNoLinkUserName($RowData[2]));
    $ExcelSheet->setCellValue("C" . ($i + 1), $RowData[5]);
    $ExcelSheet->setCellValue("D" . ($i + 1), $RowData[6]);
}
$ZipFile->close();
echo "<br />";
CreateSuccessText("打包完成！");
echo "<br />";
echo "<br />";
CreateDownload("ClockInDownloadFile/" . $ZipFileName, $ZipFileName);
require_once "Footer.php";
