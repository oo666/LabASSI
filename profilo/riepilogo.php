<?php

session_start();

if (!isset($_SESSION['email'])) {
    // Se l'utente non ha effettuato l'accesso, reindirizzalo alla pagina di accesso
    header("Location: /Sprotz/accedi/accedi.php");
    exit();
}


// Configurazione della connessione al database
$hostname = "localhost";
$username = "root";  // Cambia con il tuo username del database
$password = "";      // Cambia con la tua password del database
$database = "registrazione"; // Cambia con il nome del tuo database

// Crea una connessione al database
$connessione = mysqli_connect($hostname, $username, $password, $database);

// Verifica la connessione
if (!$connessione) {
    die("Connessione fallita: " . mysqli_connect_error());
}

// Prepara la query SQL per recuperare i dati dell'utente
$email = $_SESSION['email'];
$query = "SELECT * FROM utenti WHERE email = '$email'";

// Esegui la query
$result = mysqli_query($connessione, $query);

// Verifica se ci sono risultati
if (mysqli_num_rows($result) > 0) {
    // Estrai i dati dell'utente
    $row = mysqli_fetch_assoc($result);
    $email = $row['email'];
    $dataNascita = $row['dataNascita'];
    $gender = $row['gender'];
    $nome = $row['nome'];
    $cognome = $row['cognome'];
    $username = $row['username'];
    $old_username = $username;
    $bio = $row['bio'];
    $fotoProfilo = $row['foto_profilo'];
} else {
    // Se non ci sono risultati, gestisci l'errore o reindirizza l'utente ad una pagina di errore
    echo "Errore: Nessun dato trovato nel database";
    exit();
}

// Chiudi la connessione al database
mysqli_close($connessione);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <!-- LOGO -->
    <link rel="icon" type="image/png" href="LOGO.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $username; ?> - Profilo</title>
    <meta property="og:title" content="<?php echo $username; ?> - Profilo">
    <meta property="og:description" content="Scopri di più su <?php echo $username; ?>. Clicca qui per registrarti e unirti alla nostra comunità.">
    <meta property="og:image" content="<?php echo $fotoProfilo; ?>">
    <meta property="og:url" content="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:type" content="website">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh; /* Imposta l'altezza del corpo per coprire l'intera finestra del browser */
        }

        .container {
            max-width: 800px;
            width: 100%; /* Aggiungi larghezza del 100% per il contenitore */
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center; /* Allinea il testo al centro */
        }
        .profile-photo {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
        .username {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .bio {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .register-link {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border-radius: 4px;
            text-decoration: none;
        }
        .register-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($fotoProfilo): ?>
            <img src="<?php echo $fotoProfilo; ?>" alt="Foto di <?php echo $username; ?>" class="profile-photo">
        <?php else: ?>
            <div class="profile-photo" style="background-color: #ccc; width: 150px; height: 150px; border-radius: 50%; display: inline-block;"></div>
        <?php endif; ?>
        <div class="username"><?php echo $username; ?></div>
        <div class="bio"><?php echo $bio; ?></div>
        <a href="/Sprotz/registrati/registrati.html" class="register-link">Unisciti a noi e scopri di più!</a>
    </div>
</body>
</html>