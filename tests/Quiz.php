<?php

function createQuiz($question, $correctAnswer, $options) {
    return [
        'question' => $question,
        'correctAnswer' => $correctAnswer,
        'options' => $options
    ];
}

function getCorrectAnswer($quiz) {
    return $quiz['correctAnswer'];
}

function checkAnswer($quiz, $selectedAnswer) {
    return $selectedAnswer === $quiz['correctAnswer'];
}
?>
