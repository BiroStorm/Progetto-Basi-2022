function likePresentazione(value, codice, element) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText != "") {
                console.log(this.responseText);
                document.getElementById("result").textContent = this.responseText;
            }
        }
    };
    xmlhttp.open("POST", "/api/PrefPresentazione.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.withCredentials = true;
    xmlhttp.send("Codice=" + codice +"&add=" + value);
    if (value) {
        // cambiamo in cuore pieno
        element.className = "bi bi-heart-fill fs-3 text-danger fullheart";
        element.setAttribute("onclick", "likePresentazione(0, this)");
    } else {
        // da cuore pieno a cuore vuoto
        element.className = "bi bi-heart fs-3 emptyheart";
        element.setAttribute("onclick", "likePresentazione(1, this)");
    }
}