function checkForm() {
    if (!validateImmagine(1024)) return disableButton();
    // quindi è valido l'img
    
    var nomeSponsor = document.getElementById('nomeSponsor');
    if (nomeSponsor.value.length < 3) return disableButton();

    //allora possiamo attivare il bottone:

    document.getElementById('creabtn').disabled = false;

}

function disableButton() {

    document.getElementById('creabtn').disabled = true;
    return false;
}