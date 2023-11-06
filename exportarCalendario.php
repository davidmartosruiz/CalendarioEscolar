<?php
// Conexión a la base de datos (reemplaza con tus propios datos)
$servername = "db";
$username = "root";
$password = "";
$dbname = "calendario";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Consulta para recuperar eventos con asignatura
$query = "SELECT e.nombre as nombreEvento, a.abreviatura as nombreAsignatura, e.anotaciones as descripcion, e.fecha 
          FROM eventos AS e
          INNER JOIN asignaturas AS a ON e.asignatura_id = a.id;";
$result = $conn->query($query);

// Encabezado iCal
$ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//DAW2//calendario.davidmartos.dev//ES
CALSCALE:GREGORIAN
METHOD:PUBLISH
X-WR-CALNAME:Tus Eventos
X-WR-CALDESC:Tus eventos de calendario
";

// Iterar a través de los eventos y generar entradas iCal
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $evento_nombre = $row["nombreEvento"];
        $asignatura_nombre = $row["nombreAsignatura"];
		$descripcion = $row["descripcion"];
        $fecha = date('Ymd', strtotime($row["fecha"]));

        $ical .= "BEGIN:VEVENT
SUMMARY:$evento_nombre - $asignatura_nombre
DESCRIPTION:$descripcion
DTSTART;VALUE=DATE:$fecha
DTEND;VALUE=DATE:$fecha
END:VEVENT
";
    }
}

// Pie de iCal
$ical .= "END:VCALENDAR";

// Configurar encabezados HTTP
header('Content-type: text/calendar');
header('Content-Disposition: inline');
echo $ical;

// Cerrar la conexión a la base de datos
$conn->close();
?>