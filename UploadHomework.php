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
$InputDetail = "";
$DatabaseQuery = $Database->prepare("SELECT * FROM HomeworkUploadList WHERE HomeworkID=? and UploadUID=?");
$DatabaseQuery->bind_param("ii", $_GET["HomeworkID"], $_SESSION["UID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
if ($Result->num_rows == 0) {
    if (strcmp($RowData[6], date("Y-m-d") . " " . date("h:i")) < 0 && !$RowData[8]) {
        CreateText("非法调用");
        require_once "Footer.php";
        die();
    }
} else {
    $Result->data_seek(0);
    $RowData = $Result->fetch_array(MYSQLI_NUM);
    if ($RowData[6] != 2) {
        CreateText("非法调用");
        require_once "Footer.php";
        die();
    }
    $InputDetail = $RowData[3];
}
if (isset($_POST["Data"])) {
    $InputDetail = SanitizeString($_POST["Data"]);
}
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
echo "<input class=\"MainButton\" type=\"submit\" name=\"Submit\" value=\"提交\" />";
echo "<br />";
echo "<br />";
CreateText("请输入提交的内容：");
echo "<br />";
echo "<textarea class=\"Input\" name=\"Data\">" . $InputDetail . "</textarea>";
echo "</form>";
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\" enctype=\"multipart/form-data\">";
CreateText("请上传提交的文件：");
echo "<br />";
echo "<input class=\"Input\" type=\"file\" name=\"UploadFile\" required />";
echo "<input class=\"SecondButton\" type=\"submit\" name=\"Submit\" value=\"上传\" />";
echo "</form>";
if (isset($_FILES["UploadFile"])) {
    if ($_FILES["UploadFile"]["error"] != 0) {
        CreateErrorText("上传失败，错误码：" . $_FILES["UploadFile"]["error"]);
        echo "<br />";
    } else {
        $DatabaseQuery = $Database->prepare("INSERT INTO HomeworkUploadFileList(HomeworkID, UploadUID, UploadTime, FileName, FileType, FileSize) VALUES (?, ?, current_timestamp(), ?, ?, ?)");
        $DatabaseQuery->bind_param("iisss", $_GET["HomeworkID"], $_SESSION["UID"], $_FILES["UploadFile"]["name"], $_FILES["UploadFile"]["type"], $_FILES["UploadFile"]["size"]);
        $DatabaseQuery->execute();
        move_uploaded_file($_FILES["UploadFile"]["tmp_name"], "HomeworkUploadFile/" . $DatabaseQuery->insert_id . "_" . $_GET["HomeworkID"] . "_" . $_SESSION["UID"] . "_" . $_FILES["UploadFile"]["name"]);
    }
}
if (isset($_POST["Delete"]) && isset($_POST["HomeworkUploadFileID"])) {
    $DatabaseQuery = $Database->prepare("SELECT filename FROM HomeworkUploadFileList WHERE HomeworkUploadFileID=?");
    $DatabaseQuery->bind_param("i", $_POST["HomeworkUploadFileID"]);
    $DatabaseQuery->execute();
    $RowData = $DatabaseQuery->get_result()->fetch_array(MYSQLI_NUM);
    $DatabaseQuery = $Database->prepare("DELETE FROM HomeworkUploadFileList WHERE HomeworkUploadFileID=?");
    $DatabaseQuery->bind_param("i", $_POST["HomeworkUploadFileID"]);
    $DatabaseQuery->execute();
    if (file_exists("HomeworkUploadFile/" . $_POST["HomeworkUploadFileID"] . "_" . $_GET["HomeworkID"] . "_" . $_SESSION["UID"] . "_" . $RowData[0])) {
        if (!unlink("HomeworkUploadFile/" . $_POST["HomeworkUploadFileID"] . "_" . $_GET["HomeworkID"] . "_" . $_SESSION["UID"] . "_" . $RowData[0])) {
            CreateErrorText("删除失败");
            echo "<br />";
        }
    } else {
        CreateErrorText("删除失败：没有该文件，已将该文件记录删除");
        echo "<br />";
    }
}
if (isset($_POST["Submit"]) && isset($_POST["Data"])) {
    $DatabaseQuery = $Database->prepare("SELECT HomeworkUploadFileID FROM HomeworkUploadFileList WHERE UploadUID=? AND HomeworkID=?");
    $DatabaseQuery->bind_param("ii", $_SESSION["UID"], $_GET["HomeworkID"]);
    $DatabaseQuery->execute();
    $Result = $DatabaseQuery->get_result();
    $FileList = "";
    for ($i = 0; $i < $Result->num_rows; $i++) {
        $Result->data_seek($i);
        $RowData = $Result->fetch_array(MYSQLI_NUM);
        $FileList .= $RowData[0] . ",";
    }
    $FileList = substr($FileList, 0, strlen($FileList) - 1);
    if ($FileList == "" && $InputDetail == "") {
        CreateErrorText("请输入内容或上传文件");
        echo "<br />";
    } else {
        $ThisStatus = 0;
        $DatabaseQuery = $Database->prepare("SELECT Status FROM HomeworkUploadList WHERE HomeworkID=? and UploadUID=?");
        $DatabaseQuery->bind_param("ii", $_GET["HomeworkID"], $_SESSION["UID"]);
        $DatabaseQuery->execute();
        $Result = $DatabaseQuery->get_result();
        if ($Result->num_rows == 0) {
            $ThisStatus = 1;
        } else {
            $Result->data_seek(0);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            if ($RowData[0] == 2) {
                $ThisStatus = 3;
            } else {
                $ThisStatus = 1;
            }
            $DatabaseQuery = $Database->prepare("DELETE FROM HomeworkUploadList WHERE HomeworkID=? and UploadUID=?");
            $DatabaseQuery->bind_param("ii", $_GET["HomeworkID"], $_SESSION["UID"]);
            $DatabaseQuery->execute();
        }
        $DatabaseQuery = $Database->prepare("INSERT INTO HomeworkUploadList(HomeworkID, UploadUID, Data, FileList, UploadTime, Status) VALUES (?, ?, ?, ?, current_timestamp(), ?)");
        $DatabaseQuery->bind_param("iissi", $_GET["HomeworkID"], $_SESSION["UID"], $InputDetail, $FileList, $ThisStatus);
        $DatabaseQuery->execute();
        $DatabaseQuery = $Database->prepare("SELECT ClassID FROM HomeworkList WHERE HomeworkID=?");
        $DatabaseQuery->bind_param("i", $_GET["HomeworkID"]);
        $DatabaseQuery->execute();
        $RowData = $DatabaseQuery->get_result()->fetch_array(MYSQLI_NUM);
        echo "<script>window.location=\"Homeworks.php?ClassID=" . $RowData[0] . "\"</script>";
    }
}
if (isset($_POST["Rename"]) && isset($_POST["HomeworkUploadFileID"]) && isset($_POST["AfterFileName"])) {
    $DatabaseQuery = $Database->prepare("SELECT filename FROM HomeworkUploadFileList WHERE HomeworkUploadFileID=?");
    $DatabaseQuery->bind_param("s", $_POST["HomeworkUploadFileID"]);
    $DatabaseQuery->execute();
    $RowData = $DatabaseQuery->get_result()->fetch_array(MYSQLI_NUM);
    if (file_exists("HomeworkUploadFile/" . $_POST["HomeworkUploadFileID"] . "_" . $_GET["HomeworkID"] . "_" . $_SESSION["UID"] . "_" . $RowData[0])) {
        rename("HomeworkUploadFile/" . $_POST["HomeworkUploadFileID"] . "_" . $_GET["HomeworkID"] . "_" . $_SESSION["UID"] . "_" . $RowData[0], "HomeworkUploadFile/" . $_POST["HomeworkUploadFileID"] . "_" . $_GET["HomeworkID"] . "_" . $_SESSION["UID"] . "_" . $_POST["AfterFileName"]);
        $DatabaseQuery = $Database->prepare("UPDATE HomeworkUploadFileList SET filename=? WHERE HomeworkUploadFileID=?");
        $DatabaseQuery->bind_param("si", $_POST["AfterFileName"], $_POST["HomeworkUploadFileID"]);
        $DatabaseQuery->execute();
    } else {
        CreateErrorText("重命名失败：没有该文件，已将该文件记录删除");
        echo "<br />";
        $DatabaseQuery = $Database->prepare("DELETE FROM HomeworkUploadFileList WHERE HomeworkUploadFileID=?");
        $DatabaseQuery->bind_param("i", $_POST["HomeworkUploadFileID"]);
        $DatabaseQuery->execute();
    }
}
$DatabaseQuery = $Database->prepare("SELECT * FROM HomeworkUploadFileList WHERE UploadUID=? AND HomeworkID=?");
$DatabaseQuery->bind_param("ii", $_SESSION["UID"], $_GET["HomeworkID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
echo "<table>";
echo "<thead>";
echo "<td style=\"width: 10%\">";
CreateText("编号");
echo "</td>";
echo "<td style=\"width: 40%\">";
CreateText("文件名");
echo "</td>";
echo "<td style=\"width: 20%\">";
CreateText("文件大小");
echo "</td>";
echo "<td style=\"width: 30%\">";
CreateText("操作");
echo "</td>";
echo "</thead>";
echo "<tbody>";
if ($Result->num_rows == 0) {
    echo "<tr><td>";
    CreateText("空");
    echo "</td></tr>";
}
for ($i = 0; $i < $Result->num_rows; $i++) {
    $Result->data_seek($i);
    $RowData = $Result->fetch_array(MYSQLI_NUM);
    echo "<tr>";
    echo "<td>";
    CreateText($RowData[0]);
    echo "</td>";
    echo "<td>";
    CreateText($RowData[4]);
    echo "</td>";
    echo "<td>";
    CreateText($RowData[6]);
    echo "</td>";
    echo "<td>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"HomeworkUploadFileID\" value=\"$RowData[0]\" />";
    echo "<input class=\"DangerousButton\" name=\"Delete\" type=\"submit\" value=\"删除\" />";
    echo "<input class=\"Input\" type=\"text\" required name=\"AfterFileName\" value=\"$RowData[4]\" />";
    echo "<input class=\"WarningButton\" type=\"submit\" name=\"Rename\" value=\"重命名\" />";
    CreateDownload("HomeworkUploadFile/" . $RowData[0] . "_" . $RowData[1] . "_" . $RowData[2] . "_" . $RowData[4], $RowData[4]);
    echo "</form>";
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
require_once "Footer.php";
