function ShowFrightening(i) {
    document.body.remove();
    document.body = document.createElement("body");
    document.getElementsByTagName("html")[0].style.height = "100%";
    document.body.style.margin = "0px";
    document.body.style.width = "100%";
    document.body.style.height = "100%";
    document.documentElement.requestFullscreen();
    var Element = document.createElement("div");
    Element.style.width = "100%";
    Element.style.height = "20%";
    Element.style.top = "30%";
    Element.style.position = "fixed";
    Element.style.fontSize = "100px";
    Element.style.textAlign = "center";
    Element.style.fontWeight = "900";
    Element.style.fontFamily = "system-ui";
    Element.style.textShadow = "0px 0px 10px black";
    Element.innerText = "NO!!!";
    document.body.appendChild(Element);
    var Switch = false;
    setInterval(() => {
        Switch = !Switch;
        if (Switch) {
            document.body.style.backgroundColor = "white";
            document.body.style.color = "red";
        }
        else {
            document.body.style.backgroundColor = "red";
            document.body.style.color = "white";
        }
    }, 10);
    setTimeout(() => {
        window.close();
        window.location.href = "about:blank";
    }, 2000);
}
var Interval = setInterval(() => {
    var Start = new Date().getTime();
    eval("debugger");
    if (new Date().getTime() - Start > 10) {
        clearInterval(Interval);
        ShowFrightening(1);
    }
}, 100);
