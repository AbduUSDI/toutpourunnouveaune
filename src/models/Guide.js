class Guide {
    constructor(dbConnection) {
        this.conn = dbConnection; // Connexion à la base de données
    }

    /**
     * Crée un nouveau guide
     * @param {string} titre - Titre du guide
     * @param {string} contenu - Contenu du guide
     * @param {number} auteurId - ID de l'auteur
     */
    async createGuide(titre, contenu, auteurId) {
        const query = `
            INSERT INTO guides (titre, contenu, auteur_id)
            VALUES (?, ?, ?)
        `;
        try {
            const [result] = await this.conn.execute(query, [titre, contenu, auteurId]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la création du guide :", error.message);
            throw error;
        }
    }

    /**
     * Récupère tous les guides avec leurs auteurs
     */
    async getAllGuides() {
        const query = `
            SELECT g.*, u.nom_utilisateur AS auteur_nom
            FROM guides g
            JOIN utilisateurs u ON g.auteur_id = u.id
            ORDER BY g.date_creation DESC
        `;
        try {
            const [rows] = await this.conn.execute(query);
            return rows;
        } catch (error) {
            console.error("Erreur lors de la récupération des guides :", error.message);
            throw error;
        }
    }

    /**
     * Récupère un guide par son ID
     * @param {number} id - ID du guide
     */
    async getGuideById(id) {
        const query = `
            SELECT g.*, u.nom_utilisateur AS auteur_nom
            FROM guides g
            JOIN utilisateurs u ON g.auteur_id = u.id
            WHERE g.id = ?
        `;
        try {
            const [rows] = await this.conn.execute(query, [id]);
            return rows[0] || null;
        } catch (error) {
            console.error("Erreur lors de la récupération du guide :", error.message);
            throw error;
        }
    }

    /**
     * Met à jour un guide
     * @param {number} id - ID du guide
     * @param {string} titre - Nouveau titre
     * @param {string} contenu - Nouveau contenu
     */
    async updateGuide(id, titre, contenu) {
        const query = `
            UPDATE guides
            SET titre = ?, contenu = ?, date_mise_a_jour = CURRENT_TIMESTAMP()
            WHERE id = ?
        `;
        try {
            const [result] = await this.conn.execute(query, [titre, contenu, id]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la mise à jour du guide :", error.message);
            throw error;
        }
    }

    /**
     * Supprime un guide
     * @param {number} id - ID du guide
     */
    async deleteGuide(id) {
        const query = "DELETE FROM guides WHERE id = ?";
        try {
            const [result] = await this.conn.execute(query, [id]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la suppression du guide :", error.message);
            throw error;
        }
    }
}

export default Guide;
