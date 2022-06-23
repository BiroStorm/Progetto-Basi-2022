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
        xmlhttp.open("GET", "/utilities/checkUsername.php?username=" + str, true);
        xmlhttp.send();
    }
}

function checkForm() {

    var nome = document.getElementById('nome').value;
    if (nome.length < 2 || document.getElementById("result").value == "") return disableButton();

    console.log("ok nome");
    var cognome = document.getElementById('cognome');
    console.log("controllo cognome...")
    if (cognome.value.length < 2) return disableButton();

    console.log("ok cognome");
    var luogoNascita = document.getElementById('luogoNascita');
    console.log("controllo Luogo...")
    if (luogoNascita.value.length < 3) return disableButton();

    console.log("ok luogo");

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
        document.getElementById('firstPass').innerHTML = "Deve contenere almeno 1 numero, 1 lettera maiuscola e minuscola e un simbolo, deve essere lungo almeno 8 caratteri!";
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