<?php

// Inizia una nuova sessione
session_start();

// Variabili per la connessione al database
$hostname = "localhost";
$username = "root";
$password = "";
$database = "Registrazione";

// Recupera i valori inviati dal modulo di registrazione con il metodo POST
$nome = $_POST['nome'];
$cognome = $_POST['cognome'];
$dataNascita =  $_POST['dataNascita'];
$gender = $_POST['gender'];
$email = $_POST['email'];
$password1 = $_POST['password1'];

// Connessione al database MySQL
$connessione = mysqli_connect($hostname, $username, $password, $database);
if(!$connessione) {
    die("Connessione fallita: " . mysqli_connect_error());
}

// Query per verificare se l'email è già presente nel database
$query_check_email = "SELECT * FROM utenti WHERE email='$email'";
$result_check_email = mysqli_query($connessione, $query_check_email);
if (mysqli_num_rows($result_check_email) > 0) {
    echo '<script>alert("Email già presente nel sistema!"); window.history.back();</script>';
    exit;
}

// Query per inserire i dati del nuovo utente nel database
$query = "INSERT INTO utenti (nome, cognome, dataNascita, gender, email, password1) values ('$nome', '$cognome', '$dataNascita', '$gender', '$email', '$password1')";

// Esegue la quori "$query"
$result = mysqli_query($connessione, $query);
if(!$result) {
    // Se la registrazione non ha avuto successo, mostra un messaggio di errore
    echo "Errore durante la registrazione: " . mysqli_error($connessione);
} else {
    // Se la registrazione ha avuto successo, mostra un messaggio di conferma e reindirizza alla pagina di accesso
    echo "Registrazione riuscita";
    $_SESSION['email'] = $email; // Memorizza l'email dell'utente nella sessione
    header("Location: /Sprotz/accedi/accedi.html"); // Reindirizza alla pagina accedi.html
    exit; // Assicura che lo script termini qui e il reindirizzamento avvenga correttamente
}

// Chiude la connessione con il database
mysqli_close($connessione);

?>