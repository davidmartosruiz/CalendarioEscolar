<?php
require_once "Controlador.php" ;
require_once "../modelos/UsuarioModelo.php" ;
require_once "../modelos/TokenModelo.php" ;

class UsuarioControlador extends Controlador {

    /**
     * Muestra el formulario de login
     * @return
     */
    public function showLogin() {
        // Inicia la sesión si no ha sido iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Generamos el token para protección CSRF
    $token = Token::generate();   
        
    // Guardamos el token en la sesión
    $_SESSION["_csrf"] = $token ;
        
    // Recuperamos el error si lo hay
    $error = isset($_GET["error"]) ? $_GET["error"] : null ;
        
    // Cargamos la vista de login y le pasamos el token y los parámetros
    $this->render("iniciarSesion.php.twig", ["token" => $token, "error" => $error]) ;
}
    

    /**
     * Verifica las credenciales del usuario
     * @return
     */
    public function login() {
        ob_start(); // Inicia el almacenamiento en búfer de salida

    // Inicia la sesión si no ha sido iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
        // Recuperamos los datos del formulario
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Verificamos las credenciales del usuario
        $usuario = Usuario::getUsuarioByEmail($email) ;
        if ($usuario && password_verify($password, $usuario->password)) {
            // Iniciamos la sesión
            $_SESSION["loggedin"] = true;
            $_SESSION["email"] = $email;
            $_SESSION["id"] = $usuario->id;
            // Redirigimos a la página de inicio
            header("Location: ../Evento/listarEventos") ;
        } else {
            // Redirigimos a la página de login
            header("Location: showLogin?error=1") ;
        }

        ob_end_flush(); // Envía el contenido del búfer de salida y desactiva el almacenamiento en búfer de salida
    }

    // Función para cerrar sesión
    public function logout() {
        // Inicia la sesión
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Destruye todas las variables de sesión
        $_SESSION = array();

        // Destruye la sesión
        session_destroy();

        // Redirigimos a la página de inicio
        header("Location: ../Evento/listarEventos") ;
    }
    /**
     */
    public function eliminar() {

        $id = $_GET["id"];            
        $usuario = Usuario::getUsuario($id);

        // Verificamos el token
        if (!Token::check($_SESSION["_csrf"], $token)) {
            die("Invalid CSRF token");
        }

        echo "BORRADO!!!!" ;
        $usuario->borrar();
    }
}