<?php

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\Support\ExpectedConditions;

class QuizFunctionalTest extends TestCase
{
    protected $webDriver;

    protected function setUp(): void
    {
        $host = 'http://localhost:4444'; // Indirizzo di Selenium
        $capabilities = DesiredCapabilities::chrome();
        $this->webDriver = RemoteWebDriver::create($host, $capabilities);
    }

    public function testSolveQuizAndSendMessage()
    {
        // Accedi alla home
        $this->webDriver->get('http://localhost/Sprotz/accedi/accedi.php');
        sleep(1); 
        
        // Inserisci le credenziali e accedi
        $this->webDriver->findElement(WebDriverBy::name('email'))->sendKeys('cleliab2002@gmail.com');
        $this->webDriver->findElement(WebDriverBy::name('password1'))->sendKeys('Clelia2002');
        $this->webDriver->findElement(WebDriverBy::id('entra'))->click();
        sleep(1); 

        // Verifica che la home page sia stata caricata
        $this->assertStringContainsString('Home', $this->webDriver->getTitle());
        sleep(1);

        // Vai alla home 
        $this->webDriver->get('http://localhost/Sprotz/home/index.php');
        sleep(1); 

        // Clicca sul primo pulsante "Provaci" con l'id quizzamento
        $buttons = $this->webDriver->findElements(WebDriverBy::id('quizzamento'));
        if (count($buttons) > 0) {
            $buttons[0]->click();
        } else {
            $this->fail('Nessun pulsante "Provaci" trovato');
        }
        sleep(1); 

        // Aspetta che il popup del quiz sia visibile
        $wait = new WebDriverWait($this->webDriver, 10); 
        $wait->until(function($driver) {
            return $driver->findElement(WebDriverBy::id('quizPopup'))->isDisplayed();
        });
        sleep(1);

        // Seleziona il radio button con l'id "value-2"
        $radioButton = $this->webDriver->findElement(WebDriverBy::id("value-2"));
        $this->webDriver->executeScript('arguments[0].click();', [$radioButton]);
        sleep(1); 

        // Invia la risposta
        $submitButton = $this->webDriver->findElement(WebDriverBy::cssSelector('button.submitQuiz'));
        $wait->until(function($driver) use ($submitButton) {
            return $submitButton->isEnabled() && $submitButton->isDisplayed();
        });
        $submitButton->click();
        sleep(1); 

        // Vai alla pagina della chat
        $this->webDriver->get('http://localhost/Sprotz/chat/chat.php');
        sleep(2); 

        // Verifica la presenza di almeno un utente nella lista dei contatti
        $users = $this->webDriver->findElements(WebDriverBy::cssSelector('#list-utenti .click-utente'));
        if (count($users) > 0) {
            // Seleziona il primo utente nella lista
            $users[0]->click();
            sleep(1); 

            // Scrivi un messaggio e invialo
            $messageInput = $this->webDriver->findElement(WebDriverBy::id('message-input'));
            $sendButton = $this->webDriver->findElement(WebDriverBy::id('send-Button'));
            
            $messageInput->sendKeys('ciao!');
            sleep(1); 

            $sendButton->click();
            sleep(1); 
        } else {
            $this->fail('Nessun utente disponibile nella lista dei contatti');
        }
    }

    protected function tearDown(): void
    {
        $this->webDriver->quit();
    }
}
