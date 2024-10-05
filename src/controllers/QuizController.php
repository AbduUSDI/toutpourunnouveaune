<?php
namespace Controllers;

use Models\Quiz;

class QuizController {
    private $quiz;

    public function __construct(Quiz $quiz) {
        $this->quiz = $quiz;
    }

    // Récupérer tous les quizzes
    public function getAllQuizzes() {
        return $this->quiz->getAllQuizzes();
    }

    // Récupérer un quiz par son ID
    public function getQuizById($quiz_id) {
        return $this->quiz->getQuizById($quiz_id);
    }

    // Ajouter un nouveau quiz
    public function addQuiz($titre, $questions) {
        try {
            $quiz_id = $this->quiz->addQuiz($titre, $questions);
            return "Quiz ajouté avec succès, ID : $quiz_id";
        } catch (\Exception $e) {
            return "Erreur lors de l'ajout du quiz : " . $e->getMessage();
        }
    }

    // Mettre à jour un quiz existant
    public function updateQuiz($id, $titre, $questions) {
        try {
            $this->quiz->updateQuiz($id, $titre, $questions);
            return "Quiz mis à jour avec succès.";
        } catch (\Exception $e) {
            return "Erreur lors de la mise à jour du quiz : " . $e->getMessage();
        }
    }

    // Supprimer un quiz
    public function deleteQuiz($id) {
        try {
            $this->quiz->deleteQuiz($id);
            return "Quiz supprimé avec succès.";
        } catch (\Exception $e) {
            return "Erreur lors de la suppression du quiz : " . $e->getMessage();
        }
    }

    // Calculer le score d'un utilisateur pour un quiz donné
    public function calculateScore($quiz_id, $user_answers) {
        try {
            $score = $this->quiz->calculateScore($quiz_id, $user_answers);
            return $score;
        } catch (\Exception $e) {
            return "Erreur lors du calcul du score : " . $e->getMessage();
        }
    }
}
