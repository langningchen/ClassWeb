<?php
require_once "NotLogin.php";
require_once "Header.php";
$Count = 10;
if ($_SESSION["UserType"] != 2) {
    CreateText("非法调用");
    require_once "Footer.php";
    die();
}
if (isset($_GET["Count"])) {
    if ($_GET["Count"] > 100) {
        CreateText("非法调用");
        require_once "Footer.php";
        die();
    }
    $Count = $_GET["Count"];
}
echo "<script>";
echo "function GO() {";
echo "    var NewURL = new URL(window.location.href);";
echo "    NewURL.search = \"?Count=\" + document.getElementById(\"ShowNumber\").value;";
echo "    window.location.href = NewURL.toString();";
echo "}";
echo "</script>";
CreateText("显示数量");
echo "<input class=\"Input\" type=\"number\" min=\"1\" max=\"100\" value=\"" . $Count . "\" id=\"ShowNumber\" />";
echo "<input type=\"button\" class=\"MainButton\" onclick=\"GO();\" value=\"确定\" />";
echo "<table>";
echo "<thead>";
echo "<td style=\"width: 0%\">";
CreateText("图片验证码");
echo "</td>";
echo "<td style=\"width: 100%\">";
CreateText("答案");
echo "</td>";
echo "</thead>";
echo "<tbody>";
for ($i = 0; $i < $Count; $i++) {
    echo "<tr>";
    echo "<td>";
    echo "<img class=\"AuthCodePic\" src=\"data:image/png;base64,";
    echo base64_encode(GetCaptcha());
    echo "\" />";
    echo "</td>";
    echo "<td>";
    CreateText($_SESSION["AuthCode"]);
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
require_once "Footer.php";
