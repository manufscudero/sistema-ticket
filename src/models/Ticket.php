<?php

require_once __DIR__ . '/../config/database.php';

class Ticket {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Generar un código único de ticket, ej: UTIC-202610-A8F2
    private function generateCode() {
        $prefix = "UTIC-" . date('Ym') . "-";
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));
        return $prefix . $random;
    }

    // Insertar un nuevo ticket desde el portal público
    public function create($data) {
        $codigo = $this->generateCode();
        $sql = "INSERT INTO tickets 
                (codigo_ticket, solicitante_nombre, dependencia_id, categoria_id, gravedad, descripcion, estado) 
                VALUES (:codigo, :nombre, :dependencia_id, :categoria_id, :gravedad, :descripcion, 'Nuevo')";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':codigo' => $codigo,
            ':nombre' => $data['solicitante_nombre'],
            ':dependencia_id' => $data['dependencia_id'],
            ':categoria_id' => $data['categoria_id'],
            ':gravedad' => $data['gravedad'],
            ':descripcion' => $data['descripcion']
        ]);

        return $codigo;
    }

    // Buscar ticket por su código de seguimiento público
    public function getByCode($codigo) {
        $sql = "SELECT t.*, d.nombre AS dependencia, p.nombre AS piso, c.nombre AS categoria
                FROM tickets t
                JOIN dependencias d ON t.dependencia_id = d.id
                JOIN pisos p ON d.piso_id = p.id
                JOIN categorias c ON t.categoria_id = c.id
                WHERE t.codigo_ticket = :codigo
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':codigo' => trim($codigo)]);
        return $stmt->fetch();
    }

    // Listar tickets para el panel admin con filtros opcionales (Estado, Categoría)
    public function getAllAdmin($filtro_estado = null) {
        $sql = "SELECT t.*, d.nombre AS dependencia, p.nombre AS piso, c.nombre AS categoria, u.nombre_completo AS tecnico
                FROM tickets t
                JOIN dependencias d ON t.dependencia_id = d.id
                JOIN pisos p ON d.piso_id = p.id
                JOIN categorias c ON t.categoria_id = c.id
                LEFT JOIN usuarios u ON t.tecnico_asignado_id = u.id";

        if ($filtro_estado) {
            $sql .= " WHERE t.estado = :estado";
        }

        $sql .= " ORDER BY t.fecha_creacion DESC";

        $stmt = $this->db->prepare($sql);
        if ($filtro_estado) {
            $stmt->execute([':estado' => $filtro_estado]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll();
    }

    // Obtener detalle de ticket por ID para el admin
    public function getById($id) {
        $sql = "SELECT t.*, d.nombre AS dependencia, p.nombre AS piso, c.nombre AS categoria, u.nombre_completo AS tecnico
                FROM tickets t
                JOIN dependencias d ON t.dependencia_id = d.id
                JOIN pisos p ON d.piso_id = p.id
                JOIN categorias c ON t.categoria_id = c.id
                LEFT JOIN usuarios u ON t.tecnico_asignado_id = u.id
                WHERE t.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Actualizar estado u observación técnica
    public function updateStatus($id, $estado, $observacion, $tecnico_id) {
        $fecha_resolucion = ($estado === 'Resuelto' || $estado === 'Cerrado') ? date('Y-m-d H:i:s') : null;

        $sql = "UPDATE tickets 
                SET estado = :estado, 
                    observacion_tecnica = :observacion, 
                    tecnico_asignado_id = :tecnico_id,
                    fecha_resolucion = COALESCE(:fecha_resolucion, fecha_resolucion)
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado' => $estado,
            ':observacion' => $observacion,
            ':tecnico_id' => $tecnico_id,
            ':fecha_resolucion' => $fecha_resolucion,
            ':id' => $id
        ]);
    }

    // Contadores para las tarjetas del Dashboard Admin
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) AS total,
                    SUM(CASE WHEN estado = 'Nuevo' THEN 1 ELSE 0 END) AS nuevos,
                    SUM(CASE WHEN estado = 'En Proceso' THEN 1 ELSE 0 END) AS en_proceso,
                    SUM(CASE WHEN estado = 'Resuelto' THEN 1 ELSE 0 END) AS resueltos,
                    SUM(CASE WHEN estado = 'Cerrado' THEN 1 ELSE 0 END) AS cerrados
                FROM tickets";
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }
}