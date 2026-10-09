<?php

require_once __DIR__ . '/../config/database.php';

class Categoria {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM categorias ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }
}