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

        // Generar un código aleatorio de 4 dígitos para el Captcha local
        $captchaCode = rand(1000, 9999);
        Session::set('captcha_code', $captchaCode);

        require_once __DIR__ . '/../views/public/create_ticket.php';
    }

    // Guarda el ticket enviado por el usuario
    public function store(): void
    {
        $cedula = trim($_POST['solicitante_cedula'] ?? '');
        $nombre = trim($_POST['solicitante_nombre'] ?? '');
        $categoria_id = (int)($_POST['categoria_id'] ?? 0);
        $descripcion = trim($_POST['descripcion'] ?? '');

        $dependencia_id = !empty($_POST['dependencia_id']) ? (int)$_POST['dependencia_id'] : null;
        $dependencia_texto = trim($_POST['dependencia_texto'] ?? '');

        // --- VALIDACIÓN DEL CAPTCHA LOCAL ---
        $captchaIngresado = trim($_POST['captcha_input'] ?? '');
        $captchaReal = Session::get('captcha_code');

        // Limpiar el captcha de la sesión inmediatamente para evitar reutilización
        if (isset($_SESSION['captcha_code'])) {
            unset($_SESSION['captcha_code']);
        }

        if (empty($captchaIngresado) || $captchaIngresado != $captchaReal) {
            Session::setFlash('danger', 'El código de verificación ingresado es incorrecto.');
            header('Location: ' . BASE_URL . '/ticket/crear');
            exit();
        }
        // ------------------------------------

        // Validación básica de campos obligatorios (incluimos $cedula)
        if (empty($cedula) || empty($nombre) || (empty($dependencia_id) && empty($dependencia_texto)) || $categoria_id <= 0 || empty($descripcion)) {
            Session::setFlash('danger', 'Por favor complete todos los campos obligatorios del formulario.');
            header('Location: ' . BASE_URL . '/ticket/crear');
            exit();
        }

        $codigo = $this->ticketModel->create([
            'solicitante_cedula' => $cedula,
            'solicitante_nombre' => $nombre,
            'dependencia_id'     => $dependencia_id,
            'dependencia_texto'  => $dependencia_texto,
            'categoria_id'       => $categoria_id,
            'descripcion'        => $descripcion
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

    // ADMIN: Listado de tickets con filtros avanzados (Estado, Oficina/Dependencia, Cédula)
    public function adminList(): void
    {
        $estado_filtro = !empty($_GET['estado']) ? $_GET['estado'] : null;
        $dependencia_id = !empty($_GET['dependencia_id']) ? (int)$_GET['dependencia_id'] : null;
        $cedula = !empty($_GET['cedula']) ? trim($_GET['cedula']) : null;

        // Obtener los tickets aplicando los filtros
        $tickets = $this->ticketModel->getFilteredTickets($estado_filtro, $dependencia_id, $cedula);


        $dependenciasList = $this->dependenciaModel->getAllWithPiso();

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

    public function buscarPorCedula()
    {
        header('Content-Type: application/json');
        $cedula = trim($_GET['cedula'] ?? '');

        if (empty($cedula)) {
            echo json_encode(['encontrado' => false]);
            exit();
        }

        $db = Database::getConnection();

        $stmt = $db->prepare("
            SELECT t.solicitante_nombre, t.dependencia_id, d.piso_id 
            FROM tickets t 
            LEFT JOIN dependencias d ON t.dependencia_id = d.id 
            WHERE t.solicitante_cedula = :cedula 
            ORDER BY t.fecha_creacion DESC 
            LIMIT 1
        ");
        $stmt->execute([':cedula' => $cedula]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            echo json_encode([
                'encontrado' => true,
                'nombre' => $resultado['solicitante_nombre'],
                'dependencia_id' => $resultado['dependencia_id'],
                'piso_id' => $resultado['piso_id']
            ]);
        } else {
            echo json_encode(['encontrado' => false]);
        }
        exit();
    }
    public function getDashboardJson(): void
    {
        header('Content-Type: application/json');

        try {
            $stats = $this->ticketModel->getStats();
            $tickets = $this->ticketModel->getAllAdmin();

            echo json_encode([
                'success' => true,
                'stats' => $stats,
                'tickets' => array_slice($tickets, 0, 5)
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }
}
