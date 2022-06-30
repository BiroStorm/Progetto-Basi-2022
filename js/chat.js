const form = document.querySelector(".formInput");
const inputText = form.querySelector(".textinput");
const btn = form.querySelector(".inviobnt");
const viewMessageBox = document.querySelector(".chat-messages");
const codiceSessione = form.querySelector(".Codice").value;


inputText.onkeyup = () => {
    if (inputText.value != "") {
        btn.disabled = false;
    } else {
        btn.disabled = true;
    }
}
inputText.addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
        event.preventDefault();
        sendNewMessage();
    }
});

// AJAX invio nuovo messaggio
btn.onclick = () => {
    sendNewMessage();
}

function sendNewMessage(text) {
    btn.disabled = true;
    let xhr = new XMLHttpRequest();
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                console.log("nuovo messaggio ricevuto con successo!")
                // inviato con successo!
                inputText.value = "";
                btn.disabled = true;
                viewMessageBox.scrollTop = viewMessageBox.scrollHeight;
            } else {
                console.log(xhr.responseText);
            }
        }
    }
    xhr.open("POST", "/api/addInChat.php", true);
    xhr.withCredentials = true;
    let formData = new FormData(form);
    xhr.send(formData);
}

var offset = 0;
function loadFirstTime() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "/api/getChatMsg.php", true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 204) {
                viewMessageBox.innerHTML = "";
            } else if (xhr.status === 200) {
                let response = JSON.parse(xhr.response);
                let data = response[1];
                offset = response[0];
                viewMessageBox.innerHTML = data;
                viewMessageBox.scrollTop = viewMessageBox.scrollHeight;
            } else if (xhr.status === 405) {
                // Chat Chiusa!
                location.reload();
            }
        }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("Offset=" + offset + "&SessionID=" + codiceSessione);
}
loadFirstTime();



//after the first time
setInterval(() => {
    // controllo se la chat è stata chiusa:



    let xhr = new XMLHttpRequest();
    xhr.open("POST", "/api/getChatMsg.php", true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 204) {
                // non ci sono nuovi aggiornamenti
                return;
            } else if (xhr.status === 200) {
                let response = JSON.parse(xhr.response);
                let data = response[1];
                offset = response[0];
                viewMessageBox.innerHTML += data;
            } else if (xhr.status === 405) {
                // Chat Chiusa!
                location.reload();
            }
        }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("Offset=" + offset + "&SessionID=" + codiceSessione);
}, 2000);