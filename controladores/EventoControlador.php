<?php
require_once "../modelos/EventoModelo.php";
require_once "../modelos/AsignaturaModelo.php";
require_once "../modelos/UsuarioNotificacionModelo.php";
require_once "../modelos/EmailModelo.php";
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
                $idEvento = Evento::crearEvento($nombre, $fecha, $asignatura_id, $usuario_id, $anotaciones);
                        
                // Obtener todos los usuarios de la newsletter
                $usuariosNewsletter = UsuarioNotificacion::getAllUsuariosNotificaciones();

                setlocale(LC_TIME, 'es_ES.UTF-8');

                // Verificar que $usuariosNewsletter es un array antes de iterar
                if (is_array($usuariosNewsletter)) {
                    // Para cada usuario, enviar un correo electrónico
                    foreach ($usuariosNewsletter as $usuario) {
                        if ($usuario !== null && property_exists($usuario, 'correoUsuarioNotificaciones')) {
                            $evento = Evento::getEventoById($idEvento);
                            if ($evento !== null) {
                                $fecha = new DateTime($evento->fecha);
                                $formatter = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
                                $fecha_formateada = $formatter->format($fecha);
                                $asunto = "$evento->nombre el día $fecha_formateada";
                                $cuerpo = "Se ha agregado un nuevo evento al calendario escolar: <br>$evento->nombre - $evento->nombre_asignatura <br>$evento->anotaciones<br><hr>Evento creado por $evento->nombre_usuario";
                        
                                // Verificar si el correo se envió correctamente
                                Email::enviarCorreo($usuario->correoUsuarioNotificaciones, $asunto, $cuerpo);
                            }
                        }
                    }
                }
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

                // Formatea la fecha a 'AAAA-MM'
                $fechaFormateada = date('Y-m', strtotime($fecha));

                // Redirige a la página de listar eventos después de agregar el evento con la fecha como parámetro
                header('Location: ../Evento/listarEventos?fecha=' . $fechaFormateada);
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
        $resultado = Evento::actualizarEvento($id, $nombre, $fecha, $asignatura_id, $usuario_id, $anotaciones);

        // Si el evento se actualizó correctamente, enviar un correo electrónico
        if ($resultado) {
            // Obtener todos los usuarios de la newsletter
            $usuariosNewsletter = UsuarioNotificacion::getAllUsuariosNotificaciones();

            setlocale(LC_TIME, 'es_ES.UTF-8');

            // Verificar que $usuariosNewsletter es un array antes de iterar
            if (is_array($usuariosNewsletter)) {
                // Para cada usuario, enviar un correo electrónico
                foreach ($usuariosNewsletter as $usuario) {
                    if ($usuario !== null && property_exists($usuario, 'correoUsuarioNotificaciones')) {
                        $evento = Evento::getEventoById($id);
                        if ($evento !== null) {
                            $fecha = new DateTime($evento->fecha);
                            $formatter = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
                            $fecha_formateada = $formatter->format($fecha);
                            $asunto = "Modificado $evento->nombre para el día $fecha_formateada";
                            $cuerpo = "Se ha modificado un evento en el calendario escolar: <br>$evento->nombre - $evento->nombre_asignatura <br>$evento->anotaciones<br><hr>Evento modificado por $evento->nombre_usuario";
                    
                            // Verificar si el correo se envió correctamente
                            Email::enviarCorreo($usuario->correoUsuarioNotificaciones, $asunto, $cuerpo);
                        }
                    }
                }
            }
        }

        return $resultado;
    }

    public function showModificarEvento() {
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
                $nombre = !empty($_POST['nombre_personalizado']) ? $_POST['nombre_personalizado'] : $_POST['nombre'];
                $fecha = $_POST['fecha'];
                $asignatura_id = $_POST['asignatura_id'];
                $anotaciones = $_POST['anotaciones'];

                $this->modificarEvento($id, $nombre, $fecha, $asignatura_id, $usuario_id, $anotaciones);

                // Formatea la fecha a 'AAAA-MM'
                $fechaFormateada = date('Y-m', strtotime($fecha));

                // Redirige a la página de listar eventos después de modificar el evento
                header('Location: ../Evento/listarEventos?fecha=' . $fechaFormateada);
                exit();
            } catch (Exception $e) {
                // Redirige a showModificarEvento con un parámetro de error
                header('Location: ../Evento/showModificarEvento?error=1');
                exit();
            }
        } else {
            try {
                // Intenta obtener el ID del evento de la URL
                $id = @$_GET['evento'];

                // Si el ID del evento no está definido, lanza una excepción
                if (!isset($id)) {
                    throw new Exception('ID del evento no definido');
                }

                // Obtener el evento de la base de datos
                $evento = Evento::getEventoById($id);

                // Mostrar la vista del formulario para modificar un evento
                $asignaturaModel = new Asignatura();

                // Obtener todas las asignaturas
                $asignaturas = $asignaturaModel->getAllAsignaturas();

                // Recuperamos el error si lo hay
                $error = isset($_GET["error"]) ? $_GET["error"] : null ;

                // Pasar los datos del evento y las asignaturas a la vista
                echo $this->twig->render("modificarEvento.php.twig", ["loggedin" => $loggedin, "evento" => $evento, "asignaturas" => $asignaturas, "error" => $error]);
            } catch (Exception $e) {
                // Redirige a showModificarEvento con un parámetro de error
                header('Location: ../Evento/listarEventos?error=1');
                exit();
            }
        }
    }

    public function eliminarEvento($id) {
        // Obtener los detalles del evento antes de eliminarlo
        $evento = Evento::getEventoById($id);

        // Eliminar el evento
        $resultado = Evento::eliminarEvento($id);

        // Si el evento se eliminó correctamente, enviar un correo electrónico
        if ($resultado && $evento !== null) {
            // Obtener todos los usuarios de la newsletter
            $usuariosNewsletter = UsuarioNotificacion::getAllUsuariosNotificaciones();

            setlocale(LC_TIME, 'es_ES.UTF-8');

            // Verificar que $usuariosNewsletter es un array antes de iterar
            if (is_array($usuariosNewsletter)) {
                // Para cada usuario, enviar un correo electrónico
                foreach ($usuariosNewsletter as $usuario) {
                    if ($usuario !== null && property_exists($usuario, 'correoUsuarioNotificaciones')) {
                        $fecha = new DateTime($evento->fecha);
                        $formatter = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
                        $fecha_formateada = $formatter->format($fecha);
                        $asunto = "Cancelado $evento->nombre del día $fecha_formateada";
                        $cuerpo = "Se ha eliminado un evento del calendario escolar: <br>$evento->nombre - $evento->nombre_asignatura <br>$evento->anotaciones<br><hr>Evento eliminado por $evento->nombre_usuario";
                
                        // Verificar si el correo se envió correctamente
                        Email::enviarCorreo($usuario->correoUsuarioNotificaciones, $asunto, $cuerpo);
                    }
                }
            }
        }

        return $resultado;
    }

    public function showEliminarEvento() {
        error_reporting(E_ALL & ~E_WARNING);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica si el usuario ya ha iniciado sesión
        $loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;

        try {
            // Intenta obtener el ID del evento de la URL
            $id = @$_GET['evento'];

            // Si el ID del evento no está definido, lanza una excepción
            if (!isset($id)) {
                throw new Exception('ID del evento no definido');
            }

            // Obtener el evento de la base de datos
            $evento = Evento::getEventoById($id);

            // Si el formulario se ha enviado, procesarlo
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Obtener la fecha del evento
                $fecha = substr($evento->fecha, 0, 7);

                $this->eliminarEvento($id);

                // Redirige a la página de listar eventos después de eliminar el evento
                header("Location: ../Evento/listarEventos?fecha=" . $fecha);
                exit();
            } else {
                // Mostrar la vista de confirmación de eliminación
                echo $this->twig->render("eliminarEvento.php.twig", ["loggedin" => $loggedin, "evento" => $evento]);
            }
        } catch (Exception $e) {
            // Redirige a showEliminarEvento con un parámetro de error
            header('Location: ../Evento/listarEventos?error=1');
            exit();
        }
    }

    public function exportarEventos() {
        $eventos = Evento::getAllEventos();
        header('Content-type: text/calendar');
        header('Content-Disposition: attachment; filename="calendario.ics"');
        echo $this->twig->render('exportarEventos.php.twig', ['eventos' => $eventos]);
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
                // En el caso de que fecha sea nula
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

        // Recuperamos el error si lo hay
        $error = isset($_GET["error"]) ? $_GET["error"] : null ;

        // Recuperamos mensaje de newsletter si lo hay
        $newsletter = isset($_GET["newsletter"]) ? $_GET["newsletter"] : null ;

        // Renderiza la plantilla con Twig
        echo $twig->render('listadoEventos.php.twig', [
            'eventosPorFecha' => $eventosPorFecha,
            'dias' => $dias,
            'calendar' => $fechaActual,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
            "loggedin" => $loggedin,
            "error" => $error,
            "newsletter" => $newsletter
        ]);
    }
}