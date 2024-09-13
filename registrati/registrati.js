// Funzione che controlla che i vari campi vengano inseriti correttamente
document.getElementById("registrazione").addEventListener("submit", function (event) {
    event.preventDefault();

    var password1 = document.getElementById("password1").value;
    var confermaPassword = document.getElementById("confermaPassword").value;

    var email = document.getElementById("email").value;

    if ((password1 !== confermaPassword)) {                                                                                   // controllo su password
        alertMessage('Le password non corrispondono!');
    } else if (verifica_minorenne()) {                                                                                         // controllo su etò
        alertMessage('Devi essere maggiorenne per registrarti!');
    } else if (!controllaEmail(email)) {                                                                                     // controllo su email        
        alertMessage('Email non valida!');
    } else if (controllaPassword(password1)) {                                                                                 // controllo sulla password
        alertMessage('La password deve contenere almeno 8 caratteri, di cui almeno una lettera maiuscola e un numero!')
    } else {
        this.submit();
    }
});

// Funzione che segna il giorno, il mese e l'anno in cui ci si sta registrando
function getDataOggi() {
    var oggi = new Date();
    var giorno = oggi.getDate();
    var mese = oggi.getMonth() + 1;
    var anno = oggi.getFullYear();
    var data_oggi = [giorno, mese, anno];
    return data_oggi;
}

// Funzione che verifica se l'utente che si sta registrando è maggiorenne
function verifica_minorenne() {
    var data = new Date(document.getElementById('dataNascita').value);
    var data_oggi = getDataOggi();
    if (data_oggi[2] - data.getFullYear() < 18) {
        return true;
    }
    if (data_oggi[2] - data.getFullYear() === 18 && data_oggi[1] < (data.getMonth() + 1)) {
        return true;
    }
    if (data_oggi[2] - data.getFullYear() === 18 && data_oggi[1] === (data.getMonth() + 1) && data_oggi[0] < data.getDate()) {
        return true;
    }
    return false;
}

// Funzione che verifica con una regex se la mail inserita è scritta in maniera corretta
function controllaEmail(email) {
    var regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
    return regex.test(email);
}

// Funzione che verifica con una regex se la password inserita è scritta secondo i criteri imposti
function controllaPassword(password1) {
    var regex = /(?=.*[A-Z])(?=.*\d).*/;
    if (password1.length < 8) {
        return true;
    }
    if (regex.test(password1)) {
        return false;
    }
}

// Funzione che mostra all'utente il messaggio d'errore in base all'errore commesso durante la registrazione
function alertMessage(message) {
    document.querySelector(".warning__title").innerText = message;
    var audioError = document.getElementById("audio-error");

    showPopup(); // Mostra il popup

    audioError.play(); // Suono d'errore

    setTimeout(function () {
        hidePopup(); // Nasconde il popup dopo 5 secondi
    }, 5000);
}

function showPopup() {
    document.getElementById("popup").style.display = "flex";
    document.getElementById("overlay").style.display = "flex";
}

function hidePopup() {
    document.getElementById("popup").style.display = "none";
    document.getElementById("overlay").style.display = "none";
}





