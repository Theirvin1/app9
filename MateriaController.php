<?php
require_once 'MateriaRepository.php';
require_once 'AuthService.php';

class MateriaController {
    private $repo;

    public function __construct() {
        AuthService::checkAuth();
        $this->repo = new MateriaRepository();
    }

    public static function escape($data) {
        return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
    }

    private function validateCSRF() {
        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            die("Error de validación: Token CSRF no válido o ausente.");
        }
    }

    public function index() {
        $materias = $this->repo->getAllActive();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"><title>Materias</title></head>
        <body>
        <h1>Gestión de Materias - Ingeniería de Software</h1>
        <p>Usuario: <strong><?php echo self::escape($_SESSION['username']); ?></strong></p>
        <form action="/logout" method="POST" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit">Cerrar Sesión</button>
        </form>
        <hr>
        <a href="/materias/nueva"><button>+ Registrar Nueva Materia</button></a>
        <br><br>
        <table border="1" cellpadding="8" style="width:100%; border-collapse: collapse;">
            <tr style="background-color: #f2f2f2;">
                <th>Código</th><th>Nombre</th><th>Créditos</th><th>Semestre</th><th>Acciones</th>
            </tr>
            <?php foreach ($materias as $m): ?>
                <tr>
                    <td><?php echo self::escape($m['codigo']); ?></td>
                    <td><?php echo self::escape($m['nombre']); ?></td>
                    <td><?php echo self::escape($m['creditos']); ?></td>
                    <td><?php echo self::escape($m['semestre']); ?></td>
                    <td>
                        <a href="/materias/<?php echo $m['id']; ?>/editar">Editar</a> |
                        <form action="/materias/<?php echo $m['id']; ?>/eliminar" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que desea aplicar eliminación lógica?');">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <button type="submit" style="color:red;">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        </body>
        </html>
        <?php
    }

    public function createForm($error = null) {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"><title>Nueva Materia</title></head>
        <body>
        <h1>Registrar Nueva Materia</h1>
        <?php if ($error): ?><p style="color:red;"><?php echo self::escape($error); ?></p><?php endif; ?>
        <form action="/materias" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            Código (Exacto 6 caracteres, ej: APW501): <br>
            <input type="text" name="codigo" required minlength="6" maxlength="6" pattern="[A-Z0-9]{6}"><br><br>
            Nombre (5 a 80 caracteres): <br>
            <input type="text" name="nombre" required minlength="5" maxlength="80"><br><br>
            Créditos (Rango 1 - 6): <br>
            <input type="number" name="creditos" required min="1" max="6"><br><br>
            Semestre (Rango 1 - 10): <br>
            <input type="number" name="semestre" required min="1" max="10"><br><br>
            <button type="submit">Guardar Registro</button>
            <a href="/materias">Cancelar</a>
        </form>
        </body>
        </html>
        <?php
    }

    public function store() {
        $this->validateCSRF();
        $codigo = isset($_POST['codigo']) ? strtoupper(trim($_POST['codigo'])) : '';
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $creditos = filter_input(INPUT_POST, 'creditos', FILTER_VALIDATE_INT);
        $semestre = filter_input(INPUT_POST, 'semestre', FILTER_VALIDATE_INT);

        if (strlen($codigo) !== 6 || strlen($nombre) < 5 || strlen($nombre) > 80 ||
            $creditos === false || $creditos === null || $creditos < 1 || $creditos > 6 ||
            $semestre === false || $semestre === null || $semestre < 1 || $semestre > 10) {
            http_response_code(422);
            die("Error 422: Datos de entrada fuera de rango.");
        }

        if ($this->repo->isCodigoDuplicado($codigo)) {
            http_response_code(400);
            $this->createForm("Error: El código de la materia ya se encuentra registrado.");
            exit();
        }

        $this->repo->create($codigo, $nombre, intval($creditos), intval($semestre));
        header("Location: /materias", true, 303);
    }

    public function editForm($id, $error = null) {
        $m = $this->repo->getById($id);
        if (!$m) { http_response_code(404); die("Error 404: Materia no encontrada."); }
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"><title>Editar Materia</title></head>
        <body>
        <h1>Modificar Materia</h1>
        <?php if ($error): ?><p style="color:red;"><?php echo self::escape($error); ?></p><?php endif; ?>
        <form action="/materias/<?php echo $m['id']; ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            Código: <input type="text" value="<?php echo self::escape($m['codigo']); ?>" readonly><br><br>
            Nombre (5 a 80 caracteres): <br>
            <input type="text" name="nombre" value="<?php echo self::escape($m['nombre']); ?>" required minlength="5" maxlength="80"><br><br>
            Créditos (1 - 6): <br>
            <input type="number" name="creditos" value="<?php echo self::escape($m['creditos']); ?>" required min="1" max="6"><br><br>
            Semestre (1 - 10): <br>
            <input type="number" name="semestre" value="<?php echo self::escape($m['semestre']); ?>" required min="1" max="10"><br><br>
            <button type="submit">Actualizar Registro</button>
            <a href="/materias">Cancelar</a>
        </form>
        </body>
        </html>
        <?php
    }

    public function update($id) {
        $this->validateCSRF();
        $m = $this->repo->getById($id);
        if (!$m) { http_response_code(404); die("Materia no encontrada."); }

        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $creditos = filter_input(INPUT_POST, 'creditos', FILTER_VALIDATE_INT);
        $semestre = filter_input(INPUT_POST, 'semestre', FILTER_VALIDATE_INT);

        if (strlen($nombre) < 5 || strlen($nombre) > 80 ||
            $creditos === false || $creditos === null || $creditos < 1 || $creditos > 6 ||
            $semestre === false || $semestre === null || $semestre < 1 || $semestre > 10) {
            http_response_code(422);
            die("Error 422: Datos de actualización inválidos.");
        }

        $this->repo->update($id, $nombre, intval($creditos), intval($semestre));
        header("Location: /materias", true, 303);
    }

    public function delete($id) {
        $this->validateCSRF();
        $this->repo->softDelete($id);
        header("Location: /materias", true, 303);
    }
}
