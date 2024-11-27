class ForumTPUNN {
    constructor(dbConnection, mongoClient) {
        this.conn = dbConnection; // Connexion MySQL
        this.mongoClient = mongoClient; // Client MongoDB
        this.table = 'threads'; // Nom de la table MySQL
    }

    /**
     * Ajoute un nouveau thread
     */
    async addThread(title, body, userId) {
        const query = `
            INSERT INTO ${this.table} (title, body, user_id, created_at)
            VALUES (?, ?, ?, NOW())
        `;
        try {
            const [result] = await this.conn.execute(query, [title, body, userId]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de l'ajout du thread :", error.message);
            throw error;
        }
    }

    /**
     * Récupère les threads actifs depuis MongoDB
     */
    async getActiveThreads() {
        try {
            const viewsCollection = this.mongoClient.collection('views');
            const threadsCollection = this.mongoClient.collection('threads');

            const activeThreads = await viewsCollection
                .find({})
                .sort({ views: -1 })
                .limit(5)
                .toArray();

            const activeThreadsArray = [];
            for (const activeThread of activeThreads) {
                const threadId = activeThread.thread_id;
                const thread = await threadsCollection.findOne({ _id: threadId });
                if (thread) {
                    activeThreadsArray.push(thread);
                }
            }

            return activeThreadsArray;
        } catch (error) {
            console.error("Erreur lors de la récupération des threads actifs :", error.message);
            throw error;
        }
    }

    /**
     * Récupère une liste de threads avec une limite
     */
    async getThreads(limit = 10) {
        const query = `
            SELECT t.id, t.title, t.body, t.created_at, u.nom_utilisateur AS author
            FROM ${this.table} t
            JOIN utilisateurs u ON t.user_id = u.id
            ORDER BY t.created_at DESC
            LIMIT ?
        `;
        try {
            const [rows] = await this.conn.execute(query, [limit]);
            return rows;
        } catch (error) {
            console.error("Erreur lors de la récupération des threads :", error.message);
            throw error;
        }
    }

    /**
     * Récupère un thread par son ID
     */
    async getThreadById(id) {
        const query = `
            SELECT t.*, u.nom_utilisateur AS author
            FROM ${this.table} t
            JOIN utilisateurs u ON t.user_id = u.id
            WHERE t.id = ?
        `;
        try {
            const [rows] = await this.conn.execute(query, [id]);
            return rows[0] || null;
        } catch (error) {
            console.error("Erreur lors de la récupération du thread :", error.message);
            throw error;
        }
    }

    /**
     * Met à jour un thread
     */
    async updateThread(id, title, body) {
        const query = `
            UPDATE ${this.table}
            SET title = ?, body = ?
            WHERE id = ?
        `;
        try {
            const [result] = await this.conn.execute(query, [title, body, id]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la mise à jour du thread :", error.message);
            throw error;
        }
    }

    /**
     * Supprime un thread
     */
    async deleteThread(threadId) {
        const query = `DELETE FROM ${this.table} WHERE id = ?`;
        try {
            const [result] = await this.conn.execute(query, [threadId]);
            return result.affectedRows > 0;
        } catch (error) {
            console.error("Erreur lors de la suppression du thread :", error.message);
            throw error;
        }
    }

    /**
     * Récupère les threads créés par un utilisateur
     */
    async getThreadsByUserId(userId) {
        const query = `
            SELECT *
            FROM ${this.table}
            WHERE user_id = ?
            ORDER BY created_at DESC
        `;
        try {
            const [rows] = await this.conn.execute(query, [userId]);
            return rows;
        } catch (error) {
            console.error("Erreur lors de la récupération des threads utilisateur :", error.message);
            throw error;
        }
    }
}

export default ForumTPUNN;
