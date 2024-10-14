<?php
namespace Database;
/**
 * Classe pour établir une connexion à la base de données avec PDO
 */

use PDO;
use Exception;
use PDOException;

class DatabaseTPUNN {
    private $hostname = 'jaoftv.stackhero-network.com';
    private $port = '5095';
    private $user = 'Abdurahman';
    private $password = 'Abdufufu2525+';
    private $database = 'toupourunnouveaune'; 
    private $connexion;

    public function connect() {
        $this->connexion = null;

        try {
            $this->connexion = new PDO(
                "mysql:host=" . $this->hostname . ";port=" . $this->port . ";dbname=" . $this->database,
                $this->user,
                $this->password,
                array(
                    PDO::MYSQL_ATTR_SSL_CA => '/app/ssl/isrgrootx1.pem',
                    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true
                )
            );

            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $erreur) {

            error_log("Erreur de connexion à la base de données : " . $erreur->getMessage());

            throw new Exception("Impossible de se connecter à la base de données");
        }

        return $this->connexion;
    }
}