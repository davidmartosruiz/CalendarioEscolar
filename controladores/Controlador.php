<?php

    // importamos la librería TWIG
    require_once "../vendor/autoload.php" ;

    abstract class Controlador {

        private $twig ;

        public function __construct() {

            // Configuramos la librería Twig indicándole la ruta hasta la
            // carpeta donde tenemos todas las vistas.
            $loader = new \Twig\Loader\FilesystemLoader("../templates") ;
        
            // Instanciamos la librería Twig
            $this->twig = new \Twig\Environment($loader) ;
        }

        /**
         * Renderizamos la plantilla indicada.
         * @param $vista
         * @param $datos
         * @return
         */
        public function render(string $vista, array $datos = []) {
            echo $this->twig->render($vista, $datos) ;
        }

    }