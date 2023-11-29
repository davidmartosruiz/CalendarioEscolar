<?php
require_once "../modelos/EventoModelo.php";

class EventoControlador {
    private $evento;

    public function __construct() {
        $this->evento = new Evento();
    }

    public function agregarEvento($nombre, $fecha, $asignatura_id, $usuario_id, $anotaciones) {
        return Evento::crearEvento($nombre, $fecha, $asignatura_id, $usuario_id, $anotaciones);
    }

    public function modificarEvento($id, $nombre, $fecha, $asignatura_id, $usuario_id, $anotaciones) {
        return Evento::actualizarEvento($id, $nombre, $fecha, $asignatura_id, $usuario_id, $anotaciones);
    }

    public function eliminarEvento($id) {
        return Evento::eliminarEvento($id);
    }

    public function listarEventos() {
        global $twig; // Asegúrate de que Twig esté disponible
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
    
        // Fecha actual o seleccionada
        $fechaActual = new DateTime();
    
        // Obtener el primer y último día del mes, y ajustar para cubrir la semana completa
        $primerDia = new DateTime($fechaActual->format('Y-m-01'));
        while ($primerDia->format('N') != 1) {
            $primerDia->modify('-1 day');
        }
    
        $ultimoDia = new DateTime($fechaActual->format('Y-m-t'));
        while ($ultimoDia->format('N') != 7) {
            $ultimoDia->modify('+1 day');
        }
    
        // Generar arreglo de fechas para el mes actual
        $dias = [];
        for ($dia = clone $primerDia; $dia <= $ultimoDia; $dia->modify('+1 day')) {
            $dias[] = $dia->format('Y-m-d');
        }
    
        // Fechas para navegación
        $prevMonth = (clone $fechaActual)->modify('-1 month')->format('Y-m');
        $nextMonth = (clone $fechaActual)->modify('+1 month')->format('Y-m');
    
        // Renderiza la plantilla con Twig
        echo $twig->render('listadoEventos.php.twig', [
            'eventosPorFecha' => $eventosPorFecha,
            'dias' => $dias,
            'calendar' => $fechaActual,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth
        ]);
    }
}