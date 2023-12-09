<?php
require_once "../database/Conexion.php";

class Asignatura {

    public int $id;
    public string $nombre;
    public string $abreviatura;

    public static function getAllAsignaturas(): array {
        $pdo = Conexion::getConnection();
        $stmt = $pdo->query("SELECT * FROM asignaturas");
        return $stmt->fetchAll(PDO::FETCH_CLASS, "Asignatura");
    }

    public static function getAsignaturaById(int $id): ?Asignatura {
        $pdo = Conexion::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM asignaturas WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Asignatura");

        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    public static function crearAsignatura(string $nombre, string $abreviatura): bool {
        $pdo = Conexion::getConnection();
        $stmt = $pdo->prepare("INSERT INTO asignaturas (nombre, abreviatura) VALUES (:nombre, :abreviatura)");
        $result = $stmt->execute(['nombre' => $nombre, 'abreviatura' => $abreviatura]);
        return $result;
    }

    public static function actualizarAsignatura($id, $nombre, $abreviatura): bool {
        $stmt = Conexion::getConnection()
                        ->prepare("UPDATE asignaturas SET nombre = :nombre, abreviatura = :abreviatura WHERE id = :id;");
        return $stmt->execute(['id' => $id, 'nombre' => $nombre, 'abreviatura' => $abreviatura]);
    }

    public static function eliminarAsignatura($id): bool {
        $stmt = Conexion::getConnection()
                        ->prepare("DELETE FROM asignaturas WHERE id = :id;");
        return $stmt->execute(['id' => $id]);
    }
}
?>