<?php
require_once 'src/AuthService.php';
require_once 'src/MateriaController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/login' && $method === 'GET') {
    AuthService::startSecureSession();
    if (isset($_SESSION['user_id'])) {
        header("Location: /materias", true, 302);
        exit();
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head><meta charset="UTF-8"><title>Autenticación Requerida</title></head>
    <body>
    <h2>Micro-Sistema de Gestión de Materias</h2>
    <form action="/login" method="POST">
        Usuario Semilla: <br><input type="text" name="username" required><br><br>
        Contraseña: <br><input type="password" name="password" required><br><br>
        <button type="submit">Autenticarse</button>
    </form>
    </body>
    </html>
    <?php
    exit();
}

if ($uri === '/login' && $method === 'POST') {
    $user = isset($_POST['username']) ? trim($_POST['username']) : '';
    $pass = isset($_POST['password']) ? $_POST['password'] : '';

    if (AuthService::login($user, $pass)) {
        header("Location: /materias", true, 302);
    } else {
        http_response_code(401);
        echo "Credenciales incorrectas. <a href='/login'>Intentar de nuevo</a>";
    }
    exit();
}

if ($uri === '/logout' && $method === 'POST') {
    AuthService::logout();
    exit();
}

$controller = new MateriaController();

if ($uri === '/materias' && $method === 'GET') {
    $controller->index();
} elseif ($uri === '/materias/nueva' && $method === 'GET') {
    $controller->createForm();
} elseif ($uri === '/materias' && $method === 'POST') {
    $controller->store();
} elseif (preg_match('#^/materias/([0-9]+)/editar$#', $uri, $matches) && $method === 'GET') {
    $id = intval($matches[1]);
    $controller->editForm($id);
} elseif (preg_match('#^/materias/([0-9]+)$#', $uri, $matches) && $method === 'POST') {
    $id = intval($matches[1]);
    $controller->update($id);
} elseif (preg_match('#^/materias/([0-9]+)/eliminar$#', $uri, $matches) && $method === 'POST') {
    $id = intval($matches[1]);
    $controller->delete($id);
} else {
    http_response_code(404);
    echo "Recurso no encontrado en el servidor web de la facultad.";
}


