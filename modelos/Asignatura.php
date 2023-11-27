<?php
require_once "database/Conexion.php";

class Asignatura {

    public int $id;
    public string $nombre;
    public string $abreviatura;

    /**
     * Recupera todas las asignaturas de la base de datos y las
     * devuelve en formato array.
     * @return array
     */
    public static function getAllAsignaturas(): array {
        return Conexion::getConnection()
                        ->query("SELECT * FROM asignaturas;")
                        ->fetchAll(PDO::FETCH_CLASS, "Asignatura");
    }

    /**
     * Recupera una asignatura específica por su ID.
     * @param int $id
     * @return Asignatura|null
     */
    public static function getAsignaturaById(int $id): ?Asignatura {
        $stmt = Conexion::getConnection()
                        ->prepare("SELECT * FROM asignaturas WHERE id = :id;");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Asignatura");
        return $stmt->fetch();
    }

    // Puedes agregar más métodos estáticos según sea necesario...
}
?>
