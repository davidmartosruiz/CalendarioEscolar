<?php
require_once "database/Conexion.php";

class UsuarioNotificacion {

    public int $idUsuarioNotificaciones;
    public string $nombreUsuarioNotificaciones;
    public string $correoUsuarioNotificaciones;

    /**
     * Recupera todas las notificaciones de usuarios de la base de datos y las
     * devuelve en formato array.
     * @return array
     */
    public static function getAllUsuariosNotificaciones(): array {
        return Conexion::getConnection()
                        ->query("SELECT * FROM usuariosNotificaciones;")
                        ->fetchAll(PDO::FETCH_CLASS, "UsuarioNotificacion");
    }

    /**
     * Recupera una notificación de usuario específica por su ID.
     * @param int $id
     * @return UsuarioNotificacion|null
     */
    public static function getUsuarioNotificacionById(int $id): ?UsuarioNotificacion {
        $stmt = Conexion::getConnection()
                        ->prepare("SELECT * FROM usuariosNotificaciones WHERE idUsuarioNotificaciones = :id;");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "UsuarioNotificacion");
        return $stmt->fetch();
    }

    // Aquí puedes agregar más métodos estáticos según sea necesario...
}
?>
