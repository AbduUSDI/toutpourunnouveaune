<?php
class Tracking {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getTracking() {
        $query = "SELECT * FROM suivi_quotidien ORDER BY date_creation";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}