import { MongoClient } from 'mongodb';

class MongoTPUNNForum {
    constructor() {
        this.uri = 'mongodb+srv://AbduUSDI:heroku123456@abdurahmanusdi.lc9y4uk.mongodb.net';
        this.databaseName = 'tpunn_forum';
        this.mongoClient = null;
        this.mongoCollection = null;
    }

    /**
     * Méthode pour établir une connexion à MongoDB
     */
    async connect() {
        try {
            this.mongoClient = new MongoClient(this.uri, {
                useNewUrlParser: true,
                useUnifiedTopology: true,
            });

            await this.mongoClient.connect();
            console.log('Connexion réussie à MongoDB');

            // Collection par défaut
            this.mongoCollection = this.mongoClient
                .db(this.databaseName)
                .collection('views');
        } catch (error) {
            console.error('Erreur de connexion à MongoDB :', error.message);
            throw new Error('Impossible de se connecter à MongoDB');
        }
    }

    /**
     * Méthode pour obtenir une collection spécifique
     * @param {string} collectionName - Nom de la collection MongoDB
     * @returns {Collection} La collection MongoDB demandée
     */
    getCollection(collectionName) {
        if (!this.mongoClient) {
            throw new Error('MongoDB n’est pas encore connecté');
        }
        return this.mongoClient.db(this.databaseName).collection(collectionName);
    }

    /**
     * Méthode pour supprimer un fil de discussion (thread)
     * @param {string} threadId - L'ID du thread à supprimer
     * @returns {boolean} Indique si la suppression a été reconnue
     */
    async deleteThread(threadId) {
        try {
            if (!this.mongoCollection) {
                throw new Error('Collection MongoDB non définie');
            }

            const result = await this.mongoCollection.deleteOne({ thread_id: String(threadId) });
            return result.acknowledged; // Indique si l'opération a réussi
        } catch (error) {
            console.error('Erreur lors de la suppression du thread :', error.message);
            return false;
        }
    }

    /**
     * Méthode pour fermer la connexion à MongoDB
     */
    async closeConnection() {
        if (this.mongoClient) {
            await this.mongoClient.close();
            console.log('Connexion MongoDB fermée');
        }
    }
}

export default MongoTPUNNForum;
