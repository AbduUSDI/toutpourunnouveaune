class AvisMedicaux {
    constructor(dbConnection) {
        this.db = dbConnection; // Connexion à la base de données
    }

    /**
     * Récupère les derniers avis médicaux, limités par un nombre donné
     * @param {number} limit - Nombre maximal d'avis à récupérer
     */
    async getDerniersAvis(limit = 5) {
        const query = `
            SELECT * 
            FROM conseils_medicaux 
            ORDER BY date_creation DESC 
            LIMIT ?
        `;
        try {
            const [rows] = await this.db.execute(query, [limit]);
            return rows;
        } catch (error) {
            console.error("Erreur lors de la récupération des derniers avis médicaux :", error.message);
            throw error;
        }
    }

    /**
     * Récupère les informations d'un avis médical par son ID
     * @param {number} id - ID de l'avis médical
     */
    async getParId(id) {
        const query = `
            SELECT u.nom_utilisateur
            FROM utilisateurs u
            JOIN conseils_medicaux c ON c.id = u.id
            WHERE c.id = ?
        `;
        try {
            const [rows] = await this.db.execute(query, [id]);
            return rows[0] || null;
        } catch (error) {
            console.error("Erreur lors de la récupération de l'avis médical par ID :", error.message);
            throw error;
        }
    }

    /**
     * Récupère tous les avis médicaux
     */
    async getAll() {
        const query = "SELECT * FROM conseils_medicaux";
        try {
            const [rows] = await this.db.execute(query);
            return rows;
        } catch (error) {
            console.error("Erreur lors de la récupération de tous les avis médicaux :", error.message);
            throw error;
        }
    }

    /**
     * Crée un nouvel avis médical
     * @param {string} titre - Titre de l'avis
     * @param {string} contenu - Contenu de l'avis
     * @param {number} medecinId - ID du médecin
     */
    async create(titre, contenu, medecinId) {
        const query = `
            INSERT INTO conseils_medicaux (titre, contenu, medecin_id)
            VALUES (?, ?, ?)
        `;
        try {
            const [result] = await this.db.execute(query, [titre, contenu, medecinId]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la création de l'avis médical :", error.message);
            return false;
        }
    }

    /**
     * Met à jour un avis médical
     * @param {number} id - ID de l'avis médical
     * @param {string} titre - Nouveau titre
     * @param {string} contenu - Nouveau contenu
     */
    async update(id, titre, contenu) {
        const query = `
            UPDATE conseils_medicaux
            SET titre = ?, contenu = ?
            WHERE id = ?
        `;
        try {
            const [result] = await this.db.execute(query, [titre, contenu, id]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la mise à jour de l'avis médical :", error.message);
            return false;
        }
    }

    /**
     * Supprime un avis médical par son ID
     * @param {number} id - ID de l'avis médical
     */
    async delete(id) {
        const query = "DELETE FROM conseils_medicaux WHERE id = ?";
        try {
            const [result] = await this.db.execute(query, [id]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la suppression de l'avis médical :", error.message);
            return false;
        }
    }
}

export default AvisMedicaux;
