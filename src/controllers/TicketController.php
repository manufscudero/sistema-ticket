<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/Session.php';
require_once __DIR__ . '/../models/Piso.php';
require_once __DIR__ . '/../models/Dependencia.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../models/Ticket.php';

/** @class Session */
class TicketController
{

    private Piso $pisoModel;
    private Dependencia $dependenciaModel;
    private Categoria $categoriaModel;
    private Ticket $ticketModel;

    public function __construct()
    {
        $this->pisoModel = new Piso();
        $this->dependenciaModel = new Dependencia();
        $this->categoriaModel = new Categoria();
        $this->ticketModel = new Ticket();
    }

    // Muestra el formulario público para crear ticket
    public function create(): void
    {
        $pisos = $this->pisoModel->getAll();
        $dependencias = $this->dependenciaModel->getAllWithPiso();
        $categorias = $this->categoriaModel->getAll();

        require_once __DIR__ . '/../views/public/create_ticket.php';
    }

    // Guarda el ticket enviado por el usuario
    public function store(): void
    {
        $nombre = trim($_POST['solicitante_nombre'] ?? '');
        $dependencia_id = (int)($_POST['dependencia_id'] ?? 0);
        $categoria_id = (int)($_POST['categoria_id'] ?? 0);
        $gravedad = $_POST['gravedad'] ?? 'Media';
        $descripcion = trim($_POST['descripcion'] ?? '');

        // Validación básica
        if (empty($nombre) || $dependencia_id <= 0 || $categoria_id <= 0 || empty($descripcion)) {
            Session::setFlash('danger', 'Por favor complete todos los campos obligatorios del formulario.');
            header('Location: ' . BASE_URL . '/ticket/crear');
            exit();
        }

        $codigo = $this->ticketModel->create([
            'solicitante_nombre' => $nombre,
            'dependencia_id' => $dependencia_id,
            'categoria_id' => $categoria_id,
            'gravedad' => $gravedad,
            'descripcion' => $descripcion
        ]);

        Session::set('ticket_exito_codigo', $codigo);
        header('Location: ' . BASE_URL . '/ticket/exito');
        exit();
    }

    // Página de confirmación tras crear el ticket
    public function success(): void
    {
        $codigo = Session::get('ticket_exito_codigo');
        if (!$codigo) {
            header('Location: ' . BASE_URL . '/ticket/crear');
            exit();
        }

        // Limpiar el código de la sesión una vez leído
        if (isset($_SESSION['ticket_exito_codigo'])) {
            unset($_SESSION['ticket_exito_codigo']);
        }

        require_once __DIR__ . '/../views/public/success.php';
    }

    // Consulta de estado pública mediante código de ticket
    public function checkStatus(): void
    {
        $ticket = null;
        $codigoBuscado = trim($_GET['codigo'] ?? '');

        if (!empty($codigoBuscado)) {
            $ticket = $this->ticketModel->getByCode($codigoBuscado);
            if (!$ticket) {
                Session::setFlash('warning', 'No se encontró ningún ticket con el código ingresado.');
            }
        }

        require_once __DIR__ . '/../views/public/check_status.php';
    }

    // ADMIN: Listado de tickets
    public function adminList(): void
    {
        $estado_filtro = !empty($_GET['estado']) ? $_GET['estado'] : null;
        $tickets = $this->ticketModel->getAllAdmin($estado_filtro);

        require_once __DIR__ . '/../views/admin/tickets_list.php';
    }

    // ADMIN: Ver detalle de ticket
    public function showDetail(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $ticket = $this->ticketModel->getById($id);

        if (!$ticket) {
            Session::setFlash('danger', 'El ticket solicitado no existe.');
            header('Location: ' . BASE_URL . '/admin/tickets');
            exit();
        }
        // Obtener la lista de usuarios/técnicos del sistema para el selector
        $db = Database::getConnection();
        $tecnicos = $db->query("SELECT id, nombre_completo AS nombre FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/admin/ticket_detail.php';
    }

    // ADMIN: Actualizar el estado, observación técnica y técnico asignado
    public function updateStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticket_id = (int)($_POST['ticket_id'] ?? 0);
            $nuevo_estado = $_POST['estado'] ?? 'Nuevo';
            $observacion = trim($_POST['observacion_tecnica'] ?? '');
            $tecnico_id = !empty($_POST['tecnico_id']) ? (int)$_POST['tecnico_id'] : null;

            if ($ticket_id > 0) {
                $db = Database::getConnection();
                $stmt = $db->prepare("UPDATE tickets SET estado = :estado, observacion_tecnica = :obs, tecnico_id = :tecnico_id WHERE id = :id");
                $stmt->execute([
                    ':estado' => $nuevo_estado,
                    ':obs' => $observacion,
                    ':tecnico_id' => $tecnico_id,
                    ':id' => $ticket_id
                ]);

                Session::setFlash('success', 'Ticket actualizado correctamente.');
            } else {
                Session::setFlash('danger', 'ID de ticket inválido.');
            }

            header('Location: ' . BASE_URL . '/admin/ticket/detalle?id=' . $ticket_id);
            exit();
        }
    }
}
