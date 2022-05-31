const ul = document.getElementById("listahashtag"),
    input = document.getElementById("hashtaginput");

var tags = [];

function updateOutputHashtag() {
    output = document.getElementById("outputHashtag");
    output.value = tags.toString();

}

function creaTag() {
    ul.querySelectorAll("li").forEach(li => li.remove());
    tags.slice().reverse().forEach(tag => {
        let liTag = `<li>${tag} <i class="uit uit-multiply" onclick="remove(this, '${tag}')"></i></li>`;
        ul.insertAdjacentHTML("afterbegin", liTag);
    });
    updateOutputHashtag();
}

function aggiungiTag(element) {
    if (element.key == "Enter" || element.key == " ") {
        let hashtag = element.target.value.replace(/\s+/g, ' ');
        if (hashtag.length > 1 && !tags.includes(hashtag)) {

            tags.push(hashtag);
            creaTag();
        }
        element.target.value = "";
    }

}

function remove(element, tag) {
    let index = tags.indexOf(tag);
    tags = [...tags.slice(0, index), ...tags.slice(index + 1)];
    element.parentElement.remove();
    updateOutputHashtag();
}

input.addEventListener("keyup", aggiungiTag);


////////////////////////// Gestione Autori /////////////
const outdiv = document.getElementById("autoridiv");
const addAutoreBtn = document.getElementById("newAutoreBtn");
const clone = document.getElementById("clonehere");
const copia = clone.cloneNode(true);

function aggiungiInput(element) {
    element.classList.remove("btn-success");
    element.classList.add("btn-danger");
    element.innerText = "Rimuovi Autore";

    element.setAttribute("onclick", "rimuoviInput(this)");
    outdiv.appendChild(copia.cloneNode(true));
}

function rimuoviInput(element) {
    element.parentElement.remove();
}


////////////////////////// Modifica Dati Sessione /////////////
const inputs = document.querySelectorAll("#datiSess");
for (const input of inputs) {
    input.oldValue = input.value;
}

// Declares function and call it directly
var setEnabled = function () {
    var e = true;
    for (const input of inputs) {
        if (input.oldValue !== input.value) {
            e = false;
            break;
        }
    }
    document.querySelector("#modificaSess").disabled = e;
};

setEnabled();