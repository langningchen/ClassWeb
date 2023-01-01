<?php
require_once "NotLogin.php";
require_once "Header.php";
if (!isset($_GET["ClassID"])) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
$Member = GetClassType($_GET["ClassID"]);
if ($Member == "") {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
if ($Member != "学生") {
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    CreateText("请输入已上传文件的编号：");
    echo "<input type=\"number\" class=\"Input\" required name=\"FileID\" id=\"file\" />";
    echo "<input type=\"submit\" class=\"MainButton\" name=\"New\" value=\"添加\" />";
    echo "</form>";
}
if (isset($_POST["New"]) && isset($_POST["FileID"])) {
    if ($Member == "学生") {
        CreateErrorText("没有权限");
        echo "<br />";
    } else {
        $DatabaseQuery = $Database->prepare("SELECT uploaduid FROM FileList WHERE ID=?");
        $DatabaseQuery->bind_param("i", $_POST["FileID"]);
        $DatabaseQuery->execute();
        $Result = $DatabaseQuery->get_result();
        if ($Result->num_rows != 1) {
            CreateErrorText("没有该文件");
            echo "<br />";
        } else {
            $Result->data_seek(0);
            $RowData = $Result->fetch_array(MYSQLI_NUM);
            if ($RowData[0] != $_SESSION["UID"]) {
                CreateErrorText("必须是自己上传的文件才能设为班级文件");
                echo "<br />";
            } else {
                $DatabaseQuery = $Database->prepare("INSERT INTO ClassFileList(ClassID, UID, FileID) VALUES (?,?,?)");
                $DatabaseQuery->bind_param("iii", $_GET["ClassID"], $_SESSION["UID"], $_POST["FileID"]);
                $DatabaseQuery->execute();

                $DatabaseQuery = $Database->prepare("SELECT * FROM ClassList WHERE ClassID=?");
                $DatabaseQuery->bind_param("i", $_GET["ClassID"]);
                $DatabaseQuery->execute();
                $RowData = $DatabaseQuery->get_result()->fetch_array(MYSQLI_NUM);
                $TempArray = mb_split(",", $RowData[4]);
                for ($i = 0; $i < count($TempArray); $i++) {
                    if ($TempArray[$i] == "") continue;
                    AddMessage($TempArray[$i], GetUserName($_SESSION["UID"]) . "在" . $RowData[1] . "添加了班级文件", "ClassFile.php?ClassID=" . $_GET["ClassID"]);
                }
            }
        }
    }
}
if (isset($_POST["Delete"]) && isset($_POST["ClassFileID"])) {
    if ($Member == "学生") {
        CreateErrorText("没有权限");
        echo "<br />";
    } else {
        $DatabaseQuery = $Database->prepare("DELETE FROM ClassFileList WHERE ClassFileID=?");
        $DatabaseQuery->bind_param("i", $_POST["ClassFileID"]);
        $DatabaseQuery->execute();
    }
}
echo "<br />";
$DatabaseQuery = $Database->prepare("SELECT ClassFileID, UID, FileID FROM ClassFileList");
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
CreateText("文件数量：");
CreateText($Result->num_rows);
echo "<br />";
echo "<table>";
echo "<thead>";
echo "<td style=\"width: 20%\">";
CreateText("上传者");
echo "</td>";
echo "<td style=\"width: 40%\">";
CreateText("文件名");
echo "</td>";
echo "<td style=\"width: 20%\">";
CreateText("文件大小");
echo "</td>";
echo "<td style=\"width: 20%\">";
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
    echo GetUserName($RowData[1]);
    echo "</td>";
    $DatabaseQuery = $Database->prepare("SELECT ID, filename, filesize FROM FileList WHERE ID=?");
    $DatabaseQuery->bind_param("i", $RowData[2]);
    $DatabaseQuery->execute();
    $TempResult = $DatabaseQuery->get_result();
    if ($TempResult->num_rows == 0) {
        echo "<td>";
        CreateText("文件已被上传者删除");
        echo "</td>";
        echo "<td>";
        echo "</td>";
    }
    if ($TempResult->num_rows != 0) {
        $TempResult->data_seek($i);
        $TempRowData = $TempResult->fetch_array(MYSQLI_NUM);
        echo "<td>";
        CreateText($TempRowData[1]);
        echo "</td>";
        echo "<td>";
        CreateText($TempRowData[2]);
        echo "</td>";
    }
    echo "<td>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    if ($Member != "学生") {
        echo "<input type=\"hidden\" name=\"ClassFileID\" value=\"" . $RowData[0] . "\" />";
        echo "<input class=\"DangerousButton\" name=\"Delete\" type=\"submit\" value=\"删除\" />";
    }
    if ($TempResult->num_rows != 0) {
        CreateDownload("UploadFile/" . $TempRowData[0] . "_" . $TempRowData[1], $TempRowData[1]);
    }
    echo "</form>";
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
require_once "Footer.php";
