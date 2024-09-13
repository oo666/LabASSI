<?php
use PHPUnit\Framework\TestCase;

// Includi il file di simulazione
require_once 'Link.php';

class LinkTest extends TestCase
{
    public function testCopyProfileLinkToClipboard()
    {
    

        // Simula la copia del link
        $expectedLink = 'http://localhost/Sprotz/profilo/riepilogo.php';
        $actualLink = copyProfileLinkToClipboard();

        $this->assertEquals($expectedLink, $actualLink);
    }
}
?>