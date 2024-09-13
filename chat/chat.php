<?php
session_start();

// Recupera il ruolo dell'utente dalla sessione
$ruolo = isset($_SESSION['ruolo']) ? $_SESSION['ruolo'] : '';

// Determina il link della home in base al ruolo dell'utente
$home_link = ($ruolo === 'amministratore') ? '/Sprotz/home/home_admin.php' : '/Sprotz/home/index.php';

// connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registrazione";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
}

// prepara la query SQL per recuperare i dati dell'utente
$email = $_SESSION['email'];
$query = "SELECT * FROM utenti WHERE email = '$email'";

// esegui la query
$result = mysqli_query($conn, $query);

// verifica se ci sono risultati
if (mysqli_num_rows($result) > 0) {
    // estrai i dati dell'utente
    $row = mysqli_fetch_assoc($result);
    $email = $row['email'];
    $dataNascita = $row['dataNascita'];
    $gender = $row['gender'];
    $nome = $row['nome'];
    $cognome = $row['cognome'];
    $username = $row['username'];
    $bio = $row['bio'];
    $fotoProfilo = $row['foto_profilo'];
    $contatti = $row['contatti'];
} else {
    // se non ci sono risultati, gestisci l'errore o reindirizza l'utente ad una pagina di errore
    echo "Errore: Nessun dato trovato nel database";
    exit();
}

if (!empty($fotoProfilo)) {
    // query per recuperare i dati dei profili degli altri utenti
    $query_altri_profili = "SELECT * FROM utenti WHERE email != '$email'";
    $result_altri_profili = mysqli_query($conn, $query_altri_profili);

    // array per memorizzare i dati dei profili degli altri utenti
    $profili = array();

    // verifica se ci sono risultati
    if (mysqli_num_rows($result_altri_profili) > 0) {
        // itera su ogni riga dei risultati e memorizza i dati nell'array dei profili
        while ($row_tutti = mysqli_fetch_assoc($result_altri_profili)) {
            $profili[] = $row_tutti;
        }
    }

    

}
// chiudi la connessione al database
$conn->close();
?>


<!DOCTYPE html>
<html>

<head>
    <title> Sprotz! Chat </title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="chat.css">

    <!-- BoxIcons per CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- LOGO -->
    <link rel="icon" type="image/png" href="LOGO.png">

    <style>
        /* Stile per rimuovere i pallini dalla lista */
        ul {
            list-style-type: none;
        }
    </style>
</head>

<body>
    <nav class="sidebar open">
        <header>
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

            <i class='bx bxs-chevron-right toggleArrow'></i>
        </header>

        <div class="menu-bar">
            <div class="menu">
                <ul>
                    <li class="nav-link">
                        <a href="<?php echo $home_link; ?>">
                            <i class='bx bx-home icona'></i>
                            <span class="text nav-text">Home</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="http://localhost/Sprotz/profilo/profilo.php">
                            <i class='bx bx-wink-smile icona'></i>
                            <span class="text nav-text">Area Personale</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-conversation icona'></i>
                            <span class="text nav-text">Chat</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="http://localhost/Sprotz/informazioni/informazioni.php">
                            <i class='bx bx-info-square icona'></i>
                            <span class="text nav-text">Chi siamo?</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="bottom">
                <li class="">
                    <a href="http://localhost/Sprotz/accedi/accedi.html">
                        <i class='bx bx-log-out icona'></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>
            </div>
        </div>
    </nav>

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

    <section class="chat">
        <div class="text">Chat </div>
        <div class="container">
            <form class="form-chat">
                <!-- importante perché da qui si vede la chat corrente con il contatto scelto -->
                <div class="chat-header" id="utente-chat">Utente</div>
                <div class="messages-area" id="box-area">
                </div>
                <div class="sender-area">
                    <div class="input-place">
                        <input type="textarea" id="message-input" placeholder="Send a message..." class="send-input">
                        <div class="send" id="send-Button">
                            <svg class="send-icon" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                                <g>
                                    <g>
                                        <path fill="#6B6C7B" d="M481.508,210.336L68.414,38.926c-17.403-7.222-37.064-4.045-51.309,8.287C2.86,59.547-3.098,78.551,1.558,96.808 L38.327,241h180.026c8.284,0,15.001,6.716,15.001,15.001c0,8.284-6.716,15.001-15.001,15.001H38.327L1.558,415.193 c-4.656,18.258,1.301,37.262,15.547,49.595c14.274,12.357,33.937,15.495,51.31,8.287l413.094-171.409 C500.317,293.862,512,276.364,512,256.001C512,235.638,500.317,218.139,481.508,210.336z">
                                        </path>
                                    </g>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
            </form>
            <form class="form-utenti">
                <div class="utenti-header" hidden>
                    <!-- importante per prendere il nome utente della persona loggata -->
                    <input class="input" value="<?php echo htmlspecialchars($username); ?>" readonly> 
                </div>
                <div class="list-utenti">
                    <ul class="menu-links" id="list-utenti">
                        <!-- qui verranno inseriti dinamicamente gli elementi della lista contatti -->
                        <?php
                        // connessione al databaseq
                        $servername = "localhost";
                        $username_db = "root";
                        $password = "";
                        $dbname = "registrazione";

                        $conn = new mysqli($servername, $username_db, $password, $dbname);

                        if ($conn->connect_error) {
                            die("Connessione al database fallita: " . $conn->connect_error);
                        }

                        // query per selezionare i nomi dalla tabella degli utenti
                        $sql = "SELECT * FROM utenti";
                        $result = $conn->query($sql);

                        // verifica se ci sono risultati
                        if ($result->num_rows > 0) {
                            // output dei dati
                            while ($row = $result->fetch_assoc()) {
                                if ($username == $row["username"]) {
                                    $stringa = $row["contatti"];
                                    $stringa = substr($stringa, 0, -1);
                                    // prendo la stringa che sta il contatti e la suddivido in una lista visto
                                    // che la stringa è caratterizzata da (contatto,contatto,....)
                                    $contatti_array = explode(',', $stringa);

                                    foreach ($contatti_array as $contatto) {
                                        $contatto = trim($contatto);
                                        // verifica se il valore non è vuoto
                                        if (!empty($contatto)) {
                                            foreach ($profili as $profilo) {
                                                //se contatto è un username di una persona che sta nella lista profili 
                                                // allora si crea l'oggetto li associato a lui
                                                if ($profilo['username'] == $contatto) {
                                                    echo "<li class='click-utente' id='contatto' data-value='" . htmlspecialchars($contatto) . "' data-foto-profilo='" . $profilo['foto_profilo'] . "'>" . "<img src='" . $profilo['foto_profilo'] . "' alt='Immagine' class='img-contatti' >" .  $contatto . "</li>";
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            echo "<li>Nessun utente disponibile</li>";
                        }


                        $conn->close();
                        ?>
                    </ul>
                </div>
            </form>
        </div>
    </section>


    <script src="chat.js"></script>
</body>

</html>