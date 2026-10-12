<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/Session.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function index(): void {
        $usuarios = $this->userModel->getAll();
        require_once __DIR__ . '/../views/admin/users_list.php';
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $nombre_completo = trim($_POST['nombre_completo'] ?? '');
            $email = trim($_POST['email'] ?? '');

            if (empty($username) || empty($password) || empty($nombre_completo)) {
                Session::setFlash('danger', 'Por favor complete los campos obligatorios.');
                header('Location: ' . BASE_URL . '/admin/usuarios');
                exit();
            }

            try {
                $this->userModel->create([
                    'username' => $username,
                    'password' => $password,
                    'nombre_completo' => $nombre_completo,
                    'email' => $email
                ]);
                Session::setFlash('success', 'Usuario registrado correctamente.');
            } catch (PDOException $e) {
                Session::setFlash('danger', 'Error al registrar: El nombre de usuario o email ya existe.');
            }

            header('Location: ' . BASE_URL . '/admin/usuarios');
            exit();
        }
    }
}