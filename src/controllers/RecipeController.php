<?php
namespace Controllers;

use Models\Recipe;

class RecipeController {
    private $recipe;

    public function __construct(Recipe $recipe) {
        $this->recipe = $recipe;
    }

    // Récupérer toutes les recettes
    public function getAllRecipes() {
        return $this->recipe->getAll();
    }

    // Créer une nouvelle recette
    public function createRecipe($titre, $ingredients, $instructions, $auteur_id) {
        if ($this->recipe->create($titre, $ingredients, $instructions, $auteur_id)) {
            return "Recette créée avec succès.";
        } else {
            return "Erreur lors de la création de la recette.";
        }
    }

    // Mettre à jour une recette existante
    public function updateRecipe($id, $titre, $ingredients, $instructions) {
        if ($this->recipe->update($id, $titre, $ingredients, $instructions)) {
            return "Recette mise à jour avec succès.";
        } else {
            return "Erreur lors de la mise à jour de la recette.";
        }
    }

    // Supprimer une recette
    public function deleteRecipe($id) {
        if ($this->recipe->delete($id)) {
            return "Recette supprimée avec succès.";
        } else {
            return "Erreur lors de la suppression de la recette.";
        }
    }
}
