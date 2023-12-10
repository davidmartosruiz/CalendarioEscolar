<?php
require_once "Controlador.php" ;
require_once "../modelos/UsuarioNotificacionModelo.php" ;
require_once "../modelos/TokenModelo.php" ;
require_once "../vendor/autoload.php";

class UsuarioNotificacionControlador extends Controlador {
  public function showNewsletter() {
      error_reporting(E_ALL & ~E_WARNING);

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

          // Redirige a la página de agradecimiento después de agregar el usuario
          header('Location: ../Evento/listarEventos?newsletter=1');
          exit();
        } catch (Exception $e) {
          // Redirige a showNewsletter con un parámetro de error
          header('Location: ../UsuarioNotificacion/showNewsletter?error=1');
          exit();
        }
      } else {
        // Recuperamos el error si lo hay
        $error = isset($_GET["error"]) ? $_GET["error"] : null ;

        // Mostrar la vista del formulario para agregar un usuario
        echo $this->render("newsletter.php.twig", ["error" => $error]);
      }
  }

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

    // Obtenemos el número de página actual
    $page = isset($_GET['page']) ? $_GET['page'] : 1;

    // Obtenemos todos los usuarios subscritos a notificaciones
    $usuariosNotificaciones = UsuarioNotificacion::getAllUsuariosNotificaciones($page);

    // Obtenemos el número total de páginas
    $totalPaginas = UsuarioNotificacion::getTotalPaginas();

    // Obtenemos todos los parámetros de consulta actuales
    $query_params = $_GET;

    // Cargamos la vista y le pasamos los usuarios y los parámetros
    echo $this->render("adminNewsletter.php.twig", ["loggedin" => $loggedin, "usuariosNotificaciones" => $usuariosNotificaciones, "error" => $error, "loggedin_user_id" => $loggedin_user_id, "totalPaginas" => $totalPaginas, "paginaActual" => $page, "query_params" => $query_params]) ;
  }

  /**
   * Agrega un nuevo usuario a la base de datos.
   *
   * @param string $nombre El nombre del usuario.
   * @param string $email El correo electrónico del usuario.
   * @return void
   */
  public function agregarUsuario(string $nombre, string $email): void {
    // Guardar el usuario en la base de datos
    UsuarioNotificacion::agregarUsuarioNotificacion($nombre, $email);
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

      $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
          $id = $_POST['id'];
          $nombre = $_POST['nombre'];
          $email = $_POST['email'];

          $this->modificarUsuario($id, $nombre, $email);

          header('Location: ../UsuarioNotificacion/showAdminNewsletter');
          exit();
        } catch (Exception $e) {
          header('Location: ../UsuarioNotificacion/showModificarUsuarioNotificacion?error=1');
          exit();
        }
      } else {
        try {
          $id = @$_GET['usuario'];

          if (!isset($id)) {
            throw new Exception('ID del usuario no definido');
          }

          $usuario = UsuarioNotificacion::getUsuarioNotificacionById($id);

          if ($usuario === null) {
            throw new Exception('Usuario no encontrado');
          }

          echo $this->render("modificarUsuarioNotificacion.php.twig", ["loggedin" => $loggedin, "usuario" => $usuario]);
        } catch (Exception $e) {
          header('Location: ../UsuarioNotificacion/showAdminNewsletter?error=1');
          exit();
        }
      }
    }

    public function eliminarUsuario($id) {
      return UsuarioNotificacion::eliminarUsuarioNotificacion($id);
    }

    public function showEliminarUsuario() {
      error_reporting(E_ALL & ~E_WARNING);

      if (session_status() == PHP_SESSION_NONE) {
        session_start();
      }

      $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

      try {
        $id = @$_GET['usuario'];

        if (!isset($id)) {
          throw new Exception('ID del usuario no definido');
        }

        $usuario = UsuarioNotificacion::getUsuarioNotificacionById($id);

        // Verificar si el usuario existe
        if ($usuario === null) {
          throw new Exception('Usuario no encontrado');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          $this->eliminarUsuario($id);

          header("Location: ../UsuarioNotificacion/showAdminNewsletter");
          exit();
        } else {
          echo $this->render("eliminarUsuarioNotificacion.php.twig", ["loggedin" => $loggedin, "usuario" => $usuario]);
        }
      } catch (Exception $e) {
        header('Location: ../UsuarioNotificacion/showAdminNewsletter?error=1');
        exit();
      }
    }
}