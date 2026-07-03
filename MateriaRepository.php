<?php
require_once 'Database.php';

class MateriaRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAllActive() {
        $stmt = $this->db->query("SELECT * FROM materias WHERE activa = TRUE ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM materias WHERE id = ? AND activa = TRUE LIMIT 1");
        $stmt->execute(array($id));
        $res = $stmt->fetch();
        return $res ? $res : null;
    }

    public function isCodigoDuplicado($codigo, $excludeId = null) {
        if ($excludeId !== null) {
            $sql = "SELECT COUNT(*) FROM materias WHERE codigo = ? AND id <> ?";
            $params = array($codigo, $excludeId);
        } else {
            $sql = "SELECT COUNT(*) FROM materias WHERE codigo = ?";
            $params = array($codigo);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return intval($stmt->fetchColumn()) > 0;
    }

    public function create($codigo, $nombre, $creditos, $semestre) {
        $stmt = $this->db->prepare("INSERT INTO materias (codigo, nombre, creditos, semestre) VALUES (?, ?, ?, ?)");
        return $stmt->execute(array(
            $codigo,
            $nombre,
            $creditos,
            $semestre
        ));
    }

    public function update($id, $nombre, $creditos, $semestre) {
        $stmt = $this->db->prepare("UPDATE materias SET nombre = ?, creditos = ?, semestre = ? WHERE id = ?");
        return $stmt->execute(array(
            $nombre,
            $creditos,
            $semestre,
            $id
        ));
    }

    public function softDelete($id) {
        $stmt = $this->db->prepare("UPDATE materias SET activa = FALSE WHERE id = ?");
        return $stmt->execute(array($id));
    }
}
