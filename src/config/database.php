<?php

if (!class_exists('Database')) {
    class Database {
        private static $conn = null;

        public static function getConnection() {
            if (self::$conn === null) {
                try {
                    $host = DB_HOST;
                    $port = DB_PORT;
                    $dbname = DB_NAME;
                    $user = 'root';
                    $pass = DB_PASS;

                    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

                    self::$conn = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4" // <--- Agregar esta línea
                    ]);
                } catch (PDOException $e) {
                    die("Error de conexión a la base de datos: " . $e->getMessage());
                }
            }
            return self::$conn;
        }
    }
}