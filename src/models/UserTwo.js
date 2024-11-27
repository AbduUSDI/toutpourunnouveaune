import bcrypt from 'bcrypt';

class UserTwo {
    constructor(dbConnection) {
        this.conn = dbConnection; // Connexion à la base de données
        this.table = 'utilisateurs'; // Nom de la table
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    async register(username, email, password, role) {
        try {
            const hashedPassword = await bcrypt.hash(password, 10); // Hachage du mot de passe
            const query = `INSERT INTO ${this.table} (nom_utilisateur, email, mot_de_passe, role) VALUES (?, ?, ?, ?)`;
            const [result] = await this.conn.execute(query, [username, email, hashedPassword, role]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error('Erreur lors de l’enregistrement de l’utilisateur :', error.message);
            return false;
        }
    }

    /**
     * Récupère un utilisateur par son ID
     */
    async getUserById(id) {
        const query = `SELECT * FROM ${this.table} WHERE id = ?`;
        const [rows] = await this.conn.execute(query, [id]);
        return rows[0];
    }

    /**
     * Met à jour le profil d’un utilisateur
     */
    async updateUserProfile(userId, username, email, newPassword = null) {
        let query = `UPDATE ${this.table} SET nom_utilisateur = ?, email = ?`;
        const params = [username, email];

        if (newPassword) {
            const hashedPassword = await bcrypt.hash(newPassword, 10);
            query += `, mot_de_passe = ?`;
            params.push(hashedPassword);
        }

        query += ` WHERE id = ?`;
        params.push(userId);

        const [result] = await this.conn.execute(query, params);
        return result.affectedRows > 0;
    }

    /**
     * Envoie une demande d’amitié
     */
    async sendFriendRequest(senderId, receiverId) {
        const query = `INSERT INTO friend_requests (sender_id, receiver_id) VALUES (?, ?)`;
        const [result] = await this.conn.execute(query, [senderId, receiverId]);
        return result.affectedRows > 0;
    }

    /**
     * Répond à une demande d’amitié
     */
    async respondFriendRequest(requestId, status) {
        const query = `UPDATE friend_requests SET status = ? WHERE id = ?`;
        const [result] = await this.conn.execute(query, [status, requestId]);
        return result.affectedRows > 0;
    }

    /**
     * Récupère les demandes d’amitié
     */
    async getFriendRequests(userId) {
        const query = `SELECT * FROM friend_requests WHERE receiver_id = ? AND status = 'pending'`;
        const [rows] = await this.conn.execute(query, [userId]);
        return rows;
    }

    /**
     * Récupère les amis
     */
    async getFriends(userId) {
        const query = `
            SELECT u.id, u.nom_utilisateur, fr.id as request_id
            FROM friend_requests fr
            JOIN utilisateurs u ON (fr.sender_id = u.id OR fr.receiver_id = u.id)
            WHERE (fr.sender_id = ? OR fr.receiver_id = ?)
            AND fr.status = 'accepted'
            AND u.id != ?
        `;
        const [rows] = await this.conn.execute(query, [userId, userId, userId]);
        return rows;
    }

    /**
     * Récupère un utilisateur par son nom d'utilisateur
     */
    async getUserByUsername(username) {
        const query = `SELECT * FROM ${this.table} WHERE nom_utilisateur = ?`;
        const [rows] = await this.conn.execute(query, [username]);
        return rows[0];
    }

    /**
     * Supprime une amitié
     */
    async removeFriend(requestId) {
        const query = `DELETE FROM friend_requests WHERE id = ?`;
        const [result] = await this.conn.execute(query, [requestId]);
        return result.affectedRows > 0;
    }

    /**
     * Définit un token de réinitialisation de mot de passe
     */
    async setResetToken(userId, token) {
        const query = `UPDATE ${this.table} SET reset_token = ? WHERE id = ?`;
        const [result] = await this.conn.execute(query, [token, userId]);
        return result.affectedRows > 0;
    }

    /**
     * Récupère un utilisateur par son token de réinitialisation
     */
    async getUserByResetToken(token) {
        const query = `SELECT * FROM ${this.table} WHERE reset_token = ?`;
        const [rows] = await this.conn.execute(query, [token]);
        return rows[0];
    }

    /**
     * Met à jour le mot de passe d’un utilisateur
     */
    async updatePassword(userId, newPassword) {
        const hashedPassword = await bcrypt.hash(newPassword, 10);
        const query = `UPDATE ${this.table} SET mot_de_passe = ?, reset_token = NULL WHERE id = ?`;
        const [result] = await this.conn.execute(query, [hashedPassword, userId]);
        return result.affectedRows > 0;
    }
}

export default UserTwo;
