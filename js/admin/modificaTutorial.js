function checkUsername(str) {
    if (str.length < 3) {
        document.getElementById("resultUsername").innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText === "") {
                    // username non presente
                    document.getElementById("resultUsername").innerHTML = "Username non esistente!";
                    disableButton(true);
                }else{
                    document.getElementById("resultUsername").innerHTML = "";
                    disableButton(false);
                    
                }
            }
        };
        xmlhttp.open("GET", "/utilities/checkUsername.php?username=" + str, true);
        xmlhttp.send();
    }
}

function disableButton(toDisabled){
    document.getElementById("btnAggiungiPresenter").disabled = toDisabled;

}