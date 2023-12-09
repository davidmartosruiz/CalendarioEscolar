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
    public string $abreviatura_asignatura;
    public ?string $nombre_usuario;

    /**
     * Recupera todos los eventos de la base de datos y los
     * devuelve en formato array.
     * @return array
     */
    public static function getAllEventos(): array {
        $pdo = Conexion::getConnection();
        // Prepara la consulta SQL
        $sql = "SELECT eventos.*, asignaturas.nombre AS nombre_asignatura, asignaturas.abreviatura AS abreviatura_asignatura, usuarios.nombre AS nombre_usuario FROM eventos LEFT JOIN asignaturas ON eventos.asignatura_id = asignaturas.id LEFT JOIN usuarios ON eventos.usuario_id = usuarios.id ORDER BY eventos.fecha ASC";
        $stmt = $pdo->query($sql);
    
        // Ejecuta la consulta y retorna los resultados
        return $stmt->fetchAll(PDO::FETCH_CLASS, "Evento");
    }
    

    
    /**
     * Recupera un evento específico por su ID.
     * @param int $id
     * @return Evento|null
     */
    public static function getEventoById(int $id): ?Evento {
        $stmt = Conexion::getConnection()
                        ->prepare("SELECT eventos.*, asignaturas.nombre AS nombre_asignatura FROM eventos 
                                   INNER JOIN asignaturas ON eventos.asignatura_id = asignaturas.id 
                                   WHERE eventos.id = :id;");
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
