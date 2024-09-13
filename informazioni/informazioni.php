<?php
session_start();

// Recupera il ruolo dell'utente dalla sessione
$ruolo = isset($_SESSION['ruolo']) ? $_SESSION['ruolo'] : '';

// Determina il link della home in base al ruolo dell'utente
$home_link = ($ruolo === 'amministratore') ? '/Sprotz/home/home_admin.php' : '/Sprotz/home/index.php';

if (!isset($_SESSION['email'])) {
    // Se l'utente non ha effettuato l'accesso, reindirizzalo alla pagina di accesso
    header("Location: /Sprotz/accedi/accedi.php");
    exit();
}

// Includi qui le configurazioni per la connessione al database
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
?>

<!DOCTYPE html>
<html>

<head>
    <title> Sprotz! Chi siamo? </title>

    <!-- Viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">

    <!-- CSS -->
    <link rel="stylesheet" href="informazioni.css">

    <!-- BoxIcons per CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- LOGO -->
    <link rel="icon" type="image/png" href="LOGO.png">
</head>

<body>
    <!-- Creazione sidebar (non visibile per dispositivi mobili) -->
    <nav class="sidebar">
        <header>
            <!-- Logo e nome del sito -->
            <div class="image-sidebar">
                <span class="image">
                    <img src="LOGO.png" alt="logo">
                </span>

                <div class="image-sprotz">
                    <span class="image-scritta">
                        <img src="SPROTZ_scritta.png" alt="scritta" style="width: 75%; height: 75%;">
                    </span>
                </div>
            </div>
            <!-- Icona per il toggle della sidebar -->
            <i class='bx bxs-chevron-right toggleArrow'></i>
        </header>

         <!-- Menu della sidebar con elenco cliccabile che indirizza con i vari link alle varie pagine -->
        <div class="menu-bar">
            <div class="menu">
                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="<?php echo $home_link; ?>">
                            <i class='bx bx-home icona'></i>
                            <span class="text nav-text">Home</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/Sprotz/profilo/profilo.php">
                            <i class='bx bx-wink-smile icona'></i>
                            <span class="text nav-text">Area Personale</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/Sprotz/chat/chat.php">
                            <i class='bx bx-conversation icona'></i>
                            <span class="text nav-text">Chat</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-info-square icona'></i>
                            <span class="text nav-text">Chi siamo?</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="bottom">
                <li class="nav-link">
                    <a href="/Sprotz/accedi/accedi.html">
                        <i class='bx bx-log-out icona'></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>
            </div>
        </div>
    </nav>

    <!-- Navbar per dispositivi mobili con elenco cliccabile che indirizza con i vari link alle varie pagine -->
    <nav class="menu-telefono">
        <header>
            <ul class="menu-links">
                <li class="nav-link">
                    <a href="/Sprotz/home/index.php">
                        <i class='bx bx-home icona'></i>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="/Sprotz/profilo/profilo.php">
                        <i class='bx bx-wink-smile icona'></i>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="/Sprotz/chat/chat.php">
                        <i class='bx bx-conversation icona'></i>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="/Sprotz/informazioni/informazioni.html">
                        <i class='bx bx-info-square icona'></i>
                    </a>
                </li>

                <li class="nav-link">
                    <a href="/Sprotz/accedi/accedi.html">
                        <i class='bx bx-log-out icona'></i>
                    </a>
                </li>
            </ul>
        </header>
    </nav>

    <!-- Sezione informazioni -->
    <section class="informazioni">
        <!-- Titolo sezione -->
        <div class="text">Chi siamo</div>
        <!-- Sottotolo (si ripete successivamente con la stessa classe)-->
        <div class="titolo-info">Su di noi</div>
        <!-- Testo del sottotitolo (si ripete successivamente con la stessa classe)-->
        <div class="text-info">Ciao! Siamo noi i creatori di Sprotz! Ci chiamiamo Clelia Bernardo, Giovanni Cassino e
            Gaetano Morello, abbiamo 21 anni e siamo iscritti al terzo anno del corso di Ingegneria Informatica
            all'Università di Roma "La Sapienza".
            Ci siamo conosciuti al primo anno di Università, grazie al fatto che participavamo agli stessi corsi e
            facevamo parte dello stesso canale. Complice di questo rapporto è anche la provenienza, visto che tutti e
            tre siamo fuorisede provenienti dalla Basilicata e dalla Campania.
        </div>
        <!-- Container delle foto e descrizioni di noi creatori del sito-->
        <div class="foto-container">
            <div class="foto">
                <img src="Clelia.jpg" alt="Foto Clelia">
                <p class="descrizione">Clelia Bernardo</p>
            </div>
            <div class="foto">
                <img src="Giovanni.jpg" alt="Foto Giovanni">
                <p class="descrizione">Giovanni Cassino</p>
            </div>
            <div class="foto">
                <img src="Gaetano.jpg" alt="Foto Gaetano">
                <p class="descrizione">Gaetano Morello</p>
            </div>
        </div>
        <div class="titolo-info">L'idea</div>
        <div class="text-info">Abbiamo ideato questo sito web come progetto per il corso di "Linguaggi e Tecnologie per
            il Web", esame a scelta del nostro corso di laurea.
            L'idea è nata durante le lezioni del corso stesso, pensando a qualcosa di innovativo e di divertente da
            poter implementare all'interno del sito.
            Da qui l'idea di un sito di interazioni e conoscenza di nuove persone grazie ad un approccio particolare, i
            QUIZ!
        </div>
        <div class="titolo-info">Come funziona?</div>
        <div class="text-info">Il funzionamento è semplicissimo, una volta che ti sei registrato al sito, basta inserire
            uno username, una breve descrizione di te stesso e di cosa ti piace fare e poi la parte più importante,
            creare un vero e proprio quiz su argomenti di carattere generale!
            Consiglio! Crea un quiz che si addica a te e soprattutto che miri al range di persone che ti possano
            interessare, perché proprio qui sta il bello: ogni utente nella home vedrà una sezione con tutti gli altri
            utenti iscritti al sito e potrà scegliere con chi interagire, ma solamente se avrà passato il quiz!
            Potrai fare amicizia con tantissime nuove persone, sta a te scegliere chi!
        </div>
        <div class="titolo-info">Il Quiz</div>
        <div class="text-info">Il quiz è formato da una domanda aperta pensata direttamente dall'utente e da 4 risposte
            multiple, di cui una sola vera e le altre false.
            Un utente per poter interagire deve indovinare la risposta con un solo tentativo, altrimenti si
            danneggerebbe la precisione dell'affinità tra i due utenti, facile no?
        </div>
    </section>
    <!-- Script JavaScript -->
    <script src="informazioni.js"></script>
</body>

</html>