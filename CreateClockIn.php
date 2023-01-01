<?php
require_once "NotLogin.php";
require_once "Header.php";
$NeedUpload = $CanUploadAfterEnd = "1";
$Data = "请在此处提交今天的打卡";
$EndDate = date("Y-m-d");
$Title = date("Y年m月d日") . GetNoLinkUserName($_SESSION["UID"]) . "布置的打卡";
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
if ($Member == "学生") {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
if (isset($_POST["Title"]) && isset($_POST["Data"]) && isset($_POST["CanUploadAfterEnd"]) && isset($_POST["EndDate"])) {
    $Title = SanitizeString($_POST["Title"]);
    $Data = SanitizeString($_POST["Data"]);
    $CanUploadAfterEnd = $_POST["CanUploadAfterEnd"];
    $EndDate = $_POST["EndDate"];
}
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
CreateText("标题");
echo "<input class=\"Input\" style=\"width: 50%; \" type=\"text\" name=\"Title\" require=\"required\" placeholder=\"\" value=\"" . $Title . "\"/>";
echo "<br />";
CreateText("内容");
echo "<br />";
echo "<textarea class=\"Input\" style=\"width: 90%; \" name=\"Data\" require=\"required\">" . $Data . "</textarea>";
echo "<br />";
CreateText("是否允许补打卡：");
echo "<select class=\"SecondButton\" name=\"CanUploadAfterEnd\" value=\"" . $CanUploadAfterEnd . "\">";
echo "<option value=\"1\">允许</option>";
echo "<option value=\"0\">不允许</option>";
echo "</select>";
echo "<br />";
CreateText("结束日期：");
echo "<input class=\"Input\" type=\"date\" require=\"required\" name=\"EndDate\" value=\"" . $EndDate . "\" />";
echo "<br />";
echo "<input class=\"MainButton\" type=\"submit\" name=\"Create\" value=\"发布\" />";
echo "</form>";
echo "<br />";
if (isset($_POST["Title"]) && isset($_POST["Data"]) && isset($_POST["CanUploadAfterEnd"]) && isset($_POST["EndDate"])) {
    if ($_POST["Title"] != "" && $_POST["Data"] != "" && $_POST["EndDate"] != "") {
        $_POST["Data"] = SanitizeString($_POST["Data"]);
        $DatabaseQuery = $Database->prepare("INSERT INTO ClockInList(ClassID, UploadUID, Title, Data, CreateTime, EndTime, CanUploadAfterEnd) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $Today = date("Y-m-d");
        $DatabaseQuery->bind_param("iissssi", $_GET["ClassID"], $_SESSION["UID"], $_POST["Title"], $_POST["Data"], $Today, $_POST["EndDate"], $_POST["CanUploadAfterEnd"]);
        $DatabaseQuery->execute();
        $ClockInID = $DatabaseQuery->insert_id;
        $DatabaseQuery = $Database->prepare("SELECT * FROM ClassList WHERE ClassID=?");
        $DatabaseQuery->bind_param("i", $_GET["ClassID"]);
        $DatabaseQuery->execute();
        $RowData = $DatabaseQuery->get_result()->fetch_array(MYSQLI_NUM);
        $TempArray = mb_split(",", $RowData[4]);
        for ($i = 0; $i < count($TempArray); $i++) {
            if ($TempArray[$i] == "") continue;
            AddMessage($TempArray[$i], GetUserName($_SESSION["UID"]) . "在" . $RowData[1] . "布置了打卡", "ClockIn.php?ClockInID=" . $ClockInID);
        }
        echo "<script>window.location=\"ClockIns.php?ClassID=" . $_GET["ClassID"] . "\"</script>";
    } else {
        CreateErrorText("请填写完整");
        echo "<br />";
    }
}
require_once "Footer.php";
