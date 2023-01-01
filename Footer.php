<?php
echo "<pre>";
echo "\$_COOKIE = ";
print_r($_COOKIE);
echo "\$_SESSION = ";
print_r($_SESSION);
echo "</pre>";
echo "<br />";
echo "</div>";
if (isset($_SESSION["UID"]) && isset($_SESSION["UserName"])) {
    echo "<footer>";
    echo "<a class=\"FooterLink\" href=\"About.php\">关于本站</a>";
    echo "</footer>";
}
echo "<script>";
echo "for (var i = 0; i < document.getElementsByClassName(\"AuthCodePic\").length; i++) document.getElementsByClassName(\"AuthCodePic\")[i].onclick = function() { this.src = \"GetCaptcha.php?r=\" + Math.random(); }";
echo "</script>";
echo "</body>";
echo "</html>";
