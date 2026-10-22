<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar configuración global y helpers
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/helpers/Session.php';
require_once __DIR__ . '/../src/config/database.php';

// Cargar controladores explícitamente para tipado estático e Intelephense
require_once __DIR__ . '/../src/controllers/TicketController.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/AdminController.php';
require_once __DIR__ . '/../src/controllers/UserController.php';

// Autoload para modelos u otras clases complementarias
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../src/controllers/',
        __DIR__ . '/../src/models/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enrutador básico
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalizar la URI eliminando subdirectorios en desarrollo local
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptName !== '/' && strpos($uri, $scriptName) === 0) {
    $uri = substr($uri, strlen($scriptName));
}
$uri = '/' . trim($uri, '/');

// Dispatcher de rutas
switch ($uri) {
    // --- Rutas Públicas ---
    case '/':
    case '/ticket/crear':
    case '/ticket/create':
        $controller = new TicketController();
        $controller->create();
        break;

    case '/ticket/guardar':
    case '/ticket/store':
        $controller = new TicketController();
        $controller->store();
        break;

    case '/seguimiento':
    case '/ticket/status':
    case '/ticket/consultar':
        $controller = new TicketController();
        $controller->checkStatus();
        break;

    case '/ticket/exito':
    case '/ticket/success':
        $controller = new TicketController();
        $controller->success();
        break;

    case '/ticket/buscar-por-cedula':
        $controller = new TicketController();
        $controller->buscarPorCedula();
        break;

    case '/admin/login':
        $controller = new AuthController();
        $controller->login();
        break;

    case '/admin/logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case '/admin/dashboard':
        $controller = new AdminController();
        $controller->dashboard();
        break;

    // --- NUEVA RUTA AJAX PARA EL DASHBOARD EN TIEMPO REAL ---
    case '/admin/dashboard/json':
        $controller = new TicketController();
        $controller->getDashboardJson();
        break;

    case '/admin/tickets':
        $controller = new TicketController();
        $controller->adminList();
        break;

    case '/admin/ticket/detalle':
        $controller = new TicketController();
        $controller->showDetail();
        break;

    case '/admin/ticket/actualizar':
        $controller = new TicketController();
        $controller->updateStatus();
        break;

    // --- Rutas de Usuarios ---
    case '/admin/usuarios':
        $controller = new UserController();
        $controller->index();
        break;

    case '/admin/usuario/guardar':
        $controller = new UserController();
        $controller->store();
        break;

    // --- Fallback (404) siempre al final ---
    default:
        http_response_code(404);
        echo "<h1 style='color:#fff; background:#0b0f17; padding:2rem; font-family:sans-serif;'>404 - Página no encontrada</h1>";
        break;
}