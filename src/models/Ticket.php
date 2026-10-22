<?php

require_once __DIR__ . '/../config/database.php';

class Ticket
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Generar un código único de ticket
    private function generateCode()
    {
        $prefix = "UTIC-" . date('Ym') . "-";
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));
        return $prefix . $random;
    }

    public function create(array $data): string
    {
        $codigoTicket = $this->generateCode();

        // Limpiar y capturar la cédula y la dependencia
        $cedula = trim($data['solicitante_cedula'] ?? '');
        $dependencia_id = !empty($data['dependencia_id']) ? (int)$data['dependencia_id'] : null;

        // Si escribió un texto en "Otro", lo adjuntamos a la descripción
        $descripcionFinal = $data['descripcion'];
        if (!empty($data['dependencia_texto'])) {
            $descripcionFinal = "[Oficina/Área especificada: " . $data['dependencia_texto'] . "]\n\n" . $descripcionFinal;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO tickets (
                codigo, 
                codigo_ticket, 
                solicitante_cedula,
                solicitante_nombre, 
                dependencia_id, 
                categoria_id, 
                descripcion, 
                estado, 
                fecha_creacion
            )
            VALUES (
                :codigo, 
                :codigo_ticket, 
                :cedula,
                :nombre, 
                :dependencia_id, 
                :categoria_id, 
                :descripcion, 
                'Nuevo', 
                NOW()
            )
        ");

        $stmt->execute([
            ':codigo'         => $codigoTicket,
            ':codigo_ticket'  => $codigoTicket,
            ':cedula'         => $cedula,
            ':nombre'         => $data['solicitante_nombre'],
            ':dependencia_id' => $dependencia_id,
            ':categoria_id'   => $data['categoria_id'],
            ':descripcion'    => $descripcionFinal
        ]);

        return $codigoTicket;
    }

    // Buscar ticket por su código de seguimiento público
    public function getByCode($codigo)
    {
        $sql = "SELECT t.*, d.nombre AS dependencia, p.nombre AS piso, c.nombre AS categoria
                FROM tickets t
                LEFT JOIN dependencias d ON t.dependencia_id = d.id
                LEFT JOIN pisos p ON d.piso_id = p.id
                JOIN categorias c ON t.categoria_id = c.id
                WHERE t.codigo_ticket = :codigo
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':codigo' => trim($codigo)]);
        return $stmt->fetch();
    }

    public function getAllAdmin($estado = null)
    {
        $db = Database::getConnection();

        $sql = "SELECT t.*, 
                       COALESCE(p.nombre, 'Otro') as piso, 
                       COALESCE(d.nombre, 'Oficina externa') as dependencia, 
                       c.nombre as categoria, 
                       u.nombre_completo as tecnico_nombre 
            FROM tickets t 
            LEFT JOIN dependencias d ON t.dependencia_id = d.id 
            LEFT JOIN pisos p ON d.piso_id = p.id 
            JOIN categorias c ON t.categoria_id = c.id 
            LEFT JOIN usuarios u ON t.tecnico_id = u.id";

        if (!empty($estado)) {
            $sql .= " WHERE t.estado = :estado";
        }

        $sql .= " ORDER BY t.fecha_creacion DESC";

        $stmt = $db->prepare($sql);
        if (!empty($estado)) {
            $stmt->execute([':estado' => $estado]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener detalle de ticket por ID para el admin
    public function getById($id)
    {
        $sql = "SELECT t.*, d.nombre AS dependencia, p.nombre AS piso, c.nombre AS categoria, u.nombre_completo AS tecnico
                FROM tickets t
                LEFT JOIN dependencias d ON t.dependencia_id = d.id
                LEFT JOIN pisos p ON d.piso_id = p.id
                JOIN categorias c ON t.categoria_id = c.id
                LEFT JOIN usuarios u ON t.tecnico_asignado_id = u.id
                WHERE t.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Actualizar estado u observación técnica
    public function updateStatus($id, $estado, $observacion, $tecnico_id)
    {
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
    public function getStats()
    {
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
    // Obtener tickets filtrados por estado, dependencia y/o cédula para el panel de administración
    public function getFilteredTickets($estado = null, $dependencia_id = null, $cedula = null)
    {
        $db = Database::getConnection();
        $sql = "SELECT t.*, d.nombre as dependencia, p.nombre as piso, 
                       c.nombre as categoria, 
                       u.nombre_completo as tecnico_nombre 
                FROM tickets t 
                LEFT JOIN dependencias d ON t.dependencia_id = d.id 
                LEFT JOIN pisos p ON d.piso_id = p.id 
                JOIN categorias c ON t.categoria_id = c.id 
                LEFT JOIN usuarios u ON t.tecnico_id = u.id 
                WHERE 1=1";

        $params = [];

        if (!empty($estado)) {
            $sql .= " AND t.estado = ?";
            $params[] = $estado;
        }

        if (!empty($dependencia_id)) {
            $sql .= " AND t.dependencia_id = ?";
            $params[] = $dependencia_id;
        }

        if (!empty($cedula)) {
            $sql .= " AND t.solicitante_cedula LIKE ?";
            $params[] = "%{$cedula}%"; // Permite búsqueda parcial o exacta de la cédula
        }

        $sql .= " ORDER BY t.fecha_creacion DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
