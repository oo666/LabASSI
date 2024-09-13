// Aggiunge un event listener al form con id "accedi" quando viene inviato
document.getElementById("accedi").addEventListener("submit", function(event){
    // Recupera il valore dell'email e della password inseriti dall'utente
    var email = document.getElementById("email").value;
    var password = document.getElementById("password1").value;

    // Controlla se il campo password è vuoto
    if(password === ''){
        // Mostra un messaggio di avviso
        alertMessage('Inserire una password!');
    } else if (!controllaEmail(email)) { // Controlla se l'email è valida
        // Mostra un messaggio di avviso
        alertMessage('Email non valida!');
    } else {
        // Se entrambi i campi sono validi, invia il form
        this.submit();
    }
});

// Funzione per mostrare un messaggio di avviso
function alertMessage(message){
    // Imposta il testo del messaggio di avviso
    document.querySelector(".warning__title").innerText = message;
    // Recupera l'elemento audio per riprodurre il suono di errore
    var audioError = document.getElementById("audio-error");

    // Mostra il popup di avviso
    showPopup();
    // Riproduce il suono di errore
    audioError.play();
    
    // Nasconde il popup dopo 5 secondi
    setTimeout(function() {
        hidePopup();
    }, 5000);
}

// Funzione per mostrare il popup di avviso
function showPopup() {
    document.getElementById("popup").style.display = "flex";
}

// Funzione per nascondere il popup di avviso
function hidePopup() {
    document.getElementById("popup").style.display = "none";
}

// Funzione per controllare se l'email è valida utilizzando una espressione regolare
function controllaEmail(email){
    var regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return regex.test(email);
}



// Configura il Facebook SDK
window.fbAsyncInit = function() {
    FB.init({
        appId      : '827661742457088', // Inserisci il tuo ID App Facebook
        cookie     : true,
        xfbml      : true,
        version    : 'v14.0'
    });

    FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
    });
};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// Funzione per gestire il cambio di stato del login
function statusChangeCallback(response) {
    if (response.status === 'connected') {
        console.log('Logged in and authenticated');
    } else {
        console.log('Not authenticated');
    }
}

// Funzione per gestire il login
function checkLoginState() {
    FB.getLoginStatus(function(response) {
        if (response.status === 'connected') {
            var accessToken = response.authResponse.accessToken;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'backend_facebook_auth.php');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                console.log('Signed in as: ' + xhr.responseText);
                window.location.href = "/Sprotz/home/index.php";
            };
            xhr.send('accessToken=' + accessToken);
        } else {
            console.log('Not logged in with Facebook');
        }
    });
}