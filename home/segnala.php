<?php
// Avvia la sessione
session_start();

// Includi le configurazioni per la connessione al database
$hostname = "localhost";
$username = "root";
$password = "";
$database = "registrazione";

// Connessione al database
$connessione = mysqli_connect($hostname, $username, $password, $database);

// Verifica la connessione al database
if (!$connessione) {
    die("Connessione al database fallita: " . mysqli_connect_error());
}

// Recupera l'email dell'utente amministratore dalla sessione
$email_admin = $_SESSION['email'];

// Query per recuperare tutti gli utenti tranne l'amministratore
$query_utenti = "SELECT * FROM utenti WHERE email != '$email_admin'";
$result_utenti = mysqli_query($connessione, $query_utenti);

// Array per memorizzare i dati degli utenti
$utenti = array();

// Verifica se ci sono risultati
if (mysqli_num_rows($result_utenti) > 0) {
    // Itera su ogni riga dei risultati e memorizza i dati nell'array degli utenti
    while ($row = mysqli_fetch_assoc($result_utenti)) {
        $utenti[] = $row;
    }
}

// Chiudi la connessione al database
mysqli_close($connessione);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestione Utenti</title>
    <!-- Meta tag per la visualizzazione su dispositivi mobili -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
    <!-- Collegamento al file CSS per lo stile della pagina -->
    <link rel="stylesheet" href="/Sprotz/home/home.css">
    <!-- Collegamento al file CSS di BoxIcons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Icona del logo del sito -->
    <link rel="icon" type="image/png" href="LOGO.png">
    <!-- Stili aggiuntivi -->
    <style>
        .utente-lista {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .utente-item {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #5c5c5c;
            padding: 10px;
        }
        .utente-item img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        .utente-item img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            object-fit: cover; /* Mantiene le proporzioni senza distorcere l'immagine */
            margin-right: 10px;
        }
        .utente-item .nome {
            flex: 1;
            font-size: 18px;
        }
        .utente-item .delete-button {
            color: red;
            font-size: 20px;
            cursor: pointer;
            text-decoration: none;
        }

        .home1 {
            height: 100vh;
            margin-left: 0;
            transition: var(--tran-05);
            background: #4b4b4b;
        }

        .home1 .text {
            font-size: 30px;
            font-weight: 500;
            color: #ffffff;
            padding: 8px 40px;
        }

    </style>
</head>
<body>
    <!-- Sezione principale -->
    <section class="home1">
        <div class="text">Gestione Utenti
            <a href="/Sprotz/home/home_admin.php">
                    <i class='bx bx-error' style="font-size: 24px; color: red; margin-left: 8px;"></i>
            </a>
        </div>

        <ul class="utente-lista">
            <?php if (count($utenti) > 0) : ?>
                <?php foreach ($utenti as $utente) : ?>
                    <li class="utente-item">
                        <img src="<?php echo htmlspecialchars($utente['foto_profilo']); ?>" alt="Foto Profilo">
                        <div class="nome"><?php echo htmlspecialchars($utente['username']); ?></div>
                        <a href="#" onclick="confirmDelete('<?php echo $utente['email']; ?>'); return false;" class="delete-button">X</a>
                    </li>
                <?php endforeach; ?>
            <?php else : ?>
                <p>Nessun utente da visualizzare.</p>
            <?php endif; ?>
        </ul>
    </section>

    <!-- Script JS -->
    <script>
        function confirmDelete(email) {
            if (confirm('Sei sicuro di voler eliminare questo utente?')) {
                window.location.href = '/Sprotz/home/segnala.php?delete=' + encodeURIComponent(email);
            }
        }
    </script>

    <?php
    // Gestione della richiesta di eliminazione
    if (isset($_GET['delete'])) {
        $email_da_eliminare = $_GET['delete'];

        // Connessione al database
        $connessione = mysqli_connect($hostname, $username, $password, $database);

        // Verifica la connessione al database
        if (!$connessione) {
            die("Connessione al database fallita: " . mysqli_connect_error());
        }

        // Query per eliminare l'utente
        $query_elimina = "DELETE FROM utenti WHERE email = '$email_da_eliminare'";
        if (mysqli_query($connessione, $query_elimina)) {
            echo "<script>alert('Utente eliminato con successo.'); window.location.href='/Sprotz/home/segnala.php';</script>";
        } else {
            echo "<script>alert('Errore durante l\'eliminazione dell\'utente.');</script>";
        }

        // Chiudi la connessione al database
        mysqli_close($connessione);
    }
    ?>
</body>
</html>