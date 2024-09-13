<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require 'vendor/autoload.php';


class MyWebSocketServer implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    //quando si apre la connessione faccio richiesta al database di tutti i messaggi così da caricarli in una lista del javascript
    public function onOpen(ConnectionInterface $conn)
    {
        // memorizza la nuova connessione del cliente
        $this->clients->attach($conn);

        // se i messaggi non sono ancora stati inviati, ottieni i messaggi dal database e inviali al nuovo client
        $messaggiStampati = array();
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "registrazione";

        $connessione = new mysqli($servername, $username, $password, $dbname);

        if ($connessione->connect_error) {
            die("Connessione al database fallita: " . $connessione->connect_error);
        }

        // esegui la query per ottenere i messaggi dal database
        $sql = "SELECT * FROM messaggi";
        $result = $connessione->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $messaggiStampati[] = $row;
            }
        }

        $connessione->close();

        // invia i messaggi al nuovo client WebSocket
        $conn->send(json_encode($messaggiStampati));



        echo ("Nuova connessione: {$conn->resourceId}\n");
    }

    public function onMessage(ConnectionInterface $from, $data)
    {
        //suddivide $data in una lista
        $list = explode('|', $data);
        //potrebbe arrivare una richiesta di aggiornaNotifica quindi la lunghezza della lista è 3
        if (count($list) == 3) {
            list($visualizzato, $username_contatti, $username_utente) = explode('|', $data);
            $this->aggiornaNotifica($visualizzato, $username_contatti, $username_utente);
        } else {
            //divide $data in più pezzi
            list($messaggio, $username_contatti, $username_utente, $time_stamp) = explode('|', $data);

            //inserisce il contatto se non lo si ha
            $this->inserimento_contatti($username_contatti, $username_utente);

            // salva il messaggio ricevuto nel database
            foreach ($this->clients as $client) {
                //invia il messaggio a tutti i client tranne a quello che ha inviato
                if ($from !== $client) {
                    // costruisci un array associativo con le variabili da inviare
                    $data = array(
                        'messaggio' => $messaggio,
                        'username_contatti' => $username_contatti,
                        'username_utente' => $username_utente,
                        'time_stamp' => $time_stamp
                    );
                    // serializza l'array in formato JSON per l'invio
                    $json_data = json_encode($data);
                    // invia i dati al client
                    $client->send($json_data);
                }
            }
            //salva il messaggio nel database
            $this->saveMessageToDatabase($messaggio, $username_contatti, $username_utente, $time_stamp);
        }
    }

    private function aggiornaNotifica($visualizzato, $username_contatti, $username_utente)
    {
        $servername = "localhost";
        $username_db = "root";
        $password = "";
        $dbname = "registrazione";

        $conn = new mysqli($servername, $username_db, $password, $dbname);

        if ($conn->connect_error) {
            die("Connessione al database fallita: " . $conn->connect_error);
        }

        // query per selezionare i nomi dalla tabella degli utenti
        $sql = "SELECT * FROM messaggi";
        $result = $conn->query($sql);

        // verifica se ci sono risultati
        if ($result->num_rows > 0) {
            // output dei dati
            while ($row = $result->fetch_assoc()) {
                if ($username_contatti == $row["username_contatti"]) {

                    //modifico il valore "visualizzato" nel database per la notifica
                    $query = "UPDATE messaggi SET visualizzato = 'si' WHERE username_contatti = '$username_utente' and username_utente = '$username_contatti'";
                    if ($conn->query($query) === TRUE) {
                        echo "Contatto inserito.";
                    } else {
                        echo "Contatto non inserito.";
                    }
                }
            }
        }
        $conn->close();
    }

    private function inserimento_contatti($username_contatti, $username_utente)
    {
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
                if ($username_contatti == $row["username"]) {
                    $stringa = $row["contatti"];
                    // verifica se il $username_utente sta nella lista contatti di $username_contatti
                    if (!strstr($stringa, $username_utente)) {
                        //concatena $username_utente
                        $query = "UPDATE utenti SET contatti = CONCAT(contatti, '$username_utente, ') WHERE username = '$username_contatti'";
                        if ($conn->query($query) === TRUE) {
                            echo "Contatto inserito.";
                        } else {
                            echo "Contatto non inserito.";
                        }
                    }
                }
            }
        }
        $conn->close();
    }

    private function saveMessageToDatabase($messaggio, $username_contatti, $username_utente, $time_stamp)
    {
        // Connessione al database
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "registrazione";

        $connessione = new mysqli($servername, $username, $password, $dbname);

        if ($connessione->connect_error) {
            die("Connessione al database fallita: " . $connessione->connect_error);
        }


        // esegui la query per inserire il messaggio nel database (di default il valore "visualizzato" si salva con "no" ma viene subito cambiato se serve)
        $sql = "INSERT INTO messaggi ( messaggio, username_contatti, username_utente, time_stamp, visualizzato) VALUES ('$messaggio','$username_contatti','$username_utente', '$time_stamp', 'no')";

        if ($connessione->query($sql) === TRUE) {
            echo "Messaggio salvato nel database con successo.";
        } else {
            echo "Errore durante il salvataggio del messaggio nel database: " . $connessione->error;
        }

        $connessione->close();
    }


    public function onClose(ConnectionInterface $conn)
    {
        // Rimuovi la connessione del cliente quando si disconnette
        $this->clients->detach($conn);

        echo "Connessione chiusa: {$conn->resourceId}\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Errore: {$e->getMessage()}\n";

        $conn->close();
    }
}

// Avvia il server WebSocket su localhost sulla porta 8080
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new MyWebSocketServer()
        )
    ),
    8080
);

echo "Server WebSocket avviato su localhost:8080\n";

// Avvia il server
$server->run();