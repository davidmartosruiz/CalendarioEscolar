<?php

    class Conexion {

        private static ?Conexion $instancia = null ;
        private $db ;   // conexión con el servidor de bases de datos

        private $resultado ;

        private function __clone() { }

        /**
         * Conectamos con el servidor de bases de datos únicamente
         * cuando creo la instancia de la clase Conexión.
         */
        private function __construct() { 

            try {
               // establecer una conexión con el servidor de bases de datos indicando:
                // - La dirección del servidor (db porque estamos trabajando con un servicio de docker)
                // - Usuario
                // - Contraseña
                // - Nombre de la base de datos (opcional)
                // - Puerto (opcional)
                $this->db = new mysqli("db", "root", "", "calendario") ;
        
                // Podemos comprobar si se ha producido un error
                //if ($sqli->connect_errno)
                //	die("** Error de conexión con la base de datos: {$sqli->connect_error}<br/>") ;
        
                // seleccionar la base de datos con la que vamos a trabajar (USE en SQL)
                // $sqli->select_db("series") ;
            } catch(mysqli_sql_exception $exp) {		
                die("** Error de conexión con la base de datos: ".$exp->getMessage()."<br/>") ;
            }
         }

        /**
         * Crea una única instancia de la clase Conexión, la guarda y
         * devuelve.
         * @return Conexion
         */
        public static function getConnection():Conexion {

            if (self::$instancia==null) self::$instancia = new Conexion ;               
            return self::$instancia ;
        }

        /**
         * Realiza una consulta sobre la base de datos y guarda
         * el conjunto de resultados en una propiedad de la clase
         */
        public function query(string $sql) {
          $this->resultado = $this->db->query($sql) ;
          return $this ;
        }

        /**
         * Recupera un registro del conjunto de resultados y lo
         * devuelve en formato de Objeto.
         * @param $clase
         * @return object
         */
        public function getRow(string $clase):?object {
            return $this->resultado->fetch_object($clase) ;
        }

        /**
         * Recupera todos los registros en forma de objeto y
         * los devuelve dentro de un array.
         * @return $clase
         * @return array 
         */
        public function getAll(string $clase):array {

            $datos = [] ;
            while ($item = $this->getRow($clase))
                array_push($datos, $item) ;
            //
            return $datos ;
        }

    }
