import mysql from 'mysql2/promise';

class DatabaseTPUNN {
    constructor() {
        this.hostname = 'jaoftv.stackhero-network.com';
        this.port = '5095';
        this.user = 'Abdurahman';
        this.password = 'Abdufufu2525+';
        this.database = 'toupourunnouveaune';
        this.sslCA = '/app/ssl/isrgrootx1.pem'; // Chemin vers le certificat SSL
        this.connection = null;
    }

    /**
     * Méthode pour établir une connexion à la base de données
     */
    async connect() {
        try {
            this.connection = await mysql.createConnection({
                host: this.hostname,
                port: this.port,
                user: this.user,
                password: this.password,
                database: this.database,
                ssl: {
                    ca: this.sslCA
                }
            });

            console.log('Connexion réussie à la base de données');
            return this.connection;
        } catch (error) {
            console.error('Erreur de connexion à la base de données:', error.message);
            throw new Error('Impossible de se connecter à la base de données');
        }
    }
}
