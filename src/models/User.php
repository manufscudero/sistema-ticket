<?php

require_once __DIR__ . '/../config/database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE username = :username AND activo = 1 LIMIT 1");
        $stmt->execute([':username' => $username]);
        return $stmt->fetch();
    }

    public function verifyCredentials($username, $password) {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // --- AGREGAR ESTOS DOS MÉTODOS NUEVOS ---

    // Obtener todos los usuarios registrados
    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM usuarios ORDER BY creado_en DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Registrar un nuevo usuario/técnico
    public function create(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO usuarios (username, password, nombre_completo, email, activo) VALUES (:username, :password, :nombre_completo, :email, 1)");
        
        return $stmt->execute([
            ':username' => $data['username'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':nombre_completo' => $data['nombre_completo'],
            ':email' => $data['email']
        ]);
    }
}