/* Apertura e Chiusura Sidebar */

// Selezione degli elementi
const body = document.querySelector("body"),
    sidebar = body.querySelector(".sidebar"),
    toggle = sidebar.querySelector(".toggleArrow");

// Event listener per il click sul pulsante di toggle
toggle.addEventListener("click", () => {
    sidebar.classList.toggle("close");
});

// Funzione per aggiornare lo stato della barra laterale in base alla larghezza della finestra
function aggiornaSidebar() {
    if (window.innerWidth <= 768) {
        sidebar.classList.add('close');
    } else {
        sidebar.classList.remove('close');
    }
}

window.addEventListener('resize', aggiornaSidebar); // Chiamata a funzione per aggiornare la sidebar ogni volta che la finestra viene selezionata
