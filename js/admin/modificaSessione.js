const ul = document.getElementById("listahashtag"),
    input = document.getElementById("hashtaginput");

var tags = [];

function updateOutputHashtag(){
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
    if (element.key == "Enter") {
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