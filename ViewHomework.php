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
if ($Member == "学生") {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
if (isset($_POST["HomeworkUploadID"])) {
    if (isset($_POST["Good"])) {
        $DatabaseQuery = $Database->prepare("UPDATE HomeworkUploadList SET Status=5 WHERE HomeworkUploadID=?");
        $DatabaseQuery->bind_param("i", $_POST["HomeworkUploadID"]);
        $DatabaseQuery->execute();
    }
    if (isset($_POST["Accept"])) {
        $DatabaseQuery = $Database->prepare("UPDATE HomeworkUploadList SET Status=4 WHERE HomeworkUploadID=?");
        $DatabaseQuery->bind_param("i", $_POST["HomeworkUploadID"]);
        $DatabaseQuery->execute();
    }
    if (isset($_POST["Rewrite"])) {
        $DatabaseQuery = $Database->prepare("UPDATE HomeworkUploadList SET Status=2 WHERE HomeworkUploadID=?");
        $DatabaseQuery->bind_param("i", $_POST["HomeworkUploadID"]);
        $DatabaseQuery->execute();
    }
}
if (isset($_POST["Check"]) && isset($_POST["Data"])) {
    $_POST["Data"] = SanitizeString($_POST["Data"]);
    $DatabaseQuery = $Database->prepare("INSERT INTO HomeworkUploadCheckList(HomeworkUploadID, UploadUID, Data, CheckTime) VALUES (?, ?, ?, current_timestamp())");
    $DatabaseQuery->bind_param("iis", $_POST["HomeworkUploadID"], $_SESSION["UID"], $_POST["Data"]);
    $DatabaseQuery->execute();
}
echo "<input type=\"checkbox\" " . (isset($_GET["Search1"]) ? "checked " : "") . "id=\"Search1\" />";
CreateText("提交未批改");
echo "<input type=\"checkbox\" " . (isset($_GET["Search2"]) ? "checked " : "") . "id=\"Search2\" />";
CreateText("需订正");
echo "<input type=\"checkbox\" " . (isset($_GET["Search3"]) ? "checked " : "") . "id=\"Search3\" />";
CreateText("订正未批改");
echo "<input type=\"checkbox\" " . (isset($_GET["Search4"]) ? "checked " : "") . "id=\"Search4\" />";
CreateText("通过");
echo "<input type=\"checkbox\" " . (isset($_GET["Search5"]) ? "checked " : "") . "id=\"Search5\" />";
CreateText("优秀");
echo "<input type=\"button\" class=\"MainButton\" id=\"Search\" value=\"筛选\" />";
echo "<br />";
echo "<script>";
echo "Search.onclick = function() {";
echo "var URL = \"ViewHomework.php?HomeworkID=" . $_GET["HomeworkID"] . "&\";";
echo "if (Search1.checked) {";
echo "URL += \"Search1&\";";
echo "}";
echo "if (Search2.checked) {";
echo "URL += \"Search2&\";";
echo "}";
echo "if (Search3.checked) {";
echo "URL += \"Search3&\";";
echo "}";
echo "if (Search4.checked) {";
echo "URL += \"Search4&\";";
echo "}";
echo "if (Search5.checked) {";
echo "URL += \"Search5&\";";
echo "}";
echo "window.location=URL;";
echo "};";
echo "</script>";
$AcceptStatus = array();
if (isset($_GET["Search1"])) {
    array_push($AcceptStatus, 1);
}
if (isset($_GET["Search2"])) {
    array_push($AcceptStatus, 2);
}
if (isset($_GET["Search3"])) {
    array_push($AcceptStatus, 3);
}
if (isset($_GET["Search4"])) {
    array_push($AcceptStatus, 4);
}
if (isset($_GET["Search5"])) {
    array_push($AcceptStatus, 5);
}
if (sizeof($AcceptStatus) == 0) {
    $AcceptStatus = array(1, 2, 3, 4, 5);
}
$Query = "SELECT * FROM HomeworkUploadList WHERE HomeworkID=? AND (";
for ($i = 0; $i < sizeof($AcceptStatus); $i++) {
    $Query .= "Status=" . $AcceptStatus[$i] . " OR ";
}
$Query = substr($Query, 0, strlen($Query) - 4);
$Query .= ") ORDER BY UploadTime DESC";
$DatabaseQuery = $Database->prepare($Query);
$DatabaseQuery->bind_param("i", $_GET["HomeworkID"]);
$DatabaseQuery->execute();
$Result = $DatabaseQuery->get_result();
for ($i = 0; $i < $Result->num_rows; $i++) {
    echo "<div class=\"CheckHomeworkDiv\">";
    $Result->data_seek($i);
    $RowData = $Result->fetch_array(MYSQLI_NUM);
    echo GetUserName($RowData[2]);
    CreateText("状态：" . GetHomeworkStatusName($RowData[6]));
    CreateText("提交时间：" . $RowData[5]);
    echo "<br />";
    if ($RowData[3] != "")
        CreateText($RowData[3]);
    $FileList = array();
    if ($RowData[4] != "") {
        $TempArray = mb_split(",", $RowData[4]);
        for ($j = 0; $j < count($TempArray); $j++) {
            if ($TempArray[$j] == "") continue;
            $FileIndex = $TempArray[$j];
            $DatabaseFileQuery = $Database->prepare("SELECT * FROM HomeworkUploadFileList WHERE HomeworkUploadFileID=?");
            $DatabaseFileQuery->bind_param("i", $FileIndex);
            $DatabaseFileQuery->execute();
            $FileResult = $DatabaseFileQuery->get_result();
            if ($FileResult->num_rows == 1) {
                $FileResult->data_seek(0);
                $FileRowData = $FileResult->fetch_array(MYSQLI_NUM);
                if (IsPicture($FileRowData[4])) {
                    array_push($FileList, array("HomeworkUploadFile/" . $FileRowData[0] . "_" . $FileRowData[1] . "_" . $FileRowData[2] . "_", $FileRowData[4]));
                } else {
                    echo "<img class=\"HomeworkImage\" onclick=\"Draw(this, " . $RowData[0] . ")\" class=\"HomeworkImage\" src=\"" . "HomeworkUploadFile/" . $FileRowData[0] . "_" . $FileRowData[1] . "_" . $FileRowData[2] . "_" . $FileRowData[4] . "\" />";
                }
            } else {
                CreateText("系统错误：找不到此文件");
            }
        }
    }
    echo "<br />";
    for ($j = 0; $j < sizeof($FileList); $j++) {
        CreateDownload($FileList[$j][0] . $FileList[$j][1], $FileList[$j][1], "文件：" . $FileList[$j][1]);
    }
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"HomeworkUploadID\" value=\"" . $RowData[0] . "\" />";
    echo "<input type=\"submit\" class=\"GoodButton\" name=\"Good\" value=\"优秀\" />";
    echo "<input type=\"submit\" class=\"SecondButton\" name=\"Accept\" value=\"通过\" />";
    echo "<input type=\"submit\" class=\"WarningButton\" name=\"Rewrite\" value=\"订正\" />";
    echo "</form>";
    echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
    echo "<input type=\"hidden\" name=\"HomeworkUploadID\" value=\"" . $RowData[0] . "\" />";
    echo "<input type=\"input\" class=\"Input\" required name=\"Data\" />";
    echo "<input type=\"submit\" class=\"SecondButton\" name=\"Check\" value=\"评论\" />";
    echo "</form>";
    $CheckTemp = $Database->prepare("SELECT * FROM HomeworkUploadCheckList WHERE HomeworkUploadID=? ORDER BY CheckTime DESC");
    $CheckTemp->bind_param("i", $RowData[0]);
    $CheckTemp->execute();
    $CheckResult = $CheckTemp->get_result();
    for ($j = 0; $j < $CheckResult->num_rows; $j++) {
        $CheckResult->data_seek($j);
        $CheckRowData = $CheckResult->fetch_array(MYSQLI_NUM);
        echo GetUserName($CheckRowData[2]);
        if ($CheckRowData[3] != "") {
            CreateText($CheckRowData[3]);
        }
        if ($CheckRowData[4] != "") {
            echo "<br />";
            echo "<img class=\"HomeworkImage\" onclick=\"window.open(this.src)\" src=\"HomeworkUploadCheckFile/" . $CheckRowData[0] . "_" . $CheckRowData[4] . "\"></img>";
        }
        echo "<br />";
    }
    echo "</div>";
}
echo "<script src=\"Draw.js\"></script>";
require_once "Footer.php";
