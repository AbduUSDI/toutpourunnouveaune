class ResponseTPUNN {
    constructor(dbConnection) {
        this.conn = dbConnection; // Connexion à la base de données
        this.table = 'responses'; // Nom de la table
    }

    /**
     * Crée une nouvelle réponse
     * @param {number} threadId - ID du fil de discussion
     * @param {number} userId - ID de l'utilisateur
     * @param {string} body - Contenu de la réponse
     */
    async createResponse(threadId, userId, body) {
        const query = `INSERT INTO ${this.table} (thread_id, user_id, body) VALUES (?, ?, ?)`;
        try {
            const [result] = await this.conn.execute(query, [threadId, userId, body]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la création de la réponse :", error.message);
            return false;
        }
    }

    /**
     * Récupère les réponses pour un fil de discussion
     * @param {number} threadId - ID du fil de discussion
     */
    async getResponsesByThreadId(threadId) {
        const query = `
            SELECT r.id, r.body, r.created_at, u.nom_utilisateur AS author 
            FROM ${this.table} r 
            JOIN utilisateurs u ON r.user_id = u.id 
            WHERE r.thread_id = ? 
            ORDER BY r.created_at ASC
        `;
        try {
            const [rows] = await this.conn.execute(query, [threadId]);
            return rows;
        } catch (error) {
            console.error("Erreur lors de la récupération des réponses :", error.message);
            return [];
        }
    }

    /**
     * Met à jour une réponse
     * @param {number} id - ID de la réponse
     * @param {string} body - Nouveau contenu de la réponse
     */
    async updateResponse(id, body) {
        const query = `UPDATE ${this.table} SET body = ? WHERE id = ?`;
        try {
            const [result] = await this.conn.execute(query, [body, id]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la mise à jour de la réponse :", error.message);
            return false;
        }
    }

    /**
     * Supprime une réponse
     * @param {number} responseId - ID de la réponse
     */
    async deleteResponse(responseId) {
        const query = `DELETE FROM ${this.table} WHERE id = ?`;
        try {
            const [result] = await this.conn.execute(query, [responseId]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la suppression de la réponse :", error.message);
            return false;
        }
    }

    /**
     * Récupère les réponses d'un utilisateur
     * @param {number} userId - ID de l'utilisateur
     */
    async getResponsesByUserId(userId) {
        const query = `
            SELECT r.id, r.body, r.created_at, t.title AS thread_title
            FROM ${this.table} r
            JOIN threads t ON r.thread_id = t.id
            WHERE r.user_id = ?
            ORDER BY r.created_at DESC
        `;
        try {
            const [rows] = await this.conn.execute(query, [userId]);
            return rows;
        } catch (error) {
            console.error("Erreur lors de la récupération des réponses par utilisateur :", error.message);
            return [];
        }
    }
}

export default ResponseTPUNN;
