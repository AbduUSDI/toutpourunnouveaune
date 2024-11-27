class Recipe {
    constructor(dbConnection) {
        this.conn = dbConnection; // Connexion à la base de données
    }

    /**
     * Récupère toutes les recettes
     */
    async getAll() {
        const query = 'SELECT * FROM recettes';
        try {
            const [rows] = await this.conn.execute(query);
            return rows;
        } catch (error) {
            console.error('Erreur lors de la récupération des recettes :', error.message);
            throw new Error('Impossible de récupérer les recettes');
        }
    }

    /**
     * Crée une nouvelle recette
     */
    async create(titre, ingredients, instructions, auteur_id) {
        const query = `
            INSERT INTO recettes (titre, ingredients, instructions, auteur_id)
            VALUES (?, ?, ?, ?)
        `;
        try {
            const [result] = await this.conn.execute(query, [titre, ingredients, instructions, auteur_id]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error('Erreur lors de la création de la recette :', error.message);
            return false;
        }
    }

    /**
     * Met à jour une recette
     */
    async update(id, titre, ingredients, instructions) {
        const query = `
            UPDATE recettes
            SET titre = ?, ingredients = ?, instructions = ?
            WHERE id = ?
        `;
        try {
            const [result] = await this.conn.execute(query, [titre, ingredients, instructions, id]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error('Erreur lors de la mise à jour de la recette :', error.message);
            return false;
        }
    }

    /**
     * Supprime une recette
     */
    async delete(id) {
        const query = 'DELETE FROM recettes WHERE id = ?';
        try {
            const [result] = await this.conn.execute(query, [id]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error('Erreur lors de la suppression de la recette :', error.message);
            return false;
        }
    }
}

export default Recipe;
