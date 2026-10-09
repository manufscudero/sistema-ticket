<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/Session.php';
require_once __DIR__ . '/../models/Piso.php';
require_once __DIR__ . '/../models/Dependencia.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../models/Ticket.php';

/** @class Session */
class TicketController {

    private Piso $pisoModel;
    private Dependencia $dependenciaModel;
    private Categoria $categoriaModel;
    private Ticket $ticketModel;

    public function __construct() {
        $this->pisoModel = new Piso();
        $this->dependenciaModel = new Dependencia();
        $this->categoriaModel = new Categoria();
        $this->ticketModel = new Ticket();
    }

    // Muestra el formulario público para crear ticket
    public function create(): void {
        $pisos = $this->pisoModel->getAll();
        $dependencias = $this->dependenciaModel->getAllWithPiso();
        $categorias = $this->categoriaModel->getAll();

        require_once __DIR__ . '/../views/public/create_ticket.php';
    }

    // Guarda el ticket enviado por el usuario
    public function store(): void {
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
    public function success(): void {
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
    public function checkStatus(): void {
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
    public function adminList(): void {
        $estado_filtro = !empty($_GET['estado']) ? $_GET['estado'] : null;
        $tickets = $this->ticketModel->getAllAdmin($estado_filtro);

        require_once __DIR__ . '/../views/admin/tickets_list.php';
    }

    // ADMIN: Ver detalle de ticket
    public function showDetail(): void {
        $id = (int)($_GET['id'] ?? 0);
        $ticket = $this->ticketModel->getById($id);

        if (!$ticket) {
            Session::setFlash('danger', 'El ticket solicitado no existe.');
            header('Location: ' . BASE_URL . '/admin/tickets');
            exit();
        }

        require_once __DIR__ . '/../views/admin/ticket_detail.php';
    }

    // ADMIN: Actualizar estado u observación del ticket
    public function updateStatus(): void {
        $id = (int)($_POST['ticket_id'] ?? 0);
        $estado = $_POST['estado'] ?? 'Nuevo';
        $observacion = trim($_POST['observacion_tecnica'] ?? '');
        $userSession = Session::get('admin_user');
        $tecnico_id = is_array($userSession) ? ($userSession['id'] ?? null) : null;

        if ($id > 0) {
            $this->ticketModel->updateStatus($id, $estado, $observacion, $tecnico_id);
            Session::setFlash('success', 'El ticket fue actualizado correctamente.');
        }

        header('Location: ' . BASE_URL . '/admin/ticket/detalle?id=' . $id);
        exit();
    }
}