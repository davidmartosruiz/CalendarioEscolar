<?php

class Conexion {
    private static ?Conexion $instancia = null;
    private $pdo;

    private function __construct() {
        try {
            if (file_exists('../.env')) {
                $env = parse_ini_file('../.env');
                $dsn = "mysql:host={$env['DB_CONNECTION']};dbname={$env['DB_DATABASE']};charset=utf8";
                $this->pdo = new PDO($dsn, $env['DB_USERNAME'], $env['DB_PASSWORD'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } else {
                throw new Exception("No se encontró el archivo .env");
            }
        } catch (Exception $e) {
            die("** Error de conexión con la base de datos: " . $e->getMessage());
        }
    }

    public static function getConnection(): PDO {
        if (self::$instancia === null) {
            self::$instancia = new Conexion();
        }
        return self::$instancia->pdo;
    }
}
