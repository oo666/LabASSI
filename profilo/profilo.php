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
    $segnoZodiacale = getZodiacSign($dataNascita);
} else {
    // Se non ci sono risultati, gestisci l'errore o reindirizza l'utente ad una pagina di errore
    echo "Errore: Nessun dato trovato nel database";
    exit();
}

function getZodiacSign($birthDate) {
    $date = new DateTime($birthDate);
    $day = $date->format('d');
    $month = $date->format('m');

    if (($month == 1 && $day >= 20) || ($month == 2 && $day <= 18)) return "aquarius";
    if (($month == 2 && $day >= 19) || ($month == 3 && $day <= 20)) return "pisces";
    if (($month == 3 && $day >= 21) || ($month == 4 && $day <= 19)) return "aries";
    if (($month == 4 && $day >= 20) || ($month == 5 && $day <= 20)) return "taurus";
    if (($month == 5 && $day >= 21) || ($month == 6 && $day <= 20)) return "gemini";
    if (($month == 6 && $day >= 21) || ($month == 7 && $day <= 22)) return "cancer";
    if (($month == 7 && $day >= 23) || ($month == 8 && $day <= 22)) return "leo";
    if (($month == 8 && $day >= 23) || ($month == 9 && $day <= 22)) return "virgo";
    if (($month == 9 && $day >= 23) || ($month == 10 && $day <= 22)) return "libra";
    if (($month == 10 && $day >= 23) || ($month == 11 && $day <= 21)) return "scorpio";
    if (($month == 11 && $day >= 22) || ($month == 12 && $day <= 21)) return "sagittarius";
    if (($month == 12 && $day >= 22) || ($month == 1 && $day <= 19)) return "capricorn";
}

// Funzione per tradurre il testo
function translateText($text, $targetLanguage = 'it') {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://google-translator9.p.rapidapi.com/v2",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([
            'q' => $text,
            'source' => 'en', // Supponiamo che la lingua di origine sia inglese
            'target' => $targetLanguage,
            'format' => 'text'
        ]),
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "x-rapidapi-host: google-translator9.p.rapidapi.com",
            "x-rapidapi-key: 20b67063ccmsh97afca8964729aap17b01djsn2d89b26e068a" // Sostituisci con la tua chiave API di RapidAPI
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
        return null;
    } else {
        return json_decode($response, true);
    }
}

// Chiave API RapidAPI
$rapidApiKey = '20b67063ccmsh97afca8964729aap17b01djsn2d89b26e068a'; 
$url = "https://best-daily-astrology-and-horoscope-api.p.rapidapi.com/api/Detailed-Horoscope/?zodiacSign=$segnoZodiacale";

// Impostazioni cURL per la richiesta
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "x-rapidapi-host: best-daily-astrology-and-horoscope-api.p.rapidapi.com",
        "x-rapidapi-key: " . $rapidApiKey
    ],
]);

$response = curl_exec($ch);
$err = curl_error($ch);

curl_close($ch);

// Decodifica della risposta JSON
$data = json_decode($response, true);


// Verifica se la risposta contiene dati
$oroscopo = isset($data['prediction']) ? $data['prediction'] : "Oroscopo non disponibile.";

$translatedResponse = translateText($oroscopo);

if ($translatedResponse) {
    
    if (isset($translatedResponse['data']['translations'][0]['translatedText'])) {
        $translatedText = $translatedResponse['data']['translations'][0]['translatedText'];
        
    } else {
        echo 'No translation data found.';
    }
} else {
    echo 'Translation failed or no response from API.';
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Controlla se i valori degli input sono stati inviati
    if (isset($_POST['username']) && isset($_POST['bio'])) {
        $username = $_POST['username'];
        $bio = $_POST['bio'];

        // Query per verificare se l'username esiste già nel database
        $query_check_username = "SELECT * FROM utenti WHERE username = ? AND email != ?";
        $stmt_check_username = mysqli_prepare($connessione, $query_check_username);
        mysqli_stmt_bind_param($stmt_check_username, "ss", $username, $_SESSION['email']);
        mysqli_stmt_execute($stmt_check_username);
        $result_check_username = mysqli_stmt_get_result($stmt_check_username);

        // Se l'username esiste già nel database, mostra un messaggio di errore
        if (mysqli_num_rows($result_check_username) > 0) {
            echo "Errore: L'username inserito esiste già.";
            exit(); // Termina lo script
        }


        if (isset($_FILES['foto_profilo'])) {
            $file = $_FILES['foto_profilo'];
            $file_tmp = $_FILES['foto_profilo'];

            // Estrai le informazioni sul file
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];

            // Ottieni l'estensione del file
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');


            // Verifica se l'estensione del file è consentita
            if (in_array($fileExt, $allowedExtensions)) {
                // Genera un nome univoco per l'immagine
                $fileNameNew = uniqid('', true) . "." . $fileExt;

                // Percorso di destinazione per l'immagine caricata
                $fileDestination = '../immagini/' . $fileNameNew;

                // Sposta il file nella directory di destinazione
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    // Salva il percorso dell'immagine nel database
                    $sql = "UPDATE utenti SET foto_profilo='$fileDestination' WHERE email='$email'";
                    if (mysqli_query($connessione, $sql)) {
                        //echo "Immagine caricata con successo.";
                    } else {
                        //echo "Errore durante il caricamento dell'immagine nel database: " . mysqli_error($conn);
                    }
                } else {
                    //echo "Si è verificato un errore durante il caricamento del file.";
                }
            } else {
                //echo "Tipo di file non supportato.";
            }
        } else {
            //echo "Nessun file è stato caricato.";
        }


        // Preparazione della query per l'inserimento
        $query_update_profile = "UPDATE utenti SET username = ?, bio = ?  WHERE email = ?";
        $stmt_update_profile = mysqli_prepare($connessione, $query_update_profile);

        // Verifica se la query preparata è stata eseguita correttamente
        if ($stmt_update_profile) {
            // Bind dei parametri
            mysqli_stmt_bind_param($stmt_update_profile, "sss", $username, $bio, $email);

            // Esecuzione della query di aggiornamento del profilo
            if (mysqli_stmt_execute($stmt_update_profile)) {
                // Profilo aggiornato con successo
                header("Refresh:0");
            } else {
                // Gestisci eventuali errori nell'esecuzione della query
                echo "Errore nell'aggiornamento del profilo: " . mysqli_error($connessione);
            }
        } else {
            // Gestisci eventuali errori nella preparazione della query
            echo "Errore nella preparazione della query: " . mysqli_error($connessione);
        }
        // Chiudi la query preparata
        mysqli_stmt_close($stmt_update_profile);

        echo "<script>console.log('old__ " . $old_username . ":');</script>";

        // --------------------------------CONTATTI--------------------------------
        // Query per selezionare i nomi dalla tabella degli utenti
        $sql = "SELECT * FROM utenti";
        $result = $connessione->query($sql);

        // Verifica se ci sono risultati
        if ($result->num_rows > 0) {
            // Output dei dati
            while ($row = $result->fetch_assoc()) {
                $stringa = $row["contatti"];
                $utente_attuale = $row["username"];
                $stringa = substr($stringa, 0, -1);

                $contatti_array = explode(',', $stringa);
                if (empty($contatti_array)) continue;



                foreach ($contatti_array as $contatto) {
                    $contatto = trim($contatto);

                    // Verifica se il valore non è vuoto
                    if ($contatto == $old_username) {

                        $contatti_array[array_search($old_username, $contatti_array)] = $username;
                        // Ricostruisci la stringa contatti aggiornata
                        $stringa_aggiornata = rtrim(implode(',', $contatti_array), ',') . ',';

                        $query_update = "UPDATE utenti SET contatti = ? WHERE username = ?";
                        $stmt_update = mysqli_prepare($connessione, $query_update);

                        //$stringa_aggiornata = "";

                        // Verifica se la query preparata è stata eseguita correttamente
                        if ($stmt_update) {
                            // Bind dei parametri
                            mysqli_stmt_bind_param($stmt_update, "ss", $stringa_aggiornata, $utente_attuale);

                            // Esecuzione della query di aggiornamento
                            if (mysqli_stmt_execute($stmt_update)) {
                                header("Refresh:0");
                            } else {
                                // Gestisci eventuali errori nell'esecuzione della query
                                echo "Errore nell'aggiornamento del record: " . mysqli_error($connessione);
                            }
                        } else {
                            // Gestisci eventuali errori nella preparazione della query
                            echo "Errore nella preparazione della query: " . mysqli_error($connessione);
                        }
                    }
                }
            }
        } else {
            echo "<li>Nessun utente disponibile</li>";
        }


        // --------------------------------LISTA_NERA--------------------------------
        // Query per selezionare i nomi dalla tabella degli utenti
        $sql = "SELECT * FROM utenti";
        $result = $connessione->query($sql);
        // Verifica se ci sono risultati
        if ($result->num_rows > 0) {
            // Output dei dati
            while ($row = $result->fetch_assoc()) {
                $stringa = $row["listanera"];
                $utente_attuale = $row["username"];
                $stringa = substr($stringa, 0, -1);

                $contatti_array = explode(',', $stringa);
                if (empty($contatti_array)) continue;



                foreach ($contatti_array as $contatto) {
                    $contatto = trim($contatto);

                    // Verifica se il valore non è vuoto
                    if ($contatto == $old_username) {

                        $contatti_array[array_search($old_username, $contatti_array)] = $username;
                        // Ricostruisci la stringa contatti aggiornata
                        $stringa_aggiornata = rtrim(implode(',', $contatti_array), ',') . ',';

                        $query_update = "UPDATE utenti SET listanera = ? WHERE username = ?";
                        $stmt_update = mysqli_prepare($connessione, $query_update);

                        //$stringa_aggiornata = "";

                        // Verifica se la query preparata è stata eseguita correttamente
                        if ($stmt_update) {
                            // Bind dei parametri
                            mysqli_stmt_bind_param($stmt_update, "ss", $stringa_aggiornata, $utente_attuale);

                            // Esecuzione della query di aggiornamento
                            if (mysqli_stmt_execute($stmt_update)) {
                                header("Refresh:0");
                            } else {
                                // Gestisci eventuali errori nell'esecuzione della query
                                echo "Errore nell'aggiornamento del record: " . mysqli_error($connessione);
                            }
                        } else {
                            // Gestisci eventuali errori nella preparazione della query
                            echo "Errore nella preparazione della query: " . mysqli_error($connessione);
                        }
                    }
                }
            }
        } else {
            echo "<li>Nessun utente disponibile</li>";
        }




        // --------------------------------MESSAGGI--------------------------------
        // Query per selezionare i nomi dalla tabella degli utenti
        $sql = "SELECT * FROM messaggi";
        $result = $connessione->query($sql);
        // Verifica se ci sono risultati
        if ($result->num_rows > 0) {
            // Output dei dati
            while ($row = $result->fetch_assoc()) {

                // Verifica se il valore non è vuoto
                $query_update = "UPDATE messaggi SET username_contatti = ? WHERE username_contatti = ?";
                $stmt_update = mysqli_prepare($connessione, $query_update);

                //$username = "";
                echo "<script>console.log('stringa_attuale " . $old_username . "');</script>";

                // Verifica se la query preparata è stata eseguita correttamente
                if ($stmt_update) {
                    // Bind dei parametri
                    mysqli_stmt_bind_param($stmt_update, "ss", $username, $old_username);


                    // Esecuzione della query di aggiornamento
                    if (mysqli_stmt_execute($stmt_update)) {
                        header("Refresh:0");
                    } else {
                        // Gestisci eventuali errori nell'esecuzione della query
                        echo "Errore nell'aggiornamento del record: " . mysqli_error($connessione);
                    }
                } else {
                    // Gestisci eventuali errori nella preparazione della query
                    echo "Errore nella preparazione della query: " . mysqli_error($connessione);
                }
            }
        } else {
            echo "<li>Nessun utente disponibile</li>";
        }


        // --------------------------------MESSAGGI--------------------------------
        // Query per selezionare i nomi dalla tabella degli utenti
        $sql = "SELECT * FROM messaggi";
        $result = $connessione->query($sql);
        // Verifica se ci sono risultati
        if ($result->num_rows > 0) {
            // Output dei dati
            while ($row = $result->fetch_assoc()) {

                // Verifica se il valore non è vuoto
                $query_update = "UPDATE messaggi SET username_utente = ? WHERE username_utente = ?";
                $stmt_update = mysqli_prepare($connessione, $query_update);

                //$username = "";
                echo "<script>console.log('stringa_attuale " . $old_username . "');</script>";

                // Verifica se la query preparata è stata eseguita correttamente
                if ($stmt_update) {
                    // Bind dei parametri
                    mysqli_stmt_bind_param($stmt_update, "ss", $username, $old_username);


                    // Esecuzione della query di aggiornamento
                    if (mysqli_stmt_execute($stmt_update)) {
                        header("Refresh:0");
                    } else {
                        // Gestisci eventuali errori nell'esecuzione della query
                        echo "Errore nell'aggiornamento del record: " . mysqli_error($connessione);
                    }
                } else {
                    // Gestisci eventuali errori nella preparazione della query
                    echo "Errore nella preparazione della query: " . mysqli_error($connessione);
                }
            }
        } else {
            echo "<li>Nessun utente disponibile</li>";
        }
    } else {
        //echo "Errore: Uno o più campi del modulo non sono stati inviati correttamente.";
    }

    if (isset($_POST['domandaQuiz']) && isset($_POST['risposta1']) && isset($_POST['risposta2']) && isset($_POST['risposta3']) && isset($_POST['risposta4'])) {
        $domandaQuiz = $_POST['domandaQuiz'];
        $risposta1 = $_POST['risposta1'];
        $risposta2 = $_POST['risposta2'];
        $risposta3 = $_POST['risposta3'];
        $risposta4 = $_POST['risposta4'];

        // Prepara la query per inserire la domanda e le risposte nel database
        $query_insert_quiz = "UPDATE utenti SET domandaQuiz = ?, risposta1 = ?, risposta2 = ?, risposta3 = ?, risposta4 = ?  WHERE email = ?";
        $stmt_insert_quiz = mysqli_prepare($connessione, $query_insert_quiz);

        // Verifica se la query preparata è stata eseguita correttamente
        if ($stmt_insert_quiz) {
            // Bind dei parametri
            mysqli_stmt_bind_param($stmt_insert_quiz, "ssssss", $domandaQuiz, $risposta1, $risposta2, $risposta3, $risposta4, $email);

            // Esecuzione della query di inserimento del quiz nel database
            if (mysqli_stmt_execute($stmt_insert_quiz)) {
                // Quiz salvato con successo nel database
                header("Refresh:0");
            } else {
                // Gestisci eventuali errori nell'esecuzione della query
                echo "Errore nell'inserimento del quiz nel database: " . mysqli_error($connessione);
            }
        } else {
            // Gestisci eventuali errori nella preparazione della query
            echo "Errore nella preparazione della query: " . mysqli_error($connessione);
        }
        mysqli_stmt_close($stmt_insert_quiz);
    }
}


// Chiudi la connessione al database
mysqli_close($connessione);

?>


<!DOCTYPE html>
<html>

<head>
    <title> Sprotz! Area Personale </title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">

    <!-- CSS -->
    <link rel="stylesheet" href="profilo.css">

    <!-- BoxIcons per CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- LOGO -->
    <link rel="icon" type="image/png" href="LOGO.png">


</head>

<body>
    <nav class="sidebar">
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
                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="<?php echo $home_link; ?>">
                            <i class='bx bx-home icona'></i>
                            <span class="text nav-text">Home</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
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
                        <a href="/Sprotz/informazioni/informazioni.php">
                            <i class='bx bx-info-square icona'></i>
                            <span class="text nav-text">Chi siamo?</span>
                        </a>
                    </li>
                </ul>
            </div>

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


    <section class="area-personale">
        <div class="text">Area Personale </div>
        <div class="container">
            <div class="form-container">
                <form class="form" id="completa" action="profilo.php" method="POST" enctype="multipart/form-data">


                    <p class="title">Profilo </p>


                    <div class="grid-container">
                        <div class="grid-item">

                            <label for="file" class="custum-file-upload">
                                <div class="icon" id="file-icon" style="margin-top: 30px;">
                                    <?php if (!empty($row['foto_profilo'])) : ?>
                                        <script>
                                            var fileIcon = document.getElementById('file-icon');
                                            fileIcon.style.display = 'none';
                                        </script>
                                    <?php endif; ?>
                                    <svg viewBox="0 0 24 24" fill="" xmlns="http://www.w3.org/2000/svg">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10 1C9.73478 1 9.48043 1.10536 9.29289 1.29289L3.29289 7.29289C3.10536 7.48043 3 7.73478 3 8V20C3 21.6569 4.34315 23 6 23H7C7.55228 23 8 22.5523 8 22C8 21.4477 7.55228 21 7 21H6C5.44772 21 5 20.5523 5 20V9H10C10.5523 9 11 8.55228 11 8V3H18C18.5523 3 19 3.44772 19 4V9C19 9.55228 19.4477 10 20 10C20.5523 10 21 9.55228 21 9V4C21 2.34315 19.6569 1 18 1H10ZM9 7H6.41421L9 4.41421V7ZM14 15.5C14 14.1193 15.1193 13 16.5 13C17.8807 13 19 14.1193 19 15.5V16V17H20C21.1046 17 22 17.8954 22 19C22 20.1046 21.1046 21 20 21H13C11.8954 21 11 20.1046 11 19C11 17.8954 11.8954 17 13 17H14V16V15.5ZM16.5 11C14.142 11 12.2076 12.8136 12.0156 15.122C10.2825 15.5606 9 17.1305 9 19C9 21.2091 10.7909 23 13 23H20C22.2091 23 24 21.2091 24 19C24 17.1305 22.7175 15.5606 20.9844 15.122C20.7924 12.8136 18.858 11 16.5 11Z" fill=""></path>
                                        </g>
                                    </svg>
                                </div>
                                <div class="text" id="file-text">
                                    <?php if (!empty($row['foto_profilo'])) : ?>
                                        <script>
                                            var fileText = document.getElementById('file-text');
                                            fileText.style.display = 'none';
                                        </script>
                                    <?php endif ?>
                                    <span>Inserisci foto profilo</span>
                                </div>
                                <?php if (!empty($row['foto_profilo'])) : ?>
                                    <div id="image-preview-container">
                                        <img id="image-preview" src="<?php echo $row['foto_profilo']; ?>" style="width: 200px; height: 200px; object-fit: cover;">
                                    </div>
                                <?php endif; ?>
                                <input id="file" type="file" name="foto_profilo">
                            </label>



                        </div>
                        <div class="grid-item">
                            <label class="input-username">
                                <input class="input" type="text" placeholder="" required="" name="username" value="<?php echo $row['username']; ?>" style="font-size: 13px">
                                <span>Username</span>
                            </label>

                            <label class="input-bio">
                                <textarea class="input textarea" id="textarea" placeholder="" required="" rows="6" style="resize: none; font-size: 13px;" name="bio"><?php echo $bio; ?></textarea>
                                <span>Bio</span>
                            </label>

                            <div id="characterCount">caratteri rimanenti: 150</div>
                        </div>
                    </div>
                    <button class="submit" id="completamento" type="submit" form="completa" value="Completa">Aggiorna il profilo</button>



                </form>
                <form class="form" id="quiz" action="profilo.php" method="POST">
                    <p class="title">Crea il tuo quiz </p>
                    <label class="input-question">
                        <input class="input" type="text" id="question" name="domandaQuiz" placeholder="Inserisci la tua domanda qui" maxlength="100" value="<?php echo $row['domandaQuiz']; ?>" required>
                    </label>
                    <div class="text-input">

                        <input type="text-correct" id="answer-1" name="risposta1" placeholder="Inserisci qui la risposta corretta" maxlength="50" value="<?php echo $row['risposta1']; ?>" required>
                        <input type="text" id="answer-2" name="risposta2" placeholder="Inserisci una risposta errata" maxlength="50" value="<?php echo $row['risposta2']; ?>" required>
                        <input type="text" id="answer-3" name="risposta3" placeholder="Inserisci una risposta errata" maxlength="50" value="<?php echo $row['risposta3']; ?>" required>
                        <input type="text" id="answer-4" name="risposta4" placeholder="Inserisci una risposta errata" maxlength="50" value="<?php echo $row['risposta4']; ?>" required>
                    </div>
                    <button class="submit" id="quizzamento" type="submit" form="quiz" value="Salva">Salva il quiz</button>
                </form>
            </div>



            <div class="form-container">
                <form class="form-info">


                    <div class="flex">
                        <label>
                            <input type="text" id="nome" name="nome" class="input" placeholder="" value="<?php echo $row['nome']; ?>" readonly>
                            <span>Nome</span>
                        </label>

                        <label>
                            <input type="text" id="cognome" name="cognome" class="input" placeholder="" value="<?php echo $cognome; ?>" readonly>
                            <span>Cognome</span>
                        </label>
                    </div>

                    <label>
                        <input type="date" id="dataNascita" name="dataNascita" class="input" placeholder="" value="<?php echo $dataNascita; ?>" readonly>
                        <span>Data Nascita</span>
                    </label>

                    <div class="gender-box">
                        <label>Sesso</label>
                        <div class="gender-option">
                            <div class="gender">
                                <input <?php echo ($gender === 'M') ? 'checked' : 'disabled'; ?> name="gender" id="check-male" type="radio" value="M">
                                <label for="check-male">Maschio</label>
                            </div>
                            <div class="gender">
                                <input <?php echo ($gender === 'F') ? 'checked' : 'disabled'; ?> name="gender" id="check-female" type="radio" value="F">
                                <label for="check-female">Femmina</label>
                            </div>
                            <div class="gender">
                                <input <?php echo ($gender === 'A') ? 'checked' : 'disabled'; ?> name="gender" id="check-other" type="radio" value="A">
                                <label for="check-other">Altro</label>
                            </div>
                        </div>

                        
                    </div>

                    <label>
                        <input type="email" id="email" name="email" class="input" placeholder="" value="<?php echo $email; ?>" readonly>
                        <span>Email</span>
                    </label>

                    <button type="shareButton" id="shareButton" name="shareButton" class="shareButton">Condividi</button>
                    <div id="notification" class="notification" style="display:none">Link copiato negli appunti</div>

                    <script>
                        document.getElementById('shareButton').addEventListener('click', function() {
                            // Link da copiare
                            var link = 'http://localhost/Sprotz/profilo/riepilogo.php';

                            

                            // Copia il link negli appunti
                            navigator.clipboard.writeText(link).then(function() {
                                notification.style.display = 'block'; // Mostra il messaggio di notifica
                                setTimeout(function() {
                                    notification.style.display = 'none'; // Nascondi il messaggio dopo 3 secondi
                                }, 3000);
                                console.log('Link copiato negli appunti');


                            }).catch(function(error) {
                                console.error('Errore nella copia del link: ', error);
                            });
                        });
                    </script>

                    <!-- Sezione dell'oroscopo -->
                    <div class="oroscopo-container">
                        <h3>Oroscopo del giorno: <?php echo ucfirst($segnoZodiacale); ?></h3>
                        <p><?php echo htmlspecialchars($translatedText); ?></p>
                    </div>

                </form>



            </div>
        </div>



    </section>

    <div class="container">
    </div>

    <script src="profilo.js"></script>
</body>

</html>