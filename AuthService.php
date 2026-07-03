<?php

require_once 'Database.php';

class AuthService {
    /**
     * Inicializa una sesión segura inyectando cabeceras de defensa OWASP activas
     */
    public static function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(array(
                'cookie_lifetime' => 0,
                'cookie_path'     => '/',
                'cookie_secure'   => false, // Cambiar a true si usas entornos productivos HTTPS
                'cookie_httponly' => true,
                'cookie_samesite' => 'Strict',
            ));
        }

        // Cabeceras exigidas imperativamente por la rúbrica del examen (Top 10 OWASP)
        header("X-Frame-Options: DENY");
        header("X-Content-Type-Options: nosniff");
        header("Content-Security-Policy: default-src 'self'");
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
    }

    /**
     * Valida credenciales contra la BD mapeando con marcadores posicionales (?)
     */
    public static function login($username, $password) {
        $db = Database::getConnection();

        // Uso estricto de marcador de posición posicional (?) para evitar SQLi
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE username = ? LIMIT 1");
        $stmt->execute(array($username));
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            self::startSecureSession();
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['username']   = $user['username'];

            // Generación de Token Anti-CSRF clásico sin bin2hex
            $_SESSION['csrf_token'] = md5(uniqid((string)rand(), true));

            return true;
        }
        return false;
    }


    /**
     * Intercepta la petición y valida la existencia de una sesión de administración activa
     */
    public static function checkAuth() {
        self::startSecureSession();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login", true, 302);
            exit();
        }
    }

    /**
     * Destruye de forma segura la sesión del servidor y limpia las cookies del cliente
     */
    public static function logout() {
        self::startSecureSession();
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
        header("Location: /login", true, 302);
        exit();
    }
}
