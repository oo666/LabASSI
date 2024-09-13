/* Apertura e Chiusura Sidebar */

// Seleziona gli elementi necessari
const body = document.querySelector("body"),
    sidebar = body.querySelector(".sidebar"),
    toggle = sidebar.querySelector(".toggleArrow");

// Aggiungi un evento click al toggle per aprire/chidere la sidebar
toggle.addEventListener("click", () =>{
    sidebar.classList.toggle("close");
});

// Funzione per aggiornare lo stato della sidebar in base alla larghezza della finestra
function aggiornaSidebar(){
    if(window.innerWidth <= 768){
        sidebar.classList.add('close');
    } else {
        sidebar.classList.remove('close');
    }
}

// Aggiorna la sidebar quando la finestra viene ridimensionata o caricata
window.addEventListener('resize', aggiornaSidebar);
window.onload = aggiornaSidebar();
window.addEventListener('DOMContentLoaded', function() {
    // Esegui la funzione aggiornaSidebar quando la pagina è completamente caricata
    aggiornaSidebar();
});
window.addEventListener('resize', function() {
    // Esegui la funzione aggiornaSidebar quando la finestra viene ridimensionata
    aggiornaSidebar();
});

// Funzione per mostrare il popup del quiz
function mostraQuiz(button) {
    // Mostra il popup del quiz e l'overlay
    document.getElementById("quizPopup").style.display = "block";
    document.getElementById("overlay").style.display = "flex";

    // Ottieni i dati del profilo dell'utente e del quiz dal pulsante
    var domanda = button.getAttribute('data-domanda');
    var profiloUsername = button.getAttribute('data-username');
    var profiloBio = button.getAttribute('data-bio');
    var profiloFoto = button.getAttribute('data-foto');
    var risposte = [
        button.getAttribute('data-risposta1'),
        button.getAttribute('data-risposta2'),
        button.getAttribute('data-risposta3'),
        button.getAttribute('data-risposta4')
    ];
    var rispostaCorretta = button.getAttribute('data-risposta-corretta');

    // Mescola le risposte
    var risposte = mescolaArray(risposte);

    // Aggiorna i contenuti del popup del quiz con i dati ottenuti
    var questionElement = document.querySelector('.popup .question');
    questionElement.textContent = domanda;
    var usernameElement = document.querySelector('.popup .username');
    usernameElement.textContent = profiloUsername;
    var bioElement = document.querySelector('.popup .bio');
    bioElement.textContent = profiloBio;
    var fotoElement = document.querySelector('.popup .foto');
    fotoElement.src = profiloFoto;

    // Aggiorna le risposte nel popup del quiz
    var labels = document.querySelectorAll('.popup input[type="radio"] + label');
    labels.forEach(function(label, index) {
        label.textContent = risposte[index];
    });

    // Aggiungi event listener per verificare la correttezza delle risposte selezionate
    labels.forEach(function(label, index) {
        label.textContent = risposte[index];
        label.classList.remove('success', 'error');
        label.addEventListener('click', function() {
            if (label.textContent === rispostaCorretta) {
                label.classList.add('success');
            } else {
                label.classList.add('error');
            }
        });
    });

    // Funzione per mescolare un array
    function mescolaArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }
}

// Funzione per chiudere il popup del quiz
function chiudiQuiz() {
    // Nascondi il popup del quiz e l'overlay
    document.getElementById("quizPopup").style.display = "none";
    document.getElementById("overlay").style.display = "none";

    // Deseleziona tutte le risposte
    var radioButtons = document.querySelectorAll('.popup input[type="radio"]');
    radioButtons.forEach(function(radioButton) {
        radioButton.checked = false;
    });

    // Ricarica la pagina
    location.reload();
}

// Funzione per inviare la risposta selezionata
function submitForm() {
    // Ottieni lo username e la risposta selezionata e impostali nei campi nascosti del modulo
    var username = document.querySelector('.popup .username').textContent;
    document.getElementById("usernameHidden").value = username;
    var rispostaSelezionata = document.querySelector('.popup input[type="radio"]:checked').nextElementSibling.textContent;
    document.getElementById("rispostaHidden").value = rispostaSelezionata;
    // Invia il modulo
    document.getElementById("myForm").submit();
}


