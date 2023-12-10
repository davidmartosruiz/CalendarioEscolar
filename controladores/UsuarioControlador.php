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

    public function showAdmin() {
        // Inicia la sesión si no ha sido iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica si el usuario ya ha iniciado sesión
        $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

        // Recuperamos el error si lo hay
        $error = isset($_GET["error"]) ? $_GET["error"] : null ;

        // Obtenemos todos los usuarios
        $usuarios = Usuario::getAllUsuarios();

        // Obtenemos el ID del usuario logueado
        $loggedin_user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

        // Cargamos la vista de admin y le pasamos los usuarios y los parámetros
        echo $this->render("admin.php.twig", ["loggedin" => $loggedin, "usuarios" => $usuarios, "error" => $error, "loggedin_user_id" => $loggedin_user_id]) ;
    }

    
    public function agregarUsuario($nombre, $email, $password) {
        if(empty($nombre) || empty($email) || empty($password)) {
            throw new Exception('Todos los campos son requeridos');
        }

        // Verifica si el usuario ya existe
        $usuarioExistente = Usuario::getUsuarioByEmail($email);
        if($usuarioExistente) {
            throw new Exception('El usuario ya existe');
        }

        try {
            $usuario = Usuario::crearUsuario($nombre, $email, $password);
        } catch (Exception $e) {
            // Manejar la excepción
            echo 'Error: ',  $e->getMessage(), "\n";
        }
    }

    public function showAgregarUsuario() {
        error_reporting(E_ALL & ~E_WARNING);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Verifica si el usuario ya ha iniciado sesión
        $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

        // Si el formulario se ha enviado, procesarlo
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $nombre = $_POST['nombre'];
                $email = $_POST['email'];
                $password = $_POST['password'];

                $this->agregarUsuario($nombre, $email, $password);

                // Redirige a la página de listar usuarios después de agregar el usuario
                header('Location: ../Usuario/showAdmin');
                exit();
            } catch (Exception $e) {
                // Aquí puedes manejar la excepción como prefieras
                echo "<div class=\"bg-red-500 text-white py-2 px-4\">Error: ",  $e->getMessage(), "\n</div>";
            }
        }

        // Mostrar la vista del formulario para agregar un usuario
        echo $this->render("agregarUsuario.php.twig", ["loggedin" => $loggedin]);
    }

    public function modificarUsuario($id, $nombre, $email, $password) {
        $usuario = Usuario::getUsuarioById($id);
        if ($usuario === null) {
            throw new Exception('Usuario no encontrado');
        }
        return Usuario::actualizarUsuario($id, $nombre, $email, $password);
    }

    public function showModificarUsuario() {
        error_reporting(E_ALL & ~E_WARNING);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;
        $usuario_id = $_SESSION['id'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id = $_POST['id'];
                $nombre = $_POST['nombre'];
                $email = $_POST['email'];
                $password = !empty($_POST['password']) ? $_POST['password'] : null;

                if ($password !== null) {
                    $this->modificarUsuario($id, $nombre, $email, $password);
                } else {
                    $usuario = Usuario::getUsuarioById($id);
                    if ($usuario === null) {
                        throw new Exception('Usuario no encontrado');
                    }
                    $this->modificarUsuario($id, $nombre, $email, $usuario->password);
                }

                header('Location: ../Usuario/showAdmin');
                exit();
            } catch (Exception $e) {
                header('Location: ../Usuario/showModificarUsuario?error=1');
                exit();
            }
        } else {
            try {
                $id = @$_GET['usuario'];
                if (!isset($id)) {
                    throw new Exception('ID del usuario no definido');
                }

                $usuario = Usuario::getUsuarioById($id);
                if ($usuario === null) {
                    throw new Exception('Usuario no encontrado');
                }

                echo $this->render("modificarUsuario.php.twig", ["loggedin" => $loggedin, "usuario" => $usuario]);
            } catch (Exception $e) {
                header('Location: ../Usuario/showAdmin?error=1');
                exit();
            }
        }
    }
    public function eliminarUsuario($id) {
        return Usuario::eliminarUsuario($id);
    }

    public function showEliminarUsuario() {
        error_reporting(E_ALL & ~E_WARNING);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica si el usuario ya ha iniciado sesión
        $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

        try {
            // Intenta obtener el ID del usuario de la URL
            $id = @$_GET['usuario'];

            // Si el ID del usuario no está definido, lanza una excepción
            if (!isset($id)) {
                throw new Exception('ID del usuario no definido');
            }

            // Si el usuario intenta eliminarse a sí mismo, redirige con un error
            if ($id == $_SESSION['id']) {
                header('Location: ../Usuario/showAdmin?error=3');
                exit();
            }

            // Obtener el usuario de la base de datos
            $usuario = Usuario::getUsuarioById($id);

            // Verificar si el usuario existe
            if ($usuario === null) {
                throw new Exception('Usuario no encontrado');
            }

            // Si el formulario se ha enviado, procesarlo
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $this->eliminarUsuario($id);

                // Redirige a la página de listar usuarios después de eliminar el usuario
                header("Location: ../Usuario/showAdmin");
                exit();
            } else {
                // Mostrar la vista de confirmación de eliminación
                echo $this->render("eliminarUsuario.php.twig", ["loggedin" => $loggedin, "usuario" => $usuario]);
            }
        } catch (Exception $e) {
            // Redirige a showEliminarUsuario con un parámetro de error
            header('Location: ../Usuario/showAdmin?error=1');
            exit();
        }
    }
}