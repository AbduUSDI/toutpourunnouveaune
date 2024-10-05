<?php
namespace Controllers;

use Models\Comment;

class CommentController {
    private $comment;

    public function __construct(Comment $comment) {
        $this->comment = $comment;
    }

    // Récupérer les commentaires approuvés par ID de guide
    public function getApprovedCommentsByGuideId($guide_id) {
        return $this->comment->getApprovedCommentsByGuideId($guide_id);
    }

    // Ajouter un nouveau commentaire
    public function addComment($guide_id, $user_id, $contenu) {
        if ($this->comment->addComment($guide_id, $user_id, $contenu)) {
            return "Commentaire ajouté avec succès.";
        } else {
            return "Erreur lors de l'ajout du commentaire.";
        }
    }

    // Approuver un commentaire
    public function approveComment($comment_id) {
        if ($this->comment->approveComment($comment_id)) {
            return "Commentaire approuvé avec succès.";
        } else {
            return "Erreur lors de l'approbation du commentaire.";
        }
    }

    // Supprimer un commentaire
    public function deleteComment($comment_id) {
        if ($this->comment->deleteComment($comment_id)) {
            return "Commentaire supprimé avec succès.";
        } else {
            return "Erreur lors de la suppression du commentaire.";
        }
    }

    // Récupérer les commentaires en attente d'approbation
    public function getPendingComments() {
        return $this->comment->getPendingComments();
    }

    // Récupérer les commentaires approuvés
    public function getApprovedComments() {
        return $this->comment->getApprovedComments();
    }

    // Récupérer un commentaire par son ID
    public function getCommentById($id) {
        return $this->comment->getCommentById($id);
    }

    // Mettre à jour un commentaire
    public function updateComment($id, $content) {
        if ($this->comment->updateComment($id, $content)) {
            return "Commentaire mis à jour avec succès.";
        } else {
            return "Erreur lors de la mise à jour du commentaire.";
        }
    }
}
