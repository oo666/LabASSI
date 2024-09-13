<?php
// Avvia la sessione per gestire le variabili di sessione
session_start();

// Informazioni per la connessione al database
$hostname = "localhost";
$username = "root";
$password = "";
$database = "Registrazione";

// Recupera i dati inviati tramite il metodo POST
$email = $_POST['email'];
$password1 = $_POST['password1'];

// Connessione al database MySQL
$connessione = mysqli_connect($hostname, $username, $password, $database);
if (!$connessione) {
    // Se la connessione fallisce, mostra un messaggio di errore e termina lo script
    die("Connessione fallita: " . mysqli_connect_error());
}

// Query per cercare un utente nel database con l'email e la password fornite
$query = "SELECT * FROM utenti WHERE email='$email' AND password1='$password1'";
$result = mysqli_query($connessione, $query);

// Controlla se c'è un solo risultato (una riga corrispondente)
if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    // Se c'è un risultato, l'utente esiste nel database
    $_SESSION['email'] = $email; // Memorizza l'email dell'utente nella sessione (opzionale)
    $_SESSION['ruolo'] = $row['ruolo'];
    if ($row['ruolo'] == 'amministratore') {
        header("Location: /Sprotz/home/home_admin.php"); // Reindirizza alla home admin
    } else {
        header("Location: /Sprotz/home/index.php");
    }
    exit; // Assicura che lo script termini qui e il reindirizzamento avvenga correttamente
} else {
    // Se non ci sono risultati (nessun utente corrispondente), mostra un messaggio di errore
    echo '<script>alert("Email o password errate!");</script>';
    sleep(1.5);
    // Reindirizza l'utente alla pagina di accesso
    header("Location: /Sprotz/accedi/accedi.html");
    exit; // Assicura che lo script termini qui e il reindirizzamento avvenga correttamente
}

// Chiude la connessione al database
mysqli_close($connessione);
?>