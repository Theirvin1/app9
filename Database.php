<?php

class Database {
    // Eliminado el tipado nulo de PHP moderno (?PDO)
    private static $instance = null;

    // Eliminada la declaración de tipo de retorno (: PDO)
    public static function getConnection() {
        if (self::$instance === null) {
            $host = getenv('DB_HOST') ? getenv('DB_HOST') : 'db';
            $port = getenv('DB_PORT') ? getenv('DB_PORT') : '5432';
            $db   = getenv('DB_NAME') ? getenv('DB_NAME') : 'app_web_sem9';
            $user = getenv('DB_USER') ? getenv('DB_USER') : 'admin';
            $pass = getenv('DB_PASS') ? getenv('DB_PASS') : 'Admin*2026';

            $dsn = "pgsql:host=" . $host . ";port=" . $port . ";dbname=" . $db . ";";
            try {
                self::$instance = new PDO($dsn, $user, $pass, array(
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ));
            } catch (PDOException $e) {
                http_response_code(500);
                die("Error crítico de infraestructura: Conexión fallida.");
            }
        }
        return self::$instance;
    }
}
