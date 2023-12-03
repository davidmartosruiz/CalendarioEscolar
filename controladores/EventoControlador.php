<?php
require_once "../modelos/EventoModelo.php";
require_once "../modelos/AsignaturaModelo.php";
require_once "../vendor/autoload.php";

class EventoControlador {
    private $evento;
    private $twig;


        public function __construct() {
            $loader = new \Twig\Loader\FilesystemLoader('../templates');
            $this->twig = new \Twig\Environment($loader);
        }



    public function agregarEvento($nombre, $fecha, $asignatura_id, $usuario_id, $anotaciones) {
        if(empty($nombre) || empty($fecha) || empty($asignatura_id) || empty($usuario_id)) {
            throw new Exception('Todos los campos son requeridos');
        }

        try {
            $evento = Evento::crearEvento($nombre, $fecha, $asignatura_id, $usuario_id, $anotaciones);
        } catch (Exception $e) {
            // Manejar la excepción
            echo 'Error: ',  $e->getMessage(), "\n";
        }
    }

    public function showAgregarEvento() {
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
                $nombre = !empty($_POST['nombre_personalizado']) ? $_POST['nombre_personalizado'] : $_POST['nombre'];
                $fecha = $_POST['fecha'];
                $asignatura_id = $_POST['asignatura_id'];
                $anotaciones = $_POST['anotaciones'];

                $this->agregarEvento($nombre, $fecha, $asignatura_id, $usuario_id, $anotaciones);

                // Redirige a la página de listar eventos después de agregar el evento
                header('Location: ../Evento/listarEventos');
                exit();
            } catch (Exception $e) {
                // Aquí puedes manejar la excepción como prefieras
                echo "<div class=\"bg-red-500 text-white py-2 px-4\">Error: ",  $e->getMessage(), "\n</div>";
            }
        }

        // Mostrar la vista del formulario para agregar un evento
        $asignaturaModel = new Asignatura();

        // Obtener todas las asignaturas
        $asignaturas = $asignaturaModel->getAllAsignaturas();

        // Pasar las asignaturas a la vista
        echo $this->twig->render("agregarEvento.php.twig", ["loggedin" => $loggedin, "asignaturas" => $asignaturas]);
    }

    public function modificarEvento($id, $nombre, $fecha, $asignatura_id, $usuario_id, $anotaciones) {
        return Evento::actualizarEvento($id, $nombre, $fecha, $asignatura_id, $usuario_id, $anotaciones);
    }

    public function eliminarEvento($id) {
        return Evento::eliminarEvento($id);
    }


    public function listarEventos() {
        global $twig;
        $twig->addFunction(new \Twig\TwigFunction('nombreMes', function ($fecha) {
            $meses = [
                '01' => 'Enero',
                '02' => 'Febrero',
                '03' => 'Marzo',
                '04' => 'Abril',
                '05' => 'Mayo',
                '06' => 'Junio',
                '07' => 'Julio',
                '08' => 'Agosto',
                '09' => 'Septiembre',
                '10' => 'Octubre',
                '11' => 'Noviembre',
                '12' => 'Diciembre'
            ];
        
            if (empty($fecha)) {
                // Handle the case where $fecha is null or empty
                return 'Fecha no proporcionada';
            }
        
            try {
                $date = new DateTime($fecha);
            } catch (Exception $e) {
                // Handle the case where $fecha is not a valid date string
                return 'Fecha inválida';
            }
        
            $mes = $date->format('m'); // Extrae el mes como una cadena
            return $meses[$mes]; // Usa el mes para obtener el nombre del mes en español
        }));
    
        $eventos = Evento::getAllEventos();
        $eventosPorFecha = [];
    
        foreach ($eventos as $evento) {
            $eventosPorFecha[$evento->fecha][] = $evento;
        }
    
        // Utilizar la fecha de la URL si está disponible, de lo contrario, usar la fecha actual
    $fechaURL = $_GET['fecha'] ?? date('Y-m');
    $fechaActual = new DateTime($fechaURL);

    // Ajustar para obtener el primer y último día del mes seleccionado
    $primerDia = new DateTime($fechaActual->format('Y-m-01'));
    while ($primerDia->format('N') != 1) {
        $primerDia->modify('-1 day');
    }

    $ultimoDia = new DateTime($fechaActual->format('Y-m-t'));
    while ($ultimoDia->format('N') != 7) {
        $ultimoDia->modify('+1 day');
    }

    // Generar arreglo de fechas para el mes seleccionado
    $dias = [];
    for ($dia = clone $primerDia; $dia <= $ultimoDia; $dia->modify('+1 day')) {
        $dias[] = $dia->format('Y-m-d');
    }

    // Fechas para navegación
    $prevMonth = (clone $fechaActual)->modify('-1 month')->format('Y-m');
    $nextMonth = (clone $fechaActual)->modify('+1 month')->format('Y-m');

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    // Verifica si el usuario ya ha iniciado sesión
    $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

    // Renderiza la plantilla con Twig
    echo $twig->render('listadoEventos.php.twig', [
        'eventosPorFecha' => $eventosPorFecha,
        'dias' => $dias,
        'calendar' => $fechaActual,
        'prevMonth' => $prevMonth,
        'nextMonth' => $nextMonth,
        "loggedin" => $loggedin
    ]);
}
}