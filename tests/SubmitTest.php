<?php

use PHPUnit\Framework\TestCase;

// Includi il file PHP che contiene la funzione `handleSubmit`
// Questo deve essere fatto per far sì che la funzione sia definita
require_once 'C:/xampp/htdocs/Sprotz/home/submit.php';

class SubmitTest extends TestCase
{
    protected function setUp(): void
    {
        // Configura un database di test o qualsiasi altro setup necessario
        // Connessione al database di test
        $this->connessione = $this->createMock(mysqli::class);
    }

    public function testAggiuntaAiContatti()
    {
        // Mock del risultato della query SELECT
        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_assoc')->willReturn(['risposta1' => 'risposta_corretta']);

        // Imposta il comportamento del metodo query
        $this->connessione->expects($this->exactly(2))
                           ->method('query')
                           ->withConsecutive(
                               [$this->stringContains('SELECT risposta1')],
                               [$this->stringContains('UPDATE utenti SET contatti')]
                           )
                           ->willReturnOnConsecutiveCalls($mockResult, true);

        // Avvia l'output buffering per evitare problemi con l'header redirect
        ob_start();

        // Configura i dati di test
        $_POST['username'] = 'testUser';
        $_POST['rispostaHidden'] = 'risposta_corretta';
        $_SESSION['email'] = 'user@example.com';

        // Chiama la funzione di gestione
        handleSubmit($this->connessione);

        // Termina l'output buffering
        ob_end_clean();
    }

    public function testAggiuntaAllaListaNera()
    {
        // Mock del risultato della query SELECT
        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->method('fetch_assoc')->willReturn(['risposta1' => 'risposta_corretta']);

        // Imposta il comportamento del metodo query
        $this->connessione->expects($this->exactly(2))
                           ->method('query')
                           ->withConsecutive(
                               [$this->stringContains('SELECT risposta1')],
                               [$this->stringContains('UPDATE utenti SET listanera')]
                           )
                           ->willReturnOnConsecutiveCalls($mockResult, true);

        // Avvia l'output buffering per evitare problemi con l'header redirect
        ob_start();

        // Configura i dati di test
        $_POST['username'] = 'testUser';
        $_POST['rispostaHidden'] = 'risposta_errata';
        $_SESSION['email'] = 'user@example.com';

        // Chiama la funzione di gestione
        handleSubmit($this->connessione);

        // Termina l'output buffering
        ob_end_clean();
    }
}
?>
