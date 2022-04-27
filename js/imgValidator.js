function validateImmagine(maxKb) {
    var fileInput = document.getElementById('inputfile');
    // controlliamo che esiste il file...
    if (fileInput.files.length == 0) return false;
    var filePath = fileInput.value;

    // estensione dei file
    var allowedExtensions =
        /(\.jpg|\.jpeg|\.png)$/i;

    if (!allowedExtensions.exec(filePath)) {
        alert('File NON Valido! Deve essere un immagine .jpg o .png');
        fileInput.value = '';
        return false;
    } else {
        const filesize = fileInput.files.item(0).size;
        var filekb = Math.round((filesize / 1024));
        if (filekb > maxKb) {
            //file troppo grande
            symbol = ["KB", "MB", "GB"];
            index = 0;
            while(maxKb >= 1024){
                maxKb = maxKb/1024
                index++;
            }
            alert('File Troppo grande! Massimo ' + maxKb + symbol[index]);
            fileInput.value = '';
            return false;
        }
        return true;
    }

}