var HttpRequest;
if (window.XMLHttpRequest)
    HttpRequest = new XMLHttpRequest();
else
    HttpRequest = new ActiveXObject("Microsoft.XMLHTTP");
HttpRequest.onreadystatechange = function () {
    if (HttpRequest.readyState == 4)
        if (HttpRequest.status != 200 || HttpRequest.responseText != "OK")
            console.log("统计失败：" + " " + HttpRequest.status + " " + HttpRequest.responseText);
}
HttpRequest.open("GET", "Count.php?URI=" + String(window.location.href), true);
HttpRequest.send();
