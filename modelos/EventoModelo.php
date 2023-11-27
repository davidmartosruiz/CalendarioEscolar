<?php
require_once "../database/Conexion.php";

class Evento {

    public int $id;
    public string $nombre;
    public string $fecha;
    public int $asignatura_id;
    public ?int $usuario_id;
    public string $anotaciones;
    public string $nombre_asignatura;

    /**
     * Recupera todos los eventos de la base de datos y los
     * devuelve en formato array.
     * @return array
     */
    public static function getAllEventos(): array {
        $conn = Conexion::getConnection();
        // Asumiendo que la columna de fecha en la tabla 'eventos' se llama 'fecha'
        $conn->query("SELECT eventos.*, asignaturas.nombre AS nombre_asignatura FROM eventos LEFT JOIN asignaturas ON eventos.asignatura_id = asignaturas.id ORDER BY eventos.fecha ASC;");
        return $conn->getAll("Evento");
    }

    
    /**
     * Recupera un evento específico por su ID.
     * @param int $id
     * @return Evento|null
     */
    public static function getEventoById(int $id): ?Evento {
        $stmt = Conexion::getConnection()
                        ->prepare("SELECT * FROM eventos WHERE id = :id;");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Evento");
        return $stmt->fetch();
    }

    public static function crearEvento($nombre, $fecha, $asignatura_id, $usuario_id, $anotaciones): bool {
        $stmt = Conexion::getConnection()
                        ->prepare("INSERT INTO eventos (nombre, fecha, asignatura_id, usuario_id, anotaciones) VALUES (:nombre, :fecha, :asignatura_id, :usuario_id, :anotaciones);");
        return $stmt->execute(['nombre' => $nombre, 'fecha' => $fecha, 'asignatura_id' => $asignatura_id, 'usuario_id' => $usuario_id, 'anotaciones' => $anotaciones]);
    }

    public static function actualizarEvento($id, $nombre, $fecha, $asignatura_id, $usuario_id, $anotaciones): bool {
        $stmt = Conexion::getConnection()
                        ->prepare("UPDATE eventos SET nombre = :nombre, fecha = :fecha, asignatura_id = :asignatura_id, usuario_id = :usuario_id, anotaciones = :anotaciones WHERE id = :id;");
        return $stmt->execute(['id' => $id, 'nombre' => $nombre, 'fecha' => $fecha, 'asignatura_id' => $asignatura_id, 'usuario_id' => $usuario_id, 'anotaciones' => $anotaciones]);
    }

    public static function eliminarEvento($id): bool {
        $stmt = Conexion::getConnection()
                        ->prepare("DELETE FROM eventos WHERE id = :id;");
        return $stmt->execute(['id' => $id]);
    }

    
}
?>
