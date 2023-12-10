<?php
require_once "../database/Conexion.php";

class Usuario {

    public int $id;
    public string $nombre;
    public string $email;
    public string $password; // Considera almacenar las contraseñas de forma segura


    /**
     * Recupera todos los usuarios de la base de datos y los
     * devuelve en formato array.
     * @return array
     */
    public static function getAllUsuarios($limit, $offset): array {
        $pdo = Conexion::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, "Usuario");
    }

    public static function getTotalUsuarios(): int {
        $pdo = Conexion::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function getUsuarioById(int $id): ?Usuario {
        $pdo = Conexion::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Usuario");

        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    public static function getUsuarioByEmail(string $email): ?Usuario {
        $pdo = Conexion::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Usuario");
    
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    public static function getUsuarioByNombre(string $nombre): ?Usuario {
        $pdo = Conexion::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre = :nombre");
        $stmt->execute(['nombre' => $nombre]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Usuario");

        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    public static function crearUsuario(string $nombre, string $email, string $password): bool {
        $pdo = Conexion::getConnection();
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)");
        $result = $stmt->execute(['nombre' => $nombre, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT)]);
        return $result;
    }

    public static function actualizarUsuario($id, $nombre, $email, $password): bool {
        $stmt = Conexion::getConnection()
                        ->prepare("UPDATE usuarios SET nombre = :nombre, email = :email, password = :password WHERE id = :id;");
        return $stmt->execute(['id' => $id, 'nombre' => $nombre, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT)]);
    }

    public static function eliminarUsuario($id): bool {
        $stmt = Conexion::getConnection()
                        ->prepare("DELETE FROM usuarios WHERE id = :id;");
        return $stmt->execute(['id' => $id]);
    }
}