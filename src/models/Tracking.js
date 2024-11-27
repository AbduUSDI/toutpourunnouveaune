class Tracking {
    constructor(dbConnection) {
        this.db = dbConnection; // Connexion à la base de données
    }

    /**
     * Récupère tous les suivis quotidiens
     */
    async getTracking() {
        try {
            const [rows] = await this.db.execute("SELECT * FROM suivi_quotidien ORDER BY date_creation");
            return rows;
        } catch (error) {
            console.error("Erreur lors de la récupération des suivis quotidiens :", error.message);
            throw new Error("Impossible de récupérer les suivis quotidiens");
        }
    }

    /**
     * Crée un nouveau suivi quotidien
     */
    async create(utilisateur_id, date, heure_repas, duree_repas, heure_change, medicament, notes) {
        try {
            const date_creation = new Date().toISOString().slice(0, 19).replace('T', ' ');
            const query = `
                INSERT INTO suivi_quotidien 
                (utilisateur_id, date, heure_repas, duree_repas, heure_change, medicament, notes, date_creation) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            `;
            const [result] = await this.db.execute(query, [
                utilisateur_id,
                date,
                heure_repas,
                duree_repas,
                heure_change,
                medicament,
                notes,
                date_creation
            ]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la création du suivi quotidien :", error.message);
            return false;
        }
    }

    /**
     * Met à jour un suivi quotidien existant
     */
    async update(id, utilisateur_id, date, heure_repas, duree_repas, heure_change, medicament, notes) {
        try {
            const query = `
                UPDATE suivi_quotidien 
                SET utilisateur_id = ?, date = ?, heure_repas = ?, duree_repas = ?, heure_change = ?, medicament = ?, notes = ?
                WHERE id = ?
            `;
            const [result] = await this.db.execute(query, [
                utilisateur_id,
                date,
                heure_repas,
                duree_repas,
                heure_change,
                medicament,
                notes,
                id
            ]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la mise à jour du suivi quotidien :", error.message);
            return false;
        }
    }

    /**
     * Supprime un suivi quotidien
     */
    async delete(id) {
        try {
            const query = "DELETE FROM suivi_quotidien WHERE id = ?";
            const [result] = await this.db.execute(query, [id]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la suppression du suivi quotidien :", error.message);
            return false;
        }
    }
}

export default Tracking;
