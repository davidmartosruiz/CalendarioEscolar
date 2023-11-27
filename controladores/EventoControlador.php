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
        $eventos = Evento::getAllEventos();
        require_once "../templates/listadoEventos.php";
    }
    
    
}
?>
