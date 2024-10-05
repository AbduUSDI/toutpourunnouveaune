<?php
namespace Models;

use PDO;
use Exception;


class Quiz {
    private $conn;
    private $table_quiz = 'quizzes';
    private $table_question = 'questions';
    private $table_answer = 'answers';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllQuizzes() {
        $query = "SELECT * FROM " . $this->table_quiz;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAnswersByQuestionId($question_id) {
        $query = "SELECT * FROM " . $this->table_answer . " WHERE question_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $question_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getQuestionsByQuizId($quiz_id) {
        $query = "SELECT * FROM " . $this->table_question . " WHERE quiz_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $quiz_id);
        $stmt->execute();
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($questions as &$question) {
            $question['answers'] = $this->getAnswersByQuestionId($question['id']);
        }
    
        return $questions;
    }
    public function getQuizById($quiz_id) {
        $query = "SELECT * FROM " . $this->table_quiz . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $quiz_id);
        $stmt->execute();
        $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $quiz['questions'] = $this->getQuestionsByQuizId($quiz_id);
        return $quiz;
    }

    public function addQuiz($titre, $questions) {
        try {
            $this->conn->beginTransaction();
    
            // Insert quiz
            $query = "INSERT INTO " . $this->table_quiz . " (titre) VALUES (:titre)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':titre', $titre);
            $stmt->execute();
            $quiz_id = $this->conn->lastInsertId();
    
            // Insert questions and answers
            foreach ($questions as $question) {
                $query = "INSERT INTO " . $this->table_question . " (quiz_id, question_text) VALUES (:quiz_id, :question_text)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':quiz_id', $quiz_id);
                $stmt->bindParam(':question_text', $question['question_text']);
                $stmt->execute();
                $question_id = $this->conn->lastInsertId();
    
                foreach ($question['answers'] as $answer) {
                    $is_correct = isset($answer['is_correct']) ? 1 : 0;
                    $query = "INSERT INTO " . $this->table_answer . " (question_id, answer_text, is_correct) VALUES (:question_id, :answer_text, :is_correct)";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':question_id', $question_id);
                    $stmt->bindParam(':answer_text', $answer['answer_text']);
                    $stmt->bindParam(':is_correct', $is_correct, PDO::PARAM_BOOL);
                    $stmt->execute();
                }
            }
    
            $this->conn->commit();
            return $quiz_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }    
    public function updateQuiz($id, $titre, $questions) {
        $query = "UPDATE " . $this->table_quiz . " SET titre = :titre WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    
        foreach ($questions as $question) {
            if (isset($question['id'])) {
                $query = "UPDATE " . $this->table_question . " SET question_text = :question_text WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':question_text', $question['question_text']);
                $stmt->bindParam(':id', $question['id']);
                $stmt->execute();
            } else {
                $query = "INSERT INTO " . $this->table_question . " (quiz_id, question_text) VALUES (:quiz_id, :question_text)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':quiz_id', $id);
                $stmt->bindParam(':question_text', $question['question_text']);
                $stmt->execute();
                $question['id'] = $this->conn->lastInsertId();
            }
    
            foreach ($question['answers'] as $answer) {
                if (isset($answer['id'])) {
                    $query = "UPDATE " . $this->table_answer . " SET answer_text = :answer_text, is_correct = :is_correct WHERE id = :id";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':answer_text', $answer['answer_text']);
                    $stmt->bindParam(':is_correct', $answer['is_correct'], PDO::PARAM_BOOL);
                    $stmt->bindParam(':id', $answer['id']);
                    $stmt->execute();
                } else {
                    $query = "INSERT INTO " . $this->table_answer . " (question_id, answer_text, is_correct) VALUES (:question_id, :answer_text, :is_correct)";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':question_id', $question['id']);
                    $stmt->bindParam(':answer_text', $answer['answer_text']);
                    $stmt->bindParam(':is_correct', $answer['is_correct'], PDO::PARAM_BOOL);
                    $stmt->execute();
                }
            }
        }
    }    
    public function deleteQuiz($id) {
        $query = "DELETE FROM " . $this->table_quiz . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
    }

    public function calculateScore($quiz_id, $user_answers) {
        $score = 0;
    
        foreach ($user_answers as $question_id => $answer_id) {
            $query = "SELECT is_correct FROM answers WHERE id = ? AND question_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $answer_id);
            $stmt->bindParam(2, $question_id);
            $stmt->execute();
            $answer = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($answer && $answer['is_correct']) {
                $score++;
            }
        }
    
        return $score;
    }
}