<?php
require_once "Controlador.php" ;
require_once "../modelos/UsuarioNotificacionModelo.php" ;
require_once "../modelos/TokenModelo.php" ;
require_once "../vendor/autoload.php";

class UsuarioNotificacionControlador extends Controlador {
  public function showAdminNewsletter() {
    // Inicia la sesión si no ha sido iniciada
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }

    // Verifica si el usuario ya ha iniciado sesión
    $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

    // Recuperamos el error si lo hay
    $error = isset($_GET["error"]) ? $_GET["error"] : null ;

    // Obtenemos todos los usuarios subscritos a notificaciones
    $usuariosNotificaciones = UsuarioNotificacion::getAllUsuariosNotificaciones();

    // Obtenemos el ID del usuario logueado
    $loggedin_user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

    // Cargamos la vista y le pasamos los usuarios y los parámetros
    echo $this->render("adminNewsletter.php.twig", ["loggedin" => $loggedin, "usuariosNotificaciones" => $usuariosNotificaciones, "error" => $error, "loggedin_user_id" => $loggedin_user_id]) ;
  }

  /**
   * Agrega un nuevo usuario a la base de datos.
   *
   * @param string $nombre El nombre del usuario.
   * @param string $email El correo electrónico del usuario.
   * @return void
   */
  public function agregarUsuario(string $nombre, string $email): void {
    // Crear una nueva instancia de UsuarioNotificacion
    $usuario = new UsuarioNotificacion();

    // Establecer el nombre y el correo electrónico del usuario
    $usuario->setNombre($nombre);
    $usuario->setEmail($email);

    // Guardar el usuario en la base de datos
    $usuario->save();
  }


  public function showAgregarUsuario() {
      error_reporting(E_ALL & ~E_WARNING);

      // Inicia la sesión si no ha sido iniciada
      if (session_status() == PHP_SESSION_NONE) {
        session_start();
      }

      // Verifica si el usuario ya ha iniciado sesión
      $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

      // Si el formulario se ha enviado, procesarlo
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
          // Verificar si los campos 'nombre' y 'email' están presentes
          if (!isset($_POST['nombre']) || !isset($_POST['email'])) {
            throw new Exception('Los campos nombre y email son requeridos');
          }

          $nombre = $_POST['nombre'];
          $email = $_POST['email'];

          $this->agregarUsuario($nombre, $email);

          // Redirige a la página de listar usuarios después de agregar el usuario
          header('Location: ../UsuarioNotificacion/showAdminNewsletter');
          exit();
        } catch (Exception $e) {
          // Redirige a showAgregarUsuario con un parámetro de error
          header('Location: ../UsuarioNotificacion/showAdminNewsletter?error=1');
          exit();
        }
      } else {
        // Recuperamos el error si lo hay
        $error = isset($_GET["error"]) ? $_GET["error"] : null ;

        // Mostrar la vista del formulario para agregar un usuario
        echo $this->render("agregarUsuarioNewsletter.php.twig", ["loggedin" => $loggedin, "error" => $error]);
      }
    }

    public function modificarUsuario($id, $nombre, $email) {
      return UsuarioNotificacion::actualizarUsuarioNotificacion($id, $nombre, $email);
    }

    public function showModificarUsuario() {
      error_reporting(E_ALL & ~E_WARNING);

      if (session_status() == PHP_SESSION_NONE) {
        session_start();
      }

      // Verifica si el usuario ya ha iniciado sesión
      $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

      // Usuario ID será el ID del usuario que ha iniciado sesión
      $usuario_id = $_SESSION['id'];

      // Si el formulario se ha enviado, procesarlo
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
          $id = $_POST['id'];
          $nombre = $_POST['nombre'];
          $email = $_POST['email'];

          $this->modificarUsuario($id, $nombre, $email);

          // Redirige a la página de listar usuarios después de modificar el usuario
          header('Location: ../UsuarioNotificacion/showAdminNewsletter');
          exit();
        } catch (Exception $e) {
          // Redirige a showModificarUsuarioNotificacion con un parámetro de error
          header('Location: ../UsuarioNotificacion/showModificarUsuarioNotificacion?error=1');
          exit();
        }
      } else {
        try {
          // Intenta obtener el ID del usuario de la URL
          $id = @$_GET['usuario'];

          // Si el ID del usuario no está definido, lanza una excepción
          if (!isset($id)) {
            throw new Exception('ID del usuario no definido');
          }

          // Obtener el usuario de la base de datos
          $usuario = UsuarioNotificacion::getUsuarioNotificacionById($id);

          // Mostrar la vista del formulario para modificar un usuario
          echo $this->render("modificarUsuarioNotificacion.php.twig", ["loggedin" => $loggedin, "usuario" => $usuario]);
        } catch (Exception $e) {
          // Redirige a showModificarUsuarioNotificacion con un parámetro de error
          header('Location: ../UsuarioNotificacion/showAdminNewsletter?error=1');
          exit();
        }
      }
    }
}