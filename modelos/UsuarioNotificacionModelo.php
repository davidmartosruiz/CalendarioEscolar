<?php
require_once "../database/Conexion.php";

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

    /**
     * Recupera una notificación de usuario específica por su correo electrónico.
     * @param string $correo
     * @return UsuarioNotificacion|null
     */
    public static function getUsuarioNotificacionByEmail(string $correo): ?UsuarioNotificacion {
        $stmt = Conexion::getConnection()
                        ->prepare("SELECT * FROM usuariosNotificaciones WHERE correoUsuarioNotificaciones = :correo;");
        $stmt->execute(['correo' => $correo]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "UsuarioNotificacion");
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    public static function agregarUsuarioNotificacion(string $nombre, string $correo) {
        $pdo = Conexion::getConnection();
        $stmt = $pdo->prepare("INSERT INTO usuariosNotificaciones (nombreUsuarioNotificaciones, correoUsuarioNotificaciones) VALUES (:nombre, :correo)");
        $result = $stmt->execute(['nombre' => $nombre, 'correo' => $correo]);
        return $result;
    }

    public static function actualizarUsuarioNotificacion(int $id, string $nombre, string $correo): bool {
        $pdo = Conexion::getConnection();
        $stmt = $pdo->prepare("UPDATE usuariosNotificaciones SET nombreUsuarioNotificaciones = :nombre, correoUsuarioNotificaciones = :correo WHERE idUsuarioNotificaciones = :id");
        $result = $stmt->execute(['id' => $id, 'nombre' => $nombre, 'correo' => $correo]);
        return $result;
    }

    public static function eliminarUsuarioNotificacion(int $id): bool {
        $pdo = Conexion::getConnection();
        $stmt = $pdo->prepare("DELETE FROM usuariosNotificaciones WHERE idUsuarioNotificaciones = :id");
        $result = $stmt->execute(['id' => $id]);
        return $result;
    }
}
?>
