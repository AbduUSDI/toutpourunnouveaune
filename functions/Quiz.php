<?php
class Quiz {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function getAllQuizzesWithQuestions() {
        $query = "SELECT quizzes.id as quiz_id, quizzes.titre, questions.id as question_id, questions.question_text, answers.id as answer_id, answers.answer_text, answers.is_correct
                  FROM quizzes
                  LEFT JOIN questions ON quizzes.id = questions.quiz_id
                  LEFT JOIN answers ON questions.id = answers.question_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $quizzes = [];
        foreach ($results as $row) {
            $quizId = $row['quiz_id'];
            $questionId = $row['question_id'];
            $answerId = $row['answer_id'];
    
            if (!isset($quizzes[$quizId])) {
                $quizzes[$quizId] = [
                    'id' => $quizId,
                    'titre' => $row['titre'],
                    'questions' => []
                ];
            }
    
            if (!isset($quizzes[$quizId]['questions'][$questionId])) {
                $quizzes[$quizId]['questions'][$questionId] = [
                    'id' => $questionId,
                    'question_text' => $row['question_text'],
                    'options' => []
                ];
            }
    
            if ($answerId) {
                $quizzes[$quizId]['questions'][$questionId]['options'][$answerId] = [
                    'id' => $answerId,
                    'answer_text' => $row['answer_text'],
                    'is_correct' => $row['is_correct']
                ];
            }
        }
    
        return $quizzes;
    }
    public function createQuiz() {
        $query = "INSERT INTO quizzes (titre) VALUES ('Nouveau Quiz')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
    
    public function addQuestion($quizId, $questionText) {
        $query = "INSERT INTO questions (quiz_id, question_text) VALUES (:quiz_id, :question_text)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quiz_id', $quizId);
        $stmt->bindParam(':question_text', $questionText);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
    
    public function addOption($questionId, $optionText, $isCorrect) {
        $query = "INSERT INTO answers (question_id, answer_text, is_correct) VALUES (:question_id, :answer_text, :is_correct)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':question_id', $questionId);
        $stmt->bindParam(':answer_text', $optionText);
        $stmt->bindParam(':is_correct', $isCorrect);
        $stmt->execute();
    }
    public function getQuizById($quizId) {
        $query = "SELECT * FROM quizzes WHERE id = :quiz_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quiz_id', $quizId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getQuestionsByQuizId($quizId) {
        $query = "SELECT * FROM questions WHERE quiz_id = :quiz_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quiz_id', $quizId);
        $stmt->execute();
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($questions as &$question) {
            $query = "SELECT * FROM answers WHERE question_id = :question_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':question_id', $question['id']);
            $stmt->execute();
            $question['options'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
        return $questions;
    }
    
    public function updateQuiz($quizId, $questions) {
        foreach ($questions as $question) {
            $query = "UPDATE questions SET question_text = :question_text WHERE id = :question_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':question_text', $question['text']);
            $stmt->bindParam(':question_id', $question['id']);
            $stmt->execute();
    
            foreach ($question['options'] as $optionId => $optionText) {
                $isCorrect = ($question['correct'] == $optionId) ? 1 : 0;
                $query = "UPDATE answers SET answer_text = :answer_text, is_correct = :is_correct WHERE id = :option_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':answer_text', $optionText);
                $stmt->bindParam(':is_correct', $isCorrect);
                $stmt->bindParam(':option_id', $optionId);
                $stmt->execute();
            }
        }
    }
    
    public function deleteQuiz($quizId) {
        // Supprimer les réponses associées aux questions du quiz
        $query = "DELETE answers FROM answers 
                  INNER JOIN questions ON answers.question_id = questions.id 
                  WHERE questions.quiz_id = :quiz_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quiz_id', $quizId);
        $stmt->execute();
        // Supprimer les questions associées au quiz
        $query = "DELETE FROM questions WHERE quiz_id = :quiz_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quiz_id', $quizId);
        $stmt->execute();
        // Supprimer le quiz
        $query = "DELETE FROM quizzes WHERE id = :quiz_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quiz_id', $quizId);
        $stmt->execute();
        }
        public function getCorrectOption($questionId) {
            $query = "SELECT id FROM answers WHERE question_id = :question_id AND is_correct = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':question_id', $questionId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id'] : null;
        }
}