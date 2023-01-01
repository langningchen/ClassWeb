<?php
require_once "NotLogin.php";
require_once "Header.php";
require_once "Classes/PHPExcel.php";
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
if (($Member == "学生" || $RowData[2] != $_SESSION["UID"]) && $_SESSION["UserType"] != 2) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
$DatabaseQuery = $Database->prepare("SELECT * FROM HomeworkUploadList WHERE HomeworkID=?");
$DatabaseQuery->bind_param("i", $_GET["HomeworkID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
CreateText("正在打包数据……");
echo "<br />";
FlushOutput();

$ZipFileName = $_GET["HomeworkID"] . "号作业数据包.zip";
if (file_exists("HomeworkDownloadFile/" . $ZipFileName)) {
    unlink("HomeworkDownloadFile/" . $ZipFileName);
}
$ZipFile = new \ZipArchive;
$ZipFile->open("HomeworkDownloadFile/" . $ZipFileName, \ZipArchive::CREATE);

$ExcelObject = new PHPExcel();
$ExcelSheet = $ExcelObject->getActiveSheet();
$ExcelSheet->setTitle("数据");
$ExcelSheet->setCellValue("A1", "用户编号");
$ExcelSheet->setCellValue("B1", "用户名");
$ExcelSheet->setCellValue("C1", "提交时间");

for ($i = 0; $i < $Result->num_rows; $i++) {
    $Result->data_seek($i);
    $RowData = $Result->fetch_array(MYSQLI_NUM);
    if ($RowData[3] != "") {
        $ZipFile->addFromString("用户" . $RowData[2] . "（" . GetNoLinkUserName($RowData[2]) . "）作业内容.txt", $RowData[3]);
    }

    $TempArray = mb_split(",", $RowData[4]);
    for ($i = 0; $i < count($TempArray); $i++) {
        if ($TempArray[$i] == "") continue;
        $FileIndex = $TempArray[$i];
        $DatabaseFileQuery = $Database->prepare("SELECT * FROM HomeworkUploadFileList WHERE HomeworkUploadFileID=?");
        $DatabaseFileQuery->bind_param("i", $FileIndex);
        $DatabaseFileQuery->execute();
        $FileResult = $DatabaseFileQuery->get_result();
        if ($Result->num_rows == 1) {
            $FileResult->data_seek(0);
            $FileRowData = $FileResult->fetch_array(MYSQLI_NUM);
            $ZipFile->addFile(
                "HomeworkUploadFile/" . $FileRowData[0] . "_" . $FileRowData[1] . "_" . $FileRowData[2] . "_" . $FileRowData[4],
                "用户" . $RowData[2] . "（" . GetNoLinkUserName($RowData[2]) . "）上传的文件：" . $FileRowData[4]
            );
        }
    }

    $ExcelSheet->setCellValue("A" . ($i + 1), $RowData[2]);
    $ExcelSheet->setCellValue("B" . ($i + 1), GetNoLinkUserName($RowData[2]));
    $ExcelSheet->setCellValue("C" . ($i + 1), $RowData[5]);
}
$ExcelWriter = PHPExcel_IOFactory::createWriter($ExcelObject, "Excel2007");
$ExcelWriter->save(__DIR__ . "/HomeworkDownloadFile/" . $_GET["HomeworkID"] . "号作业上传时间.xlsx");

$ZipFile->addFile("HomeworkDownloadFile/" . $_GET["HomeworkID"] . "号作业上传时间.xlsx", "上传时间.xlsx");
$ZipFile->close();

echo "<br />";
CreateText("打包完成！");
echo "<br />";
echo "<br />";
CreateDownload("HomeworkDownloadFile/" . $ZipFileName, $ZipFileName);
require_once "Footer.php";
