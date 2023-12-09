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
    public static function getAllUsuarios(): array {
        $pdo = Conexion::getConnection();
        $stmt = $pdo->query("SELECT * FROM usuarios");
        return $stmt->fetchAll(PDO::FETCH_CLASS, "Usuario");
    }

    public static function getUsuarioByEmail(string $email): ?Usuario {
        $pdo = Conexion::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
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
}