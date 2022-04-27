// AJAX SECTION FOR VALIDATION Username
function checkUsername(str) {
    if (str.length == 0) {
        document.getElementById("result").innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("result").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "/utilities/checkUsername.php?q=" + str, true);
        xmlhttp.send();
    }
}

function checkForm() {

    var nomeSponsor = document.getElementById('nomeSponsor').value;
    if (nomeSponsor.length < 3 && document.getElementById("result").value == "") return disableButton();


    var nomeSponsor = document.getElementById('cognomeSponsor');
    if (nomeSponsor.value.length < 3) return disableButton();

    /*
    var nomeSponsor = document.getElementById('dataNascita');
    if (nomeSponsor.value.length < 3) return disableButton();
    */
    var nomeSponsor = document.getElementById('luogoNascita');
    if (nomeSponsor.value.length < 5) return disableButton();

    if (checkPass() && isValidPass()) document.getElementById('registerbtn').disabled = false;

}

function disableButton() {
    document.getElementById('registerbtn').disabled = true;
    return false;
}

function isValidPass() {
    var pass1 = document.getElementById('pass1').value;
    var regex = /^(?!.*\s)(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[~`!@#$%^&*()--+={}\[\]|\\:;"'<>,.?/_₹]).{8,}$/;

    if (!regex.test(pass1)) {
        document.getElementById('firstPass').innerHTML = "Deve contenere almeno 1 numero, 1 lettera maiuscola e minuscola, deve essere lungo almeno 8 caratteri!";
        return disableButton();
    } else {
        document.getElementById('firstPass').innerHTML = "";
        return true;
    }
}

function checkPass() {
    var pass1 = document.getElementById('pass1').value;
    var pass2 = document.getElementById('pass2').value;
    
    var text = document.getElementById('validPass');

    if (pass1 != pass2) {
        text.innerHTML = "Password diversi!";
        text.className += "form-text text-danger";
        return disableButton();
    } else {
        text.innerHTML = "Password Valido!";
        text.className += "form-text text-success";
        return true;
    }

}