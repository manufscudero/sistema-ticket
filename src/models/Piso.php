<?php

require_once __DIR__ . '/../config/database.php';

class Piso {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM pisos ORDER BY id ASC");
        return $stmt->fetchAll();
    }
}