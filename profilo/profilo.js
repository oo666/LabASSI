/* Apertura e Chiusura Sidebar */

const body = document.querySelector("body"),
    sidebar = body.querySelector(".sidebar"),
    toggle = sidebar.querySelector(".toggleArrow");
    

toggle.addEventListener("click", () =>{
    sidebar.classList.toggle("close");
});

function aggiornaSidebar(){
    if(window.innerWidth <= 768){
        sidebar.classList.add('close');
    } else {
        sidebar.classList.remove('close');
    }
}

window.addEventListener('resize', aggiornaSidebar);

document.addEventListener("DOMContentLoaded", function () {
    var textArea = document.getElementById('textarea');
    var maxCharacters = 150; // Numero massimo di caratteri consentiti
    var characterCount = document.getElementById('characterCount');

    textArea.addEventListener('input', function () {

        updateCharacterCount();

        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';

        // Ridimensiona il testo all'interno della textarea
        adjustFontSize(this);
    });

    function adjustFontSize(element) {
        // Imposta la dimensione del carattere in base all'altezza della textarea
        var maxHeight = parseInt(window.getComputedStyle(element).height, 10);
        var currentHeight = element.scrollHeight;
        var ratio = currentHeight / maxHeight;
        var fontSize = 13 / ratio; // Imposta la dimensione del carattere di base
        element.style.fontSize = fontSize + 'px';
    }

    function updateCharacterCount() {
        // Limita il numero di caratteri a 150
        if (textArea.value.length > maxCharacters) {
            textArea.value = textArea.value.substring(0, maxCharacters);
        }

        // Aggiorna il contatore dei caratteri rimanenti
        var remainingCharacters = maxCharacters - textArea.value.length;
        characterCount.textContent = 'Caratteri rimanenti: ' + remainingCharacters;
    }

});


const textArea = document.getElementById('textarea');

textArea.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
        event.preventDefault(); // Impedisce il comportamento predefinito del tasto "Invio"
    }
});

//Quando una foto profilo è selezionata, mostra la foto profilo, altrimenti mostra un'immagine predefinita
document.getElementById("file").onchange = function (e) {
    var fileText = document.getElementById('file-text');
    fileText.style.display = 'none';
    var fileIcon = document.getElementById('file-icon');
    fileIcon.style.display = 'none';
    var imagePreview = document.getElementById('image-preview');
    imagePreview.style.display = 'block';
    var reader = new FileReader();
    reader.onload = function () {
        var preview = document.getElementById('image-preview');
        preview.src = reader.result;
    };
    reader.readAsDataURL(e.target.files[0]);
};

//Mostra l'anteprima dell'immagine profilo caricata
function setPreviewImage() {
    var imgSrc = document.getElementById("image-preview").src;
    if (imgSrc === "") {
        document.getElementById("image-preview-container").style.display = "none";
    } else {
        document.getElementById("image-preview-container").style.display = "block";
    }

}

document.addEventListener("DOMContentLoaded", function() {
    // Seleziona il pulsante di condivisione su Facebook
    const shareFacebookButton = document.getElementById('shareFacebook');

    // URL del profilo utente (assicurati di cambiare questo URL con quello dinamico del tuo sito)
    const userProfileUrl = window.location.href; // O utilizza un URL specifico se diverso

    // Aggiungi l'evento click per il pulsante di condivisione
    shareFacebookButton.addEventListener('click', function() {
        // Crea l'URL di condivisione di Facebook
        const facebookShareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(userProfileUrl)}`;

        // Apri una finestra di dialogo per la condivisione
        window.open(facebookShareUrl, '_blank', 'width=600,height=400');
    });
});

// Chiama la funzione al caricamento della pagina
window.onload = setPreviewImage;