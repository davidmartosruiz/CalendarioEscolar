<?php
require_once "database/Conexion.php";

class Usuario {

    public int $id;
    public string $username;
    public string $password; // Considera almacenar las contraseñas de forma segura
    public string $email;

    /**
     * Recupera todos los usuarios de la base de datos y los
     * devuelve en formato array.
     * @return array
     */
    public static function getAllUsuarios(): array {
        return Conexion::getConnection()
                        ->query("SELECT * FROM usuarios;")
                        ->fetchAll(PDO::FETCH_CLASS, "Usuario");
    }

    /**
     * Recupera un usuario específico por su ID.
     * @param int $id
     * @return Usuario|null
     */
    public static function getUsuarioById(int $id): ?Usuario {
        $stmt = Conexion::getConnection()
                        ->prepare("SELECT * FROM usuarios WHERE id = :id;");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Usuario");
        return $stmt->fetch();
    }

    // Aquí puedes agregar más métodos estáticos según sea necesario...
}
?>
