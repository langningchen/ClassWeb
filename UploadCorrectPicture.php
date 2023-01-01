<?php
require_once "NotLogin.php";
require_once "Function.php";
if (!isset($_POST["HomeworkUploadID"])) {
    CreateText("非法调用");
    require_once("Footer.php");
    die();
}
$DatabaseQuery = $Database->prepare("SELECT * FROM HomeworkUploadList WHERE HomeworkUploadID=?");
$DatabaseQuery->bind_param("i", $_POST["HomeworkUploadID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
if ($Result->num_rows != 1) {
    CreateText("非法调用");
    require_once("Footer.php");
    die();
}
$Result->data_seek(0);
$RowData = $Result->fetch_array(MYSQLI_NUM);
$Member = GetClassType($RowData[1]);
if ($Member == "") {
    CreateText("非法调用");
    require_once("Footer.php");
    die();
}
if ($Member == "学生") {
    CreateText("非法调用");
    require_once("Footer.php");
    die();
}
$Image = $_POST["Image"];
$Image = str_replace('data:image/png;base64,', '', $Image);
$Image = str_replace(' ', '+', $Image);
$ImageData = base64_decode($Image);
$File = $_SESSION["UID"] . "_" . time() . "_Check.png";
$DatabaseQuery = $Database->prepare("INSERT INTO HomeworkUploadCheckList(HomeworkUploadID, UploadUID, FileName) VALUES (?, ?, ?)");
$DatabaseQuery->bind_param("iis", $_POST["HomeworkUploadID"], $_SESSION["UID"], $File);
$DatabaseQuery->execute();
if (!file_put_contents("HomeworkUploadCheckFile/" . $DatabaseQuery->insert_id . "_" . $File, $ImageData)) {
    CreateText("系统错误：写入文件失败");
    require_once("Footer.php");
    die();
}
echo "OK";
