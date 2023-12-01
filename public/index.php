<?php

    require_once '../vendor/autoload.php';

    $loader = new \Twig\Loader\FilesystemLoader('../templates');
 
    $twig = new \Twig\Environment($loader, [
        //'cache' => '../var/cache',
    ]);


    // recopilo información sobre qué método de qué controlador
    // voy a tener que ejecutar.
    $metodo      = $_GET["met"]??"listarEventos" ;
    $controlador = $_GET["con"]??"Evento" ; // libro, resena, usuario, 


    $nombreControlador = "{$controlador}Controlador" ;   // libroController


    //echo "Nombre de la clase: {$nombreControlador}<br/>" ;
    //echo "Nombre del método: {$metodo}<br/>" ;

    // importamos el archivo xxxxController.php
    $controladorPath = "../controladores/{$nombreControlador}.php";
    if (file_exists($controladorPath)) {
        require_once $controladorPath;
    } else {
        die("Se ha producido un error en el controlador.");
    }
    
    // instanciar la clase controladora
    $instanciaControlador = new $nombreControlador ;

    // invocamos el método de la clase que se nos pide
    if (method_exists($instanciaControlador, $metodo)) $instanciaControlador->$metodo() ;
    else 
        die("Se ha producido un error en el controlador.") ;

