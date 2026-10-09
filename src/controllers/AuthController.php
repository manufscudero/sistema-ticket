<?php

class AuthController {

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Buscar usuario en DB
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT * FROM usuarios WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificación apuntando a la columna 'password'
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_user'] = [
                    'id' => $user['id'],
                    'nombre' => $user['nombre_completo'],
                    'username' => $user['username']
                ];
                header('Location: ' . BASE_URL . '/admin/dashboard');
                exit;
            } else {
                Session::setFlash('danger', 'Usuario o contraseña incorrectos.');
            }
        }

        require_once __DIR__ . '/../views/admin/login.php';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['admin_user']);
        session_destroy();
        header('Location: ' . BASE_URL . '/admin/login');
        exit;
    }
}