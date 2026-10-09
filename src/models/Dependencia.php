<?php

require_once __DIR__ . '/../config/database.php';

class Dependencia {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAllWithPiso() {
        $sql = "SELECT d.id, d.nombre, d.piso_id, p.nombre AS piso_nombre 
                FROM dependencias d 
                JOIN pisos p ON d.piso_id = p.id 
                ORDER BY p.id ASC, d.nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getByPisoId($piso_id) {
        $stmt = $this->db->prepare("SELECT * FROM dependencias WHERE piso_id = :piso_id ORDER BY nombre ASC");
        $stmt->execute([':piso_id' => $piso_id]);
        return $stmt->fetchAll();
    }
}