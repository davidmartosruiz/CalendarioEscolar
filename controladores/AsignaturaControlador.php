<?php
require_once "Controlador.php" ;
require_once "../modelos/AsignaturaModelo.php" ;

class AsignaturaControlador extends Controlador {

  public function showAdminAsignaturas() {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }

    $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;
    $error = isset($_GET["error"]) ? $_GET["error"] : null ;
    $asignaturas = Asignatura::getAllAsignaturas();
    $loggedin_user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

    echo $this->render("adminAsignaturas.php.twig", ["loggedin" => $loggedin, "asignaturas" => $asignaturas, "error" => $error, "loggedin_user_id" => $loggedin_user_id]) ;
  }

  public function agregarAsignatura($nombre, $abreviatura) {
    if(empty($nombre) || empty($abreviatura)) {
      throw new Exception('Todos los campos son requeridos');
    }

    if(strlen($abreviatura) > 20) { // Ajusta el número según la longitud máxima permitida en tu base de datos
      throw new Exception('La abreviatura es demasiado larga');
    }

    try {
      $asignatura = Asignatura::crearAsignatura($nombre, $abreviatura);
    } catch (Exception $e) {
      echo 'Error: ',  $e->getMessage(), "\n";
    }
  }

  public function showAgregarAsignatura() {
      error_reporting(E_ALL & ~E_WARNING);

      if (session_status() == PHP_SESSION_NONE) {
        session_start();
      }
      $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
          $nombre = $_POST['nombre'];
          $abreviatura = $_POST['abreviatura'];

          $this->agregarAsignatura($nombre, $abreviatura);

          header('Location: ../Asignatura/showAdminAsignaturas');
          exit();
        } catch (Exception $e) {
          echo "<div class=\"bg-red-500 text-white py-2 px-4\">Error: ",  $e->getMessage(), "\n</div>";
        }
      }

      echo $this->render("agregarAsignatura.php.twig", ["loggedin" => $loggedin]);
    }



  public function modificarAsignatura($id, $nombre, $abreviatura) {
    return Asignatura::actualizarAsignatura($id, $nombre, $abreviatura);
  }

  public function showModificarAsignatura() {
      error_reporting(E_ALL & ~E_WARNING);

      if (session_status() == PHP_SESSION_NONE) {
        session_start();
      }

      $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
          $id = $_POST['id'];
          $nombre = $_POST['nombre'];
          $abreviatura = $_POST['abreviatura'];

          $this->modificarAsignatura($id, $nombre, $abreviatura);

          header('Location: ../Asignatura/showAdminAsignaturas');
          exit();
        } catch (Exception $e) {
          header('Location: ../Asignatura/showModificarAsignatura?error=1');
          exit();
        }
      } else {
        try {
          $id = @$_GET['asignatura'];

          if (!isset($id)) {
            throw new Exception('ID de la asignatura no definido');
          }

          $asignatura = Asignatura::getAsignaturaById($id);

          // Verificar si la asignatura existe
          if ($asignatura === null) {
              throw new Exception('Asignatura no encontrada');
          }

          echo $this->render("modificarAsignatura.php.twig", ["loggedin" => $loggedin, "asignatura" => $asignatura]);
        } catch (Exception $e) {
          header('Location: ../Asignatura/showAdminAsignaturas?error=1');
          exit();
        }
      }
  }

  public function eliminarAsignatura($id) {
    return Asignatura::eliminarAsignatura($id);
  }

  public function showEliminarAsignatura() {
    error_reporting(E_ALL & ~E_WARNING);

    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }

    $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

    try {
      $id = @$_GET['asignatura'];

      if (!isset($id)) {
        throw new Exception('ID de la asignatura no definido');
      }

      $asignatura = Asignatura::getAsignaturaById($id);

      // Verificar si la asignatura existe
      if ($asignatura === null) {
          throw new Exception('Asignatura no encontrada');
      }

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $this->eliminarAsignatura($id);

        header("Location: ../Asignatura/showAdminAsignaturas");
        exit();
      } else {
        echo $this->render("eliminarAsignatura.php.twig", ["loggedin" => $loggedin, "asignatura" => $asignatura]);
      }
    } catch (Exception $e) {
      header('Location: ../Asignatura/showAdminAsignaturas?error=1');
      exit();
    }
  }
}