<?php

class AdminController {

    public function __construct() {
        // Proteger todas las rutas administrativas
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['admin_user'])) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
    }

    public function dashboard() {
        $db = Database::getConnection();

        // Obtener estadísticas
        $stats = [
            'total' => $db->query("SELECT COUNT(*) FROM tickets")->fetchColumn(),
            'nuevos' => $db->query("SELECT COUNT(*) FROM tickets WHERE estado = 'Nuevo'")->fetchColumn(),
            'en_proceso' => $db->query("SELECT COUNT(*) FROM tickets WHERE estado = 'En Proceso'")->fetchColumn(),
            'resueltos' => $db->query("SELECT COUNT(*) FROM tickets WHERE estado = 'Resuelto'")->fetchColumn(),
        ];

        // Obtener los últimos 5 tickets haciendo JOIN encadenado (tickets -> dependencias -> pisos)
        $stmt = $db->query("SELECT t.*, p.nombre as piso, d.nombre as dependencia, c.nombre as categoria 
                            FROM tickets t
                            LEFT JOIN dependencias d ON t.dependencia_id = d.id
                            LEFT JOIN pisos p ON d.piso_id = p.id
                            LEFT JOIN categorias c ON t.categoria_id = c.id
                            ORDER BY t.fecha_creacion DESC LIMIT 5");
        $ultimos_tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    public function tickets() {
        $db = Database::getConnection();
        $estado = $_GET['estado'] ?? null;

        $query = "SELECT t.*, p.nombre as piso, d.nombre as dependencia, c.nombre as categoria 
                  FROM tickets t
                  LEFT JOIN dependencias d ON t.dependencia_id = d.id
                  LEFT JOIN pisos p ON d.piso_id = p.id
                  LEFT JOIN categorias c ON t.categoria_id = c.id";

        if ($estado) {
            $query .= " WHERE t.estado = :estado";
        }
        $query .= " ORDER BY t.fecha_creacion DESC";

        $stmt = $db->prepare($query);
        if ($estado) {
            $stmt->bindValue(':estado', $estado);
        }
        $stmt->execute();
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/admin/tickets_list.php';
    }

    public function ticketDetail() {
        $db = Database::getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // --- BLOQUE DE DIAGNÓSTICO TEMPORAL ---
            echo "<pre>";
            print_r($_POST);
            echo "</pre>";
            exit;
            // -------------------------------------

            $ticket_id = (int)($_POST['ticket_id'] ?? 0);
            $nuevo_estado = $_POST['estado'] ?? 'Nuevo';
            $observacion = trim($_POST['observacion_tecnica'] ?? '');

            // Consulta ajustada para evitar errores si no existe la columna fecha_actualizacion
            $stmt = $db->prepare("UPDATE tickets SET estado = :estado, observacion_tecnica = :obs WHERE id = :id");
            $stmt->execute([
                ':estado' => $nuevo_estado,
                ':obs' => $observacion,
                ':id' => $ticket_id
            ]);

            Session::setFlash('success', 'Ticket actualizado correctamente.');
            header('Location: ' . BASE_URL . '/admin/ticket/detalle?id=' . $ticket_id);
            exit;
        }

        $id = (int)($_GET['id'] ?? 0);
        $stmt = $db->prepare("SELECT t.*, p.nombre as piso, d.nombre as dependencia, c.nombre as categoria 
                              FROM tickets t
                              LEFT JOIN dependencias d ON t.dependencia_id = d.id
                              LEFT JOIN pisos p ON d.piso_id = p.id
                              LEFT JOIN categorias c ON t.categoria_id = c.id
                              WHERE t.id = :id");
        $stmt->execute([':id' => $id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        require_once __DIR__ . '/../views/admin/ticket_detail.php';
    }
}