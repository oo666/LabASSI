/* Apertura e Chiusura Sidebar */

const body = document.querySelector("body"),
    sidebar = body.querySelector(".sidebar"),
    toggle = sidebar.querySelector(".toggleArrow");


toggle.addEventListener("click", () => {
    sidebar.classList.toggle("close");
});

function aggiornaSidebar() {
    if (window.innerWidth <= 768) {
        sidebar.classList.add('close');
    } else {
        sidebar.classList.remove('close');
    }
}
window.addEventListener('resize', aggiornaSidebar);

//prende l'orario con la data attuale
function getCurrentTime() {
    var months = ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'];
    var now = new Date();
    var hours = now.getHours().toString().padStart(2, "0");
    var minutes = now.getMinutes().toString().padStart(2, "0");

    var monthsIndex = now.getMonth();
    var day = now.getDate();


    return day + " " + months[monthsIndex] + " " + hours + ":" + minutes;
}

//creo una lista dove verranno caricati tutti i messaggi del database
var messaggiDB = [];

//stabilisco a chi verrà assegnata la nofica sfruttando i messaggi caricati nella lista dal database
window.addEventListener('load', function() {
    for (var i = 0; i < messaggiDB.length; i++) {

        var msg = messaggiDB[i];
        var username_contatti_php = msg.username_contatti;
        var username_utente_php = msg.username_utente;
        var visualizzato = msg.visualizzato;
        
        //gestisce se impostare la notifica o no
        if(visualizzato == 'no'){
            aggiungiNotifica(username_utente_php, username_contatti_php);
        }
    }
});

var username_contatti = null;

var username_utente = null;
var elementi = document.querySelectorAll('.input');
elementi.forEach(function (elemento) {
    // ottengo l'username dell'utente attualmente loggato
    username_utente = elemento.value;
});

//disabilita "send-message" visto che all'inizio della pagina non è selezionato nessun contatto
var inputField = document.getElementById('message-input');
inputField.disabled = true;


//-----------------CHAT-----------------------------------------


// connessione al server WebSocket
var socket = new WebSocket("ws://localhost:8080/Sprotz/chat/chat.php");


// evento di apertura della connessione WebSocket
socket.onopen = function (event) {
    // console.log(socket.readyState);
    console.log("Connessione al server WebSocket aperta");
};

// evento di ricezione di un messaggio dal server WebSocket
socket.onmessage = function (event) {
    //fa una verifica se event è un array o no
    // questo per differenziare l'arrivo della lista con tutti i messaggi da un messaggio normale
    if (Array.isArray(JSON.parse(event.data))) {
        //vengono caricati tutti i messaggi
        messaggiDB = JSON.parse(event.data);

    } else {
        var jsonData = JSON.parse(event.data);
        // suddivido in più pezzi
        var messaggio = jsonData.messaggio;
        var username_contatti_php = jsonData.username_contatti;
        var username_utente_php = jsonData.username_utente;

        // faccio un controllo a chi deve comparire il messaggio
        //se l'utente loggato si trova già nella chat con l'interlucore allora stampa il messaggio e lo salva nella lista
        // sennò lo salva solo nella lista e aggiunge la notifica
        if (username_utente_php == username_contatti && username_contatti_php == username_utente) {
            messaggiDB.push(JSON.parse(event.data));
            ricezioneMessaggio(messaggio, getCurrentTime());
            aggiornaNotifica();
        } else {
            messaggiDB.push(JSON.parse(event.data));
            aggiungiNotifica(username_utente_php, username_contatti_php);
        }
    }
};

// evento di chiusura della connessione WebSocket
socket.onclose = function (event) {
    console.log("Connessione al server WebSocket chiusa");
};
//--------------------------------------------------------------------------------------------------

//appena si clicca il tasto per inviare il messaggio
document.getElementById("send-Button").addEventListener("click", function (event) {
    event.preventDefault();

    //prende il messaggio scritto
    var messaggio = document.getElementById("message-input").value;
    var time_stamp = getCurrentTime();

    var jsonData = {
        messaggio: messaggio,
        username_contatti: username_contatti,
        username_utente: username_utente,
        time_stamp: time_stamp
    };

    //inserisce il messaggio nella lista
    messaggiDB.push(jsonData);

    //stampa il messaggio inviato
    stampaMessaggio(messaggio, time_stamp);
    //invia il messaggio al server
    inviaMessaggio(messaggio, username_contatti, username_utente);
});


//--------------------------------MESSAGGIO_CHE_INVIO----------------------------------------------------------------------------

// funzione per inviare un messaggio al server WebSocket
function inviaMessaggio(messaggio, username_contatti, username_utente) {
    if (socket.readyState === WebSocket.OPEN) {
        var dataToSend = messaggio + "|" + username_contatti + "|" + username_utente + "|" + getCurrentTime();
        socket.send(dataToSend);
    } else {
        console.error("Impossibile inviare il messaggio: la connessione WebSocket non è aperta");
    }
}

function stampaMessaggio(messaggio, time_stamp) {
    //crea un div da inserire nella chat
    var messageElement = document.createElement("div");
    messageElement.classList.add("inviato");
    messageElement.classList.add("bubble-orange");
    messageElement.innerHTML = "<span class='message-text' style='color: white'>" + "</strong>" + messaggio + "</span>";


    var timeElement = document.createElement("span");
    timeElement.classList.add("message-time");
    timeElement.textContent = time_stamp; // ottengo l'ora corrente

    // aggiungo l'elemento del tempo alla "nuvoletta" del messaggio
    messageElement.appendChild(timeElement);
    // aggiungo l'elemento alla chat box
    document.getElementById("box-area").appendChild(messageElement);

    //setto la stringa vuota nella casella di invio messaggi
    document.getElementById('message-input').value = '';
}
//---------------------------------------------------------------------------------------------------------------



//--------------------------------MESSAGGIO_CHE_RICEVO----------------------------------------------------------------------------
function ricezioneMessaggio(messaggio, time_stamp) {
    var message = messaggio;
    //crea un div da inserire nella chat
    var messageElement = document.createElement("div");
    messageElement.classList.add("bubble-gray");
    messageElement.innerHTML = "<span class='message-text' style='color: white'>" + "</strong>" + message + "</span>";

    var timeElement = document.createElement("span");
    timeElement.classList.add("message-time");
    timeElement.textContent = time_stamp; // ottengo l'ora corrente

    // aggiungo l'elemento del tempo alla "nuvoletta" del messaggio
    messageElement.appendChild(timeElement);
    // aggiungo l'elemento alla chat box
    document.getElementById("box-area").appendChild(messageElement);
}
//------------------------------------------------------------------------------------------------------------






// Aggiungi un gestore di eventi a tutti i contatti che un utente ha nella lista che si vede nel sito
var userItems = document.querySelectorAll('.click-utente');
userItems.forEach(function (item) {
    item.addEventListener('click', function () {

        username_contatti = item.getAttribute('data-value');

        //rimuovo la notifica dal contatto cliccato e sovrascrivo nel database
        rimuovoNotifica(item);
        aggiornaNotifica();

        //ottengo il nome del contatto cliccato e lo inserisco sopra la chat box
        var username = item.textContent;
        var outputDiv = document.getElementById('utente-chat');
        outputDiv.textContent = username;

        // prendo il nome sopra la chat-box e la casella dove si inseriscono i messaggi
        var divContent = document.getElementById('utente-chat');
        var inputField = document.getElementById('message-input');

        // Verificare se il nome sopra la chat-box corrisponde a 'Utente'
        if (divContent.textContent.trim() !== 'Utente') {
            //se non corrisponde attivo la casella dove inserire i messaggi
            inputField.disabled = false;
        }

        //resetta la box-area
        var divContenitore = document.getElementById('box-area');
        // rimuove tutti i child del div
        while (divContenitore.firstChild) {
            divContenitore.removeChild(divContenitore.firstChild);
        }

        //sovrascrivo la nuova chat prendendo tutti i messaggi dalla lista
        for (var i = 0; i < messaggiDB.length; i++) {

            //suddivido i dati
            var msg = messaggiDB[i];
            var messaggio = msg.messaggio;
            var username_contatti_php = msg.username_contatti;
            var username_utente_php = msg.username_utente;
            var time_stamp = msg.time_stamp;

            //stabilisco come vengono stampati i messaggi, cioè quali sono i messaggi ricevuti e quali inviati
            if (username_utente_php == username_utente && username_contatti == username_contatti_php) {
                stampaMessaggio(messaggio, time_stamp);
            }
            if (username_utente_php == username_contatti && username_contatti_php == username_utente) {
                ricezioneMessaggio(messaggio, time_stamp);
            }
        }
    });
});


// ------------NOTIFICA------------------

function aggiungiNotifica(username_utente_php, username_contatti_php) {
    var listItem = document.querySelectorAll('[id="contatto"]');
    // converte la NodeList in un array
    listItem.forEach(function (elemento) {
        
        if(elemento.getAttribute('data-value') == username_utente_php){
            var pallini = elemento.querySelector('p');
        //se il contatto non ha "pallini" cioè la notifica segnalata allora la aggiunge
        if (pallini == null) {
            var notifica = document.createElement("p");
            notifica.classList.add("title");
            notifica.setAttribute("id", "id_della_notifica");

            // seleziona tutti gli elementi con attributo data-value
            var elementiConDataValue = document.querySelectorAll('[data-value]');

            // converte la NodeList in un array
            var arrayDataValue = Array.from(elementiConDataValue);

            // stabilisce a chi assegnare la notifica
            arrayDataValue.forEach(function (elemento) {
                if (username_utente_php == elemento.getAttribute('data-value') && username_contatti_php == username_utente) {
                    elemento.appendChild(notifica);
                }
            });
        }
        }
        
    });

}
//rimuove la notifica
function rimuovoNotifica(item){
    var pallini = item.querySelectorAll('p');
        if(pallini != null){
            pallini.forEach(function (pallino) {
                pallino.remove();
            });
        }
}

// aggiorna la notifica nel database
function aggiornaNotifica() {
    if (socket.readyState === WebSocket.OPEN) {
        var dataToSend = "si" + "|" + username_contatti + "|" + username_utente;
        socket.send(dataToSend);
        // console.log("Messaggio inviato al server WebSocket:", dataToSend);
    } else {
        console.error("Impossibile inviare il messaggio: la connessione WebSocket non è aperta");
    }
}
// ---------------------------------------------------------