<?php
public function obtenerEventosPorFecha($eventos) {
    $eventosPorFecha = [];

    foreach ($eventos as $evento) {
        $eventosPorFecha[$evento["fecha"]][] = [
            'asignatura' => $evento["nombre_asignatura"],
            'nombre_evento' => $evento["nombre_evento"],
            'anotaciones' => $evento["anotaciones"],
        ];
    }

    return $eventosPorFecha;
}

public function prepararDatosCalendario($fecha) {
    $fecha = isset($fecha) ? $fecha : date('Y-m');
    list($year, $month) = explode('-', $fecha);

    $calendar = new DateTime("$year-$month-01");
    $prevMonth = $calendar->modify('-1 month')->format('Y-m');
    $nextMonth = $calendar->modify('+2 months')->format('Y-m');

    $primerDia = new DateTime("$year-$month-01");
    $ultimoDia = new DateTime($primerDia->format('Y-m-t'));

    while ($primerDia->format('N') != 1) {
        $primerDia->modify('-1 day');
    }

    while ($ultimoDia->format('N') != 7) {
        $ultimoDia->modify('+1 day');
    }

    return [
        'calendar' => $calendar,
        'primerDia' => $primerDia,
        'ultimoDia' => $ultimoDia,
        'prevMonth' => $prevMonth,
        'nextMonth' => $nextMonth,
    ];
}

public function mostrarCalendario($eventos, $fecha) {
    $eventosPorFecha = $this->obtenerEventosPorFecha($eventos);
    $datosCalendario = $this->prepararDatosCalendario($fecha);

    // Renderiza la plantilla Twig
    echo $twig->render('listadoEventos.php.twig', array_merge($datosCalendario, ['eventosPorFecha' => $eventosPorFecha]));
}