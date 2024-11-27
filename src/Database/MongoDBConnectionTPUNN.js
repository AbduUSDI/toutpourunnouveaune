import { MongoClient } from 'mongodb';

class MongoDBConnectionTPUNN {
    constructor() {
        this.uri = 'mongodb+srv://AbduUSDI:heroku123456@abdurahmanusdi.lc9y4uk.mongodb.net';
        this.databaseName = 'tpunn_quizz_score';
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

            this.mongoCollection = this.mongoClient
                .db(this.databaseName)
                .collection('scores'); // Collection par défaut
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
     * Méthode pour récupérer les scores des parents triés
     * @returns {Promise<Array>} Liste des scores triés par ordre décroissant
     */
    async getScoresParents() {
        try {
            if (!this.mongoCollection) {
                throw new Error('Collection MongoDB non définie');
            }

            const cursor = await this.mongoCollection.find(
                {},
                {
                    sort: { score: -1 },
                    limit: 10,
                }
            );

            const documents = await cursor.toArray();
            const scores = documents.map(document => ({
                user_id: document.user_id ?? null,
                score: document.score ?? 0,
                total_score: document.total_score ?? 0,
            }));

            return scores;
        } catch (error) {
            console.error('Erreur lors de la récupération des scores :', error.message);
            return [];
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

export default MongoDBConnectionTPUNN;
