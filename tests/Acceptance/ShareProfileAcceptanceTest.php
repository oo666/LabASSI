<?php

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\Support\ExpectedConditions;

class ShareProfileAcceptanceTest extends TestCase
{
    protected $webDriver;

    protected function setUp(): void
    {
        $host = 'http://localhost:4444'; // Indirizzo di Selenium
        $capabilities = DesiredCapabilities::chrome();
        $this->webDriver = RemoteWebDriver::create($host, $capabilities);
    }

    public function testShareProfileLink()
    {
        // Accedi alla home
        $this->webDriver->get('http://localhost/Sprotz/accedi/accedi.php');
        sleep(1); // Ritardo per visualizzare la pagina di accesso

        // Inserisci le credenziali e accedi
        $this->webDriver->findElement(WebDriverBy::name('email'))->sendKeys('cleliab2002@gmail.com');
        $this->webDriver->findElement(WebDriverBy::name('password1'))->sendKeys('Clelia2002');
        $this->webDriver->findElement(WebDriverBy::id('entra'))->click();
        sleep(1); // Ritardo per visualizzare l'accesso

        // Verifica che la home page sia stata caricata
        $this->assertStringContainsString('Home', $this->webDriver->getTitle());
        sleep(1); // Ritardo per visualizzare la pagina home

        // Vai alla pagina del profilo
        $this->webDriver->get('http://localhost/Sprotz/profilo/profilo.php');
        sleep(1); // Ritardo per visualizzare la pagina del profilo

        // Clicca sul pulsante "Condividi"
        $shareButton = $this->webDriver->findElement(WebDriverBy::id('shareButton'));
        $shareButton->click();
        sleep(1); // Ritardo per visualizzare l'azione del pulsante

        // Ottieni l'URL della pagina di riepilogo del profilo
        $expectedProfileLink = 'http://localhost/Sprotz/profilo/riepilogo.php';
        
        // Usa il JavaScript per ottenere il link copiato negli appunti
        $copiedLink = $this->webDriver->executeScript(
            "return navigator.clipboard.readText();"
        );
        
        // Verifica che il link copiato sia corretto
        $this->assertEquals($expectedProfileLink, $copiedLink);

        // Naviga al link copiato
        $this->webDriver->get($copiedLink);
        sleep(4); // Ritardo per visualizzare la pagina di riepilogo

       
    }

    protected function tearDown(): void
    {
        $this->webDriver->quit();
    }
}
