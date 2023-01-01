<?php
require_once "NotLogin.php";
require_once "Header.php";
$NeedUpload = $CanUploadAfterEnd = "1";
$Data = "请在此处提交今天的作业";
$EndDate = date("Y-m-d");
$EndTime = "22:00:00";
$Title = date("Y年m月d日") . GetNoLinkUserName($_SESSION["UID"]) . "布置的作业";
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
if (isset($_POST["Title"]) && isset($_POST["Data"]) && isset($_POST["NeedUpload"]) && isset($_POST["CanUploadAfterEnd"]) && isset($_POST["EndDate"]) && isset($_POST["EndTime"])) {
    $Title = SanitizeString($_POST["Title"]);
    $Data = SanitizeString($_POST["Data"]);
    $NeedUpload = $_POST["NeedUpload"];
    $CanUploadAfterEnd = $_POST["CanUploadAfterEnd"];
    $EndDate = $_POST["EndDate"];
    $EndTime = $_POST["EndTime"];
}
echo "<form action=\"" . $_SERVER["REQUEST_URI"] . "\" method=\"post\">";
CreateText("标题");
echo "<input class=\"Input\" style=\"width: 50%; \" type=\"text\" name=\"Title\" require=\"required\" placeholder=\"\" value=\"" . $Title . "\"/>";
echo "<br />";
CreateText("内容");
echo "<br />";
echo "<textarea class=\"Input\" style=\"width: 90%; \" name=\"Data\" require=\"required\">" . $Data . "</textarea>";
echo "<br />";
CreateText("是否需要提交：");
echo "<select class=\"SecondButton\" name=\"NeedUpload\" value=\"" . $NeedUpload . "\">";
echo "<option value=\"1\">需要</option>";
echo "<option value=\"0\">不需要</option>";
echo "</select>";
echo "<script>";
echo "document.getElementsByName(\"NeedUpload\")[0].onchange = function() {";
echo "    if (document.getElementsByName(\"NeedUpload\")[0].value == \"0\") {";
echo "        document.getElementById(\"CanUploadAfterEnd\").style.display = \"none\";";
echo "        document.getElementById(\"EndDateTime\").style.display = \"none\";";
echo "    }";
echo "    else {";
echo "        document.getElementById(\"CanUploadAfterEnd\").style.display = \"\";";
echo "        document.getElementById(\"EndDateTime\").style.display = \"\";";
echo "    }";
echo "}";
echo "</script>";
echo "<br />";
echo "<div id=\"CanUploadAfterEnd\">";
CreateText("是否允许补交：");
echo "<select class=\"SecondButton\" name=\"CanUploadAfterEnd\" value=\"" . $CanUploadAfterEnd . "\">";
echo "<option value=\"1\">允许</option>";
echo "<option value=\"0\">不允许</option>";
echo "</select>";
echo "<br />";
echo "</div>";
echo "<div id=\"EndDateTime\">";
CreateText("截止时间：");
echo "<input class=\"Input\" type=\"date\" require=\"required\" name=\"EndDate\" value=\"" . $EndDate . "\" />";
echo "<input class=\"Input\" type=\"time\" require=\"required\" name=\"EndTime\" value=\"" . $EndTime . "\" />";
echo "<br />";
echo "</div>";
echo "<input class=\"MainButton\" type=\"submit\" name=\"Create\" value=\"发布\" />";
echo "</form>";
echo "<br />";
CreateText("提示：因为技术原因，如需添加附件请上传至班级文件并在正文中使用文字说明，感谢您的谅解");
echo "<br />";
echo "<br />";
if (isset($_POST["Title"]) && isset($_POST["Data"]) && isset($_POST["NeedUpload"]) && isset($_POST["CanUploadAfterEnd"]) && isset($_POST["EndDate"]) && isset($_POST["EndTime"])) {
    if ($_POST["Title"] != "" && $_POST["Data"] != "" && $_POST["EndDate"] != "" && $_POST["EndTime"] != "") {
        $_POST["Data"] = SanitizeString($_POST["Data"]);
        $_POST["EndTime"] = $_POST["EndDate"] . " " . $_POST["EndTime"];
        $DatabaseQuery = $Database->prepare("INSERT INTO HomeworkList(ClassID, UploadUID, Title, Data, CreateTime, EndTime, NeedUpload, CanUploadAfterEnd) VALUES (?, ?, ?, ?, current_timestamp(), ?, ?, ?)");
        $DatabaseQuery->bind_param("iisssii", $_GET["ClassID"], $_SESSION["UID"], $_POST["Title"], $_POST["Data"], $_POST["EndTime"], $_POST["NeedUpload"], $_POST["CanUploadAfterEnd"]);
        $DatabaseQuery->execute();
        $ClassHomeworkID = $DatabaseQuery->insert_id;
        echo "<script>window.location=\"Homeworks.php?ClassID=" . $_GET["ClassID"] . "\"</script>";

        $DatabaseQuery = $Database->prepare("SELECT * FROM ClassList WHERE ClassID=?");
        $DatabaseQuery->bind_param("i", $_GET["ClassID"]);
        $DatabaseQuery->execute();
        $RowData = $DatabaseQuery->get_result()->fetch_array(MYSQLI_NUM);
        $TempArray = mb_split(",", $RowData[4]);
        for ($i = 0; $i < count($TempArray); $i++) {
            if ($TempArray[$i] == "") continue;
            AddMessage($TempArray[$i], GetUserName($_SESSION["UID"]) . "在" . $RowData[1] . "布置了作业", "Homework.php?HomeworkID=" . $ClassHomeworkID);
        }
    } else {
        CreateErrorText("请填写完整");
        echo "<br />";
    }
}
require_once "Footer.php";
