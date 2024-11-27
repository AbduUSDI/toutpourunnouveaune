class UserOne {
    constructor(dbConnection) {
        this.conn = dbConnection; // Instance de connexion MySQL
    }

    /**
     * Récupère tous les utilisateurs
     */
    async getAllUtilisateurs() {
        const [rows] = await this.conn.execute("SELECT * FROM utilisateurs");
        return rows;
    }

    /**
     * Récupère un utilisateur par son email
     * @param {string} email - Email de l'utilisateur
     */
    async getUtilisateurParEmail(email) {
        const [rows] = await this.conn.execute("SELECT * FROM utilisateurs WHERE email = ?", [email]);
        return rows[0];
    }

    /**
     * Vérifie si un email existe
     * @param {string} email - Email à vérifier
     */
    async getEmail(email) {
        const [rows] = await this.conn.execute("SELECT COUNT(*) AS count FROM utilisateurs WHERE email = ?", [email]);
        return rows[0].count > 0;
    }

    /**
     * Récupère un utilisateur par son ID
     * @param {number} id - ID de l'utilisateur
     */
    async getUtilisateurParId(id) {
        const [rows] = await this.conn.execute("SELECT * FROM utilisateurs WHERE id = ?", [id]);
        return rows[0];
    }

    /**
     * Ajoute un nouvel utilisateur
     * @param {string} email - Email de l'utilisateur
     * @param {string} password - Mot de passe non haché
     * @param {number} role_id - Rôle de l'utilisateur
     * @param {string} username - Nom d'utilisateur
     */
    async addUser(email, password, role_id, username) {
        const hashedPassword = await bcrypt.hash(password, 10); // Hachage du mot de passe
        const [result] = await this.conn.execute(
            "INSERT INTO utilisateurs (email, mot_de_passe, role_id, nom_utilisateur) VALUES (?, ?, ?, ?)",
            [email, hashedPassword, role_id, username]
        );
        return result.insertId;
    }

    /**
     * Met à jour un utilisateur
     * @param {number} id - ID de l'utilisateur
     * @param {string} email - Email de l'utilisateur
     * @param {number} role_id - Rôle de l'utilisateur
     * @param {string} username - Nom d'utilisateur
     * @param {string|null} password - Nouveau mot de passe (optionnel)
     */
    async updateUser(id, email, role_id, username, password = null) {
        const params = [email, role_id, username];
        let query = "UPDATE utilisateurs SET email = ?, role_id = ?, nom_utilisateur = ?";

        if (password) {
            const hashedPassword = await bcrypt.hash(password, 10);
            query += ", mot_de_passe = ?";
            params.push(hashedPassword);
        }

        query += " WHERE id = ?";
        params.push(id);

        await this.conn.execute(query, params);
    }

    /**
     * Supprime un utilisateur par son ID
     * @param {number} id - ID de l'utilisateur
     */
    async deleteUser(id) {
        await this.conn.execute("DELETE FROM utilisateurs WHERE id = ?", [id]);
    }

    /**
     * Met à jour le mot de passe d'un utilisateur
     * @param {number} userId - ID de l'utilisateur
     * @param {string} hashedPassword - Nouveau mot de passe haché
     */
    async updatePassword(userId, hashedPassword) {
        await this.conn.execute("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?", [hashedPassword, userId]);
    }

    /**
     * Récupère les noms d'utilisateur pour une liste d'IDs
     * @param {Array<number>} userIds - Liste des IDs des utilisateurs
     */
    async getUsernames(userIds) {
        if (userIds.length === 0) return [];

        const placeholders = userIds.map(() => "?").join(", ");
        const [rows] = await this.conn.execute(
            `SELECT id, nom_utilisateur FROM utilisateurs WHERE id IN (${placeholders})`,
            userIds
        );

        // Convertir les résultats en un objet clé-valeur
        return rows.reduce((acc, row) => {
            acc[row.id] = row.nom_utilisateur;
            return acc;
        }, {});
    }
}

export default UserOne;
