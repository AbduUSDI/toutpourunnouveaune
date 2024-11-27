class ProfileTPUNN {
    constructor(dbConnection) {
        this.db = dbConnection; // Connexion à la base de données
    }

    /**
     * Récupère le profil d'un utilisateur par son ID
     * @param {number} userId - ID de l'utilisateur
     */
    async getProfileByUserId(userId) {
        const query = "SELECT * FROM profils WHERE utilisateur_id = ?";
        try {
            const [rows] = await this.db.execute(query, [userId]);
            return rows[0] || null;
        } catch (error) {
            console.error("Erreur lors de la récupération du profil :", error.message);
            throw error;
        }
    }

    /**
     * Sauvegarde ou met à jour un profil
     * @param {number} userId - ID de l'utilisateur
     * @param {string} firstName - Prénom de l'utilisateur
     * @param {string} lastName - Nom de l'utilisateur
     * @param {string} birthDate - Date de naissance de l'utilisateur
     * @param {string} biography - Biographie de l'utilisateur
     * @param {string|null} imageName - Nom du fichier d'image (optionnel)
     */
    async saveProfile(userId, firstName, lastName, birthDate, biography, imageName = null) {
        try {
            // Vérifie si le profil existe
            const existsQuery = "SELECT COUNT(*) AS count FROM profils WHERE utilisateur_id = ?";
            const [existsRows] = await this.db.execute(existsQuery, [userId]);
            const exists = existsRows[0].count > 0;

            let query;
            const params = [firstName, lastName, birthDate, biography];

            if (exists) {
                // Met à jour le profil existant
                query = `
                    UPDATE profils
                    SET prenom = ?, nom = ?, date_naissance = ?, biographie = ?
                `;
                if (imageName) {
                    query += ", photo_profil = ?";
                    params.push(imageName);
                }
                query += " WHERE utilisateur_id = ?";
                params.push(userId);
            } else {
                // Crée un nouveau profil
                query = `
                    INSERT INTO profils (utilisateur_id, prenom, nom, date_naissance, biographie, photo_profil)
                    VALUES (?, ?, ?, ?, ?, ?)
                `;
                params.unshift(userId); // Ajoute l'ID utilisateur au début
                params.push(imageName); // Ajoute l'image (ou null) à la fin
            }

            const [result] = await this.db.execute(query, params);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la sauvegarde du profil :", error.message);
            throw error;
        }
    }

    /**
     * Met à jour la photo de profil
     * @param {number} userId - ID de l'utilisateur
     * @param {string} imageName - Nom du fichier d'image
     */
    async updateProfilePicture(userId, imageName) {
        const query = "UPDATE profils SET photo_profil = ? WHERE utilisateur_id = ?";
        try {
            const [result] = await this.db.execute(query, [imageName, userId]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la mise à jour de la photo de profil :", error.message);
            throw error;
        }
    }
}

export default ProfileTPUNN;
