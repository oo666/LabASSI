<?php

namespace Functional;

use Tests\Support\Actor\FunctionalTester;

/** @var FunctionalTester $I */
class QuizFunctionalCest
{
    public function solveQuizAndAddContact(FunctionalTester $I)
    {
        $I->wantTo('Solve a quiz and add a new user to my contacts');

        // Assume that a user is logged in
        $I->amOnPage('/Sprotz/accedi/accedi.php');
        $I->fillField('email', 'cleliab2002@gmail.com');
        $I->fillField('password1', 'Clelia2002');
        $I->click('Entra');
        $I->see('Home');

        // Vai alla pagina principale
        $I->amOnPage('/Sprotz/home/index.php');
        $I->see('Home');

        // Trova e clicca sul pulsante del quiz
        $I->click('Provaci!', ['data-username' => 'giovannicasinoo']);

        // Verifica la presenza del popup del quiz
        $I->seeElement('#quizPopup');
        //$I->see('Il mio anime preferito è senza dubbio Inazuma Eleven, ma quale personaggio mi piacerebbe essere?');

        // Seleziona la risposta corretta
        $I->click('input[name="risposta"][value="Xavier Foster"]'); // Supponendo che il radio button abbia il valore 'Xavier Foster'
        $I->click('Invia la risposta');

        // Verifica che il creatore del quiz sia aggiunto ai contatti
        $I->amOnPage('/Sprotz/chat/chat.php');
        $I->see('giovannicasinoo');
    }
}
