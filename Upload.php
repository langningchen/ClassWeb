<?php
require_once "NotLogin.php";
require_once "Header.php";
if ($_SESSION["UserType"] == 0) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\" enctype=\"multipart/form-data\">";
echo "<input class=\"Input\" type=\"file\" name=\"file\" id=\"file\" required />";
echo "<input class=\"MainButton\" type=\"submit\" name=\"submit\" value=\"上传\" />";
echo "</form>";
echo "<br />";
if (isset($_FILES["file"])) {
    if ($_FILES["file"]["error"] != 0) {
        $CreateErrorText("上传失败，错误码：" . $_FILES["file"]["error"]);
        echo "<br />";
    } else {
        $DatabaseQuery = $Database->prepare("INSERT INTO FileList(uploaduid, filename, filetype, filesize) VALUES (?,?,?,?)");
        $DatabaseQuery->bind_param("issi", $_SESSION["UID"], $_FILES["file"]["name"], $_FILES["file"]["type"], $_FILES["file"]["size"]);
        $DatabaseQuery->execute();
        move_uploaded_file($_FILES["file"]["tmp_name"], "UploadFile/" . $DatabaseQuery->insert_id . "_" . $_FILES["file"]["name"]);
    }
}
if (isset($_POST["Delete"]) && isset($_POST["ID"])) {
    $DatabaseQuery = $Database->prepare("SELECT filename FROM FileList WHERE ID=?");
    $DatabaseQuery->bind_param("i", $_POST["ID"]);
    $DatabaseQuery->execute();
    $RowData = $DatabaseQuery->get_result()->fetch_array(MYSQLI_NUM);
    $DatabaseQuery = $Database->prepare("DELETE FROM FileList WHERE ID=?");
    $DatabaseQuery->bind_param("i", $_POST["ID"]);
    $DatabaseQuery->execute();
    if (file_exists("UploadFile/" . $_POST["ID"] . "_" . $RowData[0])) {
        if (!unlink("UploadFile/" . $_POST["ID"] . "_" . $RowData[0])) {
            $CreateErrorText("删除失败");
            echo "<br />";
        }
    } else {
        CreateErrorText("删除失败：没有该文件，已将该文件记录删除");
        echo "<br />";
    }
}
if (isset($_POST["Rename"]) && isset($_POST["ID"]) && isset($_POST["AfterFileName"])) {
    $DatabaseQuery = $Database->prepare("SELECT filename FROM FileList WHERE ID=?");
    $DatabaseQuery->bind_param("i", $_POST["ID"]);
    $DatabaseQuery->execute();
    $RowData = $DatabaseQuery->get_result()->fetch_array(MYSQLI_NUM);
    if (file_exists("UploadFile/" . $_POST["ID"] . "_" . $RowData[0])) {
        rename("UploadFile/" . $_POST["ID"] . "_" . $RowData[0], "UploadFile/" . $_POST["ID"] . "_" . $_POST["AfterFileName"]);
        $DatabaseQuery = $Database->prepare("UPDATE FileList SET filename=? WHERE ID=?");
        $DatabaseQuery->bind_param("si", $_POST["AfterFileName"], $_POST["ID"]);
        $DatabaseQuery->execute();
    } else {
        CreateErrorText("重命名失败：没有该文件，已将该文件记录删除");
        echo "<br />";
        $DatabaseQuery = $Database->prepare("DELETE FROM FileList WHERE ID=?");
        $DatabaseQuery->bind_param("i", $_POST["ID"]);
        $DatabaseQuery->execute();
    }
}
$DatabaseQuery = $Database->prepare("SELECT filename, filesize, ID FROM FileList WHERE uploaduid=?");
$DatabaseQuery->bind_param("i", $_SESSION["UID"]);
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
    CreateText($RowData[2]);
    echo "</td>";
    echo "<td>";
    CreateText($RowData[0]);
    echo "</td>";
    echo "<td>";
    CreateText($RowData[1]);
    echo "</td>";
    echo "<td>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"ID\" value=\"$RowData[2]\" />";
    echo "<input class=\"DangerousButton\" name=\"Delete\" type=\"submit\" value=\"删除\" />";
    echo "<input class=\"Input\" type=\"text\" required name=\"AfterFileName\" value=\"$RowData[0]\" />";
    echo "<input class=\"WarningButton\" type=\"submit\" name=\"Rename\" value=\"重命名\" />";
    CreateDownload("UploadFile/" . $RowData[2] . "_" . $RowData[0], $RowData[0]);
    echo "</form>";
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
require_once "Footer.php";
