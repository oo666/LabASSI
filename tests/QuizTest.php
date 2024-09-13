<?php
use PHPUnit\Framework\TestCase;

// Includi il file di simulazione
require_once 'quiz.php';

class QuizTest extends TestCase
{
    public function testGetCorrectAnswer()
    {
        $quiz = createQuiz("What is the capital of France?", "Paris", ["Berlin", "Paris", "London", "Rome"]);
        $this->assertEquals("Paris", getCorrectAnswer($quiz));
    }

    public function testCheckAnswer()
    {
        $quiz = createQuiz("What is the capital of France?", "Paris", ["Berlin", "Paris", "London", "Rome"]);
        $this->assertTrue(checkAnswer($quiz, "Paris"));
        $this->assertFalse(checkAnswer($quiz, "Berlin"));
    }
}
?>
