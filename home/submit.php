<?php
session_start();

// Funzione per gestire la logica di submit
function handleSubmit($connessione) {
    $email = $_SESSION['email'];

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"])) {
        $username = $_POST["username"];
        $selectedValue = $_POST["rispostaHidden"];

        $query_risposta1 = "SELECT risposta1 FROM utenti WHERE username = '$username'";
        $result_risposta1 = mysqli_query($connessione, $query_risposta1);
        $row_risposta1 = mysqli_fetch_assoc($result_risposta1);
        $risposta1 = $row_risposta1['risposta1'];

        if ($selectedValue == $risposta1) {
            $query = "UPDATE utenti SET contatti = CONCAT(contatti, '$username, ') WHERE email = '$email'";
        } else {
            $query = "UPDATE utenti SET listanera = CONCAT(listanera, '$username, ') WHERE email = '$email'";
        }

        $result = mysqli_query($connessione, $query);

        if ($result) {
            header("Location: /Sprotz/home/index.php");
            exit();
        } else {
            echo "Errore nell'inserimento dei dati nel database: " . mysqli_error($connessione);
        }
    } else {
        echo "Errore nell'elaborazione della richiesta.";
    }

    mysqli_close($connessione);
}

// Connessione al database
$hostname = "localhost";
$username = "root";
$password = "";
$database = "registrazione";
$connessione = mysqli_connect($hostname, $username, $password, $database);

// Verifica la connessione al database
if (!$connessione) {
    die("Connessione al database fallita: " . mysqli_connect_error());
}

// Chiama la funzione di gestione
handleSubmit($connessione);
?>
