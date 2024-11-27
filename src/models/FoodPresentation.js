class FoodPresentation {
    constructor(dbConnection) {
        this.conn = dbConnection; // Connexion à la base de données
    }

    /**
     * Récupère toutes les présentations alimentaires
     */
    async getAll() {
        const query = "SELECT * FROM presentations_alimentaires";
        try {
            const [rows] = await this.conn.execute(query);
            return rows;
        } catch (error) {
            console.error("Erreur lors de la récupération des présentations alimentaires :", error.message);
            throw error;
        }
    }

    /**
     * Crée une nouvelle présentation alimentaire
     * @param {string} titre - Titre de la présentation
     * @param {string} contenu - Contenu de la présentation
     * @param {string} groupe_age - Groupe d'âge associé
     * @param {number} medecin_id - ID du médecin associé
     */
    async create(titre, contenu, groupe_age, medecin_id) {
        const query = `
            INSERT INTO presentations_alimentaires (titre, contenu, groupe_age, medecin_id)
            VALUES (?, ?, ?, ?)
        `;
        try {
            const [result] = await this.conn.execute(query, [titre, contenu, groupe_age, medecin_id]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la création de la présentation alimentaire :", error.message);
            return false;
        }
    }

    /**
     * Met à jour une présentation alimentaire
     * @param {number} id - ID de la présentation
     * @param {string} titre - Nouveau titre
     * @param {string} contenu - Nouveau contenu
     * @param {string} groupe_age - Nouveau groupe d'âge
     */
    async update(id, titre, contenu, groupe_age) {
        const query = `
            UPDATE presentations_alimentaires
            SET titre = ?, contenu = ?, groupe_age = ?
            WHERE id = ?
        `;
        try {
            const [result] = await this.conn.execute(query, [titre, contenu, groupe_age, id]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la mise à jour de la présentation alimentaire :", error.message);
            return false;
        }
    }

    /**
     * Supprime une présentation alimentaire
     * @param {number} id - ID de la présentation
     */
    async delete(id) {
        const query = "DELETE FROM presentations_alimentaires WHERE id = ?";
        try {
            const [result] = await this.conn.execute(query, [id]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la suppression de la présentation alimentaire :", error.message);
            return false;
        }
    }
}

export default FoodPresentation;
