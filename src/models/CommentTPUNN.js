class CommentTPUNN {
    constructor(dbConnection) {
        this.conn = dbConnection; // Connexion à la base de données
    }

    /**
     * Récupère les commentaires approuvés pour un guide donné
     * @param {number} guideId - ID du guide
     */
    async getApprovedCommentsByGuideId(guideId) {
        const query = `
            SELECT c.*, u.nom_utilisateur
            FROM commentaires c
            JOIN utilisateurs u ON c.user_id = u.id
            WHERE c.guide_id = ? AND c.approuve = 1
            ORDER BY c.date_creation DESC
        `;
        try {
            const [rows] = await this.conn.execute(query, [guideId]);
            return rows;
        } catch (error) {
            console.error("Erreur lors de la récupération des commentaires approuvés :", error.message);
            throw error;
        }
    }

    /**
     * Ajoute un nouveau commentaire pour un guide
     * @param {number} guideId - ID du guide
     * @param {number} userId - ID de l'utilisateur
     * @param {string} contenu - Contenu du commentaire
     */
    async addComment(guideId, userId, contenu) {
        const query = `
            INSERT INTO commentaires (guide_id, user_id, contenu, approuve)
            VALUES (?, ?, ?, 0)
        `;
        try {
            const [result] = await this.conn.execute(query, [guideId, userId, contenu]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de l'ajout du commentaire :", error.message);
            throw error;
        }
    }

    /**
     * Approuve un commentaire par son ID
     * @param {number} commentId - ID du commentaire
     */
    async approveComment(commentId) {
        const query = "UPDATE commentaires SET approuve = 1 WHERE id = ?";
        try {
            const [result] = await this.conn.execute(query, [commentId]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de l'approbation du commentaire :", error.message);
            throw error;
        }
    }

    /**
     * Supprime un commentaire par son ID
     * @param {number} commentId - ID du commentaire
     */
    async deleteComment(commentId) {
        const query = "DELETE FROM commentaires WHERE id = ?";
        try {
            const [result] = await this.conn.execute(query, [commentId]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la suppression du commentaire :", error.message);
            throw error;
        }
    }

    /**
     * Récupère tous les commentaires en attente d'approbation
     */
    async getPendingComments() {
        const query = `
            SELECT c.*, u.nom_utilisateur, g.titre AS guide_titre
            FROM commentaires c
            JOIN utilisateurs u ON c.user_id = u.id
            JOIN guides g ON c.guide_id = g.id
            WHERE c.approuve = 0
            ORDER BY c.date_creation DESC
        `;
        try {
            const [rows] = await this.conn.execute(query);
            return rows;
        } catch (error) {
            console.error("Erreur lors de la récupération des commentaires en attente :", error.message);
            throw error;
        }
    }

    /**
     * Récupère tous les commentaires approuvés
     */
    async getApprovedComments() {
        const query = `
            SELECT c.*, u.nom_utilisateur, g.titre AS guide_titre
            FROM commentaires c
            JOIN utilisateurs u ON c.user_id = u.id
            JOIN guides g ON c.guide_id = g.id
            WHERE c.approuve = 1
            ORDER BY c.date_creation DESC
        `;
        try {
            const [rows] = await this.conn.execute(query);
            return rows;
        } catch (error) {
            console.error("Erreur lors de la récupération des commentaires approuvés :", error.message);
            throw error;
        }
    }

    /**
     * Récupère un commentaire par son ID
     * @param {number} id - ID du commentaire
     */
    async getCommentById(id) {
        const query = "SELECT * FROM commentaires WHERE id = ?";
        try {
            const [rows] = await this.conn.execute(query, [id]);
            return rows[0] || null;
        } catch (error) {
            console.error("Erreur lors de la récupération du commentaire :", error.message);
            throw error;
        }
    }

    /**
     * Met à jour le contenu d'un commentaire
     * @param {number} id - ID du commentaire
     * @param {string} content - Nouveau contenu du commentaire
     */
    async updateComment(id, content) {
        const query = "UPDATE commentaires SET contenu = ? WHERE id = ?";
        try {
            const [result] = await this.conn.execute(query, [content, id]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la mise à jour du commentaire :", error.message);
            throw error;
        }
    }
}

export default CommentTPUNN;
