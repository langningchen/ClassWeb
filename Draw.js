ThisDrawObject = null;
ThisHomeworkUploadID = 0;
function ClearImage() {
    var ObjectWidth = ThisDrawObject.width;
    var ObjectHeight = ThisDrawObject.height;
    var CanvasDiv = document.getElementById("CanvasDiv");
    var CanvasDivContext = CanvasDiv.getContext("2d");
    CanvasDiv.height = window.innerHeight - 100;
    CanvasDiv.width = CanvasDiv.height / ObjectHeight * ObjectWidth;
    CanvasDiv.style["position"] = "fixed";
    if (CanvasDiv.width > window.innerWidth) {
        CanvasDiv.width = window.innerWidth;
        CanvasDiv.height = CanvasDiv.width / ObjectWidth * ObjectHeight;
        CanvasDiv.style["top"] = (window.innerHeight - 100 - CanvasDiv.height) / 2 + "px";
    }
    else {
        CanvasDiv.style["left"] = (window.innerWidth - CanvasDiv.width) / 2 + "px";
    }
    CanvasDiv.getContext("2d").drawImage(ThisDrawObject, 0, 0, CanvasDiv.width, CanvasDiv.height);
    CanvasDivContext.strokeStyle = "red";
    CanvasDivContext.lineWidth = 3;
}
function UploadImage() {
    var HttpRequest;
    if (window.XMLHttpRequest) {
        HttpRequest = new XMLHttpRequest();
    } else {
        HttpRequest = new ActiveXObject("Microsoft.XMLHTTP");
    }
    HttpRequest.onreadystatechange = function () {
        if (HttpRequest.readyState == 4) {
            if (HttpRequest.status == 200 && HttpRequest.responseText == "OK") {
                DrawDiv.parentNode.removeChild(DrawDiv);
            } else {
                alert("提交失败：" + HttpRequest.responseText);
            }
        }
    }
    var canvas = document.getElementById("CanvasDiv");
    var img = canvas.toDataURL();
    HttpRequest.open("POST", "UploadCorrectPicture.php", true);
    HttpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    HttpRequest.send("HomeworkUploadID=" + ThisHomeworkUploadID + "&" + "Image=" + img);
}
function Draw(object, ID) {
    ThisDrawObject = object;
    ThisHomeworkUploadID = ID;
    var DrawDiv = document.createElement("div");
    DrawDiv.style.position = "fixed";
    DrawDiv.style.left = "0%";
    DrawDiv.style.top = "0%";
    DrawDiv.style.width = "100%";
    DrawDiv.style.height = "100%";
    DrawDiv.id = "DrawDiv";
    DrawDiv.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    var CloseDiv = document.createElement("div");
    CloseDiv.style.position = "fixed";
    CloseDiv.style.right = "10px";
    CloseDiv.style.top = "10px";
    CloseDiv.style.margin = "5px";
    CloseDiv.style.zIndex = "2";
    CloseDiv.innerHTML = "";
    CloseDiv.innerHTML += "<input type=\"button\" onclick=\"DrawDiv.parentNode.removeChild(DrawDiv);\" class=\"DangerousButton\" value=\"取消批改\" />";
    CloseDiv.innerHTML += "<br />";
    CloseDiv.innerHTML += "<input type=\"button\" onclick=\"UploadImage()\" class=\"MainButton\" value=\"提交批改\" />";
    DrawDiv.appendChild(CloseDiv);
    var ChangeDiv = document.createElement("div");
    ChangeDiv.innerHTML = "";
    ChangeDiv.innerHTML += "<input type=\"button\" onclick=\"document.getElementById('CanvasDiv').getContext('2d').strokeStyle='red'  ; \" style=\"color: red;   background-color: red;   \" />";
    ChangeDiv.innerHTML += "<input type=\"button\" onclick=\"document.getElementById('CanvasDiv').getContext('2d').strokeStyle='green'; \" style=\"color: green; background-color: green; \" />";
    ChangeDiv.innerHTML += "<input type=\"button\" onclick=\"document.getElementById('CanvasDiv').getContext('2d').strokeStyle='blue' ; \" style=\"color: blue;  background-color: blue;  \" />";
    ChangeDiv.innerHTML += "<input type=\"button\" onclick=\"document.getElementById('CanvasDiv').getContext('2d').strokeStyle='black'; \" style=\"color: black; background-color: black; \" />";
    ChangeDiv.innerHTML += "<input type=\"button\" onclick=\"document.getElementById('CanvasDiv').getContext('2d').strokeStyle='white'; \" style=\"color: white; background-color: white; \" />";
    ChangeDiv.innerHTML += "<input type=\"button\" onclick=\"ClearImage()                                                             ; \" style=\"color: black; background-color: white; \" value=\"清空\" />";
    DrawDiv.appendChild(ChangeDiv);
    var CanvasDiv = document.createElement("canvas");
    CanvasDiv.id = "CanvasDiv";
    CanvasDiv.innerHTML = "您的浏览器不支持画布";
    DrawDiv.appendChild(CanvasDiv);
    document.body.appendChild(DrawDiv);
    ClearImage();
    var CanvasDivContext = CanvasDiv.getContext("2d");
    CanvasDiv.onmousedown = function (e) {
        var ev = window.event || e;
        var jiu_left = ev.layerX || ev.offsetX;
        var jiu_top = ev.layerY || ev.offsetY;
        CanvasDivContext.beginPath();
        CanvasDivContext.moveTo(jiu_left, jiu_top);
        CanvasDiv.onmousemove = function (e) {
            var ev = window.event || e;
            var now_left = ev.layerX || ev.offsetX;
            var now_top = ev.layerY || ev.offsetY;
            if (ev.type == "touchmove") {
                now_left = ev.changedTouches[0].pageX - CanvasDiv.offsetLeft;
                now_top = ev.changedTouches[0].pageY - CanvasDiv.offsetTop;
                console.log(pageXOffset, pageYOffset);
            }
            CanvasDivContext.lineTo(now_left, now_top);
            CanvasDivContext.stroke();
        }
        CanvasDiv.addEventListener('touchmove', CanvasDiv.onmousemove, false);
    }
    CanvasDiv.addEventListener('touchstart', CanvasDiv.onmousedown, false);
    CanvasDiv.onmouseup = function () {
        CanvasDiv.onmousemove = null;
    }
    CanvasDiv.addEventListener('touchend', CanvasDiv.onmouseup, false);
}