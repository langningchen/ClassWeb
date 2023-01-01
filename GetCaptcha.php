<?php
require_once "Function.php";
if (!isset($_SERVER["HTTP_REFERER"]) || strpos($_SERVER["HTTP_REFERER"], $_SERVER["SERVER_NAME"]) === false) {
    die();
}
header("content-type:image/png");
echo GetCaptcha();
