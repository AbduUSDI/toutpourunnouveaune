<?php
class AvisMedicaux {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getDerniersAvis($limit = 5) {
        $query = "SELECT * FROM conseils_medicaux ORDER BY date_creation DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}