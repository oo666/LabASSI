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

// Recupera l'email dell'utente dalla sessione
$email = $_SESSION['email'];

// Query per recuperare i dati del profilo dell'utente loggato
$query_utente = "SELECT * FROM utenti WHERE email = '$email'";
$result_utente = mysqli_query($connessione, $query_utente);
$row_utente = mysqli_fetch_assoc($result_utente);

// Controlla se il profilo dell'utente loggato è completo
if (!empty($row_utente['foto_profilo'])) {
    // Query per recuperare i dati dei profili degli altri utenti
    $query_altri_profili = "SELECT * FROM utenti WHERE email != '$email'";
    $result_altri_profili = mysqli_query($connessione, $query_altri_profili);

    // Array per memorizzare i dati dei profili degli altri utenti
    $profili = array();

    // Verifica se ci sono risultati
    if (mysqli_num_rows($result_altri_profili) > 0) {
        // Itera su ogni riga dei risultati e memorizza i dati nell'array dei profili
        while ($row = mysqli_fetch_assoc($result_altri_profili)) {
            $profili[] = $row;
        }
    }

    // Chiudi la connessione al database

} else {
    // Se il profilo dell'utente loggato non è completo, gestisci di conseguenza
}

// Chiudi la connessione al database
mysqli_close($connessione);
?>










<!DOCTYPE html>
<html>

<head>
    <title> Sprotz! Home </title>

    <!-- Meta tag per la visualizzazione su dispositivi mobili -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">

    <!-- Collegamento al file CSS per lo stile della pagina home -->
    <link rel="stylesheet" href="/Sprotz/home/home.css">

    <!-- Collegamento al file CSS di BoxIcons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    

    <!-- Icona del logo del sito -->
    <link rel="icon" type="image/png" href="LOGO.png">

</head>

<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <header>
            <!-- Logo e scritta del sito nella sidebar -->
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

        <!-- Menu della sidebar -->
        <div class="menu-bar">
            <div class="menu">
                <ul class="menu-links">
                    <!-- Link per la home -->
                    <li class="nav-link">
                        <a href="/Sprotz/home/index.php">
                            <i class='bx bx-home icona'></i>
                            <span class="text nav-text">Home</span>
                        </a>
                    </li>

                    <!-- Link per l'area personale -->
                    <li class="nav-link">
                        <a href="/Sprotz/profilo/profilo.php">
                            <i class='bx bx-wink-smile icona'></i>
                            <span class="text nav-text">Area Personale</span>
                        </a>
                    </li>

                    <!-- Link per la chat -->
                    <li class="nav-link">
                        <a href="/Sprotz/chat/chat.php">
                            <i class='bx bx-conversation icona'></i>
                            <span class="text nav-text">Chat</span>
                        </a>
                    </li>

                    <!-- Link per le informazioni -->
                    <li class="nav-link">
                        <a href="/Sprotz/informazioni/informazioni.php">
                            <i class='bx bx-info-square icona'></i>
                            <span class="text nav-text">Chi siamo?</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Link per il logout -->
            <div class="bottom">
                <li class="">
                    <a href="/Sprotz/accedi/accedi.html">
                        <i class='bx bx-log-out icona'></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>
            </div>
        </div>
    </nav>

    <!-- Menu per dispositivi mobili -->
    <nav class="menu-telefono">
        <header>
            <ul class="menu-links">
                <!-- Link per la home -->
                <li class="nav-link">
                    <a href="/Sprotz/home/index.php">
                        <i class='bx bx-home icona'></i>
                    </a>
                </li>

                <!-- Link per l'area personale -->
                <li class="nav-link">
                    <a href="/Sprotz/profilo/profilo.php">
                        <i class='bx bx-wink-smile icona'></i>
                    </a>
                </li>

                <!-- Link per la chat -->
                <li class="nav-link">
                    <a href="/Sprotz/chat/chat.php">
                        <i class='bx bx-conversation icona'></i>
                    </a>
                </li>

                <!-- Link per le informazioni -->
                <li class="nav-link">
                    <a href="#">
                        <i class='bx bx-info-square icona'></i>
                    </a>
                </li>

                <!-- Link per il logout -->
                <li class="nav-link">
                    <a href="/Sprotz/accedi/accedi.html">
                        <i class='bx bx-log-out icona'></i>
                    </a>
                </li>
            </ul>
        </header>
    </nav>

    <!-- Sezione home -->
    <section class="home">
        <div class="text">Home
            <!-- Icona punto esclamativo per amministratori -->
            <a href="/Sprotz/home/segnala.php">
                    <i class='bx bx-error' style="font-size: 24px; color: red; margin-left: 8px;"></i>
            </a>
        </div>
         
        
        <div class="lista-profili">

            <!-- Contenuto dinamico  -->
            <?php if (!empty($row_utente['foto_profilo']) && !empty($row_utente['domandaQuiz'])) : ?>
                <?php foreach ($profili as $profilo) : ?>
                    <?php if (!empty($profilo['foto_profilo']) && !empty($profilo['domandaQuiz']) && strpos($row_utente['listanera'], $profilo['username']) === false && strpos($row_utente['contatti'], $profilo['username']) === false) : ?>
                        <!-- Inizia un riquadro profilo -->
                        <div class="profilo-riquadro">
                            <div class="profilo-nome"><?php echo $profilo['username']; ?></div>
                            <img src="<?php echo $profilo['foto_profilo']; ?>" alt="Foto" class="profilo-immagine">
                            <!-- Bottone per avviare il quiz -->
                            <button class="submit" id="quizzamento" type="button" value="Provaci" data-domanda="<?php echo htmlspecialchars($profilo['domandaQuiz']); ?>" data-risposta1="<?php echo htmlspecialchars($profilo['risposta1']); ?>" data-risposta2="<?php echo htmlspecialchars($profilo['risposta2']); ?>" data-risposta3="<?php echo htmlspecialchars($profilo['risposta3']); ?>" data-risposta4="<?php echo htmlspecialchars($profilo['risposta4']); ?>" data-risposta-corretta="<?php echo htmlspecialchars($profilo['risposta1']); ?>" data-username="<?php echo htmlspecialchars($profilo['username']); ?>" data-bio="<?php echo htmlspecialchars($profilo['bio']); ?>" data-foto="<?php echo htmlspecialchars($profilo['foto_profilo']); ?>" onclick="mostraQuiz(this)">Provaci!</button>
                        </div>
                        <!-- Fine riquadro profilo -->
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else : ?>
                <!-- Messaggio quando il profilo è incompleto -->
                <div class="profilo-incompleto" style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%;">
                    <p style="text-align: center; color: white;">Completa il tuo profilo per visualizzare gli altri utenti.</p>
                    <p style="text-align: center;"><a href="/Sprotz/profilo/profilo.php" style="color: #D65200;">Vai al tuo profilo</a></p>
                </div>
            <?php endif; ?>
    </section>

    <!-- Popup per il quiz -->
    <div class="popup" id="quizPopup" style="display:none">
        <div class="closebutton" onclick="chiudiQuiz()">X</div>

        <div class="radio-input">
            <div class="info">
                <div class="foto-box">
                    <img class="foto" src="">
                </div>
                <div class="username-bio-box">
                    <span class='username'></span><br>
                    <span class="bio"></span>
                </div>
            </div>
            <div class="question-box">
                <span class="question"></span>
            </div>
            <!-- Form per inviare la risposta -->
            <form id="myForm" action="submit.php" method="post">
                <input type="hidden" id="usernameHidden" name="username" value="">
                <input type="hidden" id="rispostaHidden" name="rispostaHidden" value="">
                <input type="radio" id="value-1" name="risposta" value="value-1">
                <label for="value-1"></label>
                <input type="radio" id="value-2" name="risposta" value="value-2">
                <label for="value-2"></label>
                <input type="radio" id="value-3" name="risposta" value="value-3">
                <label for="value-3"></label>
                <input type="radio" id="value-4" name="risposta" value="value-4">
                <label for="value-4"></label>
                <button class="submitQuiz" type="submit" onclick="submitForm()">Invia la risposta</button>
            </form>
        </div>
    </div>
    <!-- Fine popup -->

    <!-- Script JS -->
    <script src="home.js"></script>



</body>

<!-- Overlay per il popup -->
<div id="overlay" style="display:none;"></div>

</html>