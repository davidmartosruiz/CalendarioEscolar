<?php
// Conexión a la base de datos
$servername = "db";
$username = "root";
$password = "";
$dbname = "calendario";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die("Error de conexión: " . $conn->connect_error);
}

// Consulta para obtener eventos, asignaturas y anotaciones
$sql = "SELECT eventos.id, eventos.nombre as nombre_evento, eventos.fecha, eventos.anotaciones, asignaturas.nombre as nombre_asignatura FROM eventos INNER JOIN asignaturas ON eventos.asignatura_id = asignaturas.id ORDER BY eventos.fecha ASC";
$result = $conn->query($sql);

$eventos = [];
while ($row = $result->fetch_assoc()) {
	$eventos[$row["fecha"]][] = [
		'asignatura' => $row["nombre_asignatura"],
		'nombre_evento' => $row["nombre_evento"],
		'anotaciones' => $row["anotaciones"],
	];
}

// Función para mostrar los meses en español
function nombreMes($mes) {
	$meses = [
		'January' => 'Enero',
		'February' => 'Febrero',
		'March' => 'Marzo',
		'April' => 'Abril',
		'May' => 'Mayo',
		'June' => 'Junio',
		'July' => 'Julio',
		'August' => 'Agosto',
		'September' => 'Septiembre',
		'October' => 'Octubre',
		'November' => 'Noviembre',
		'December' => 'Diciembre',
	];

	return $meses[$mes] ?? $mes;
}

// Establecer la fecha actual o la fecha especificada en la URL (formato: yyyy-mm)
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m');
list($year, $month) = explode('-', $fecha);

$calendar = new DateTime("$year-$month-01");
$prevMonth = $calendar->modify('-1 month')->format('Y-m');
$nextMonth = $calendar->modify('+2 months')->format('Y-m');

// Obtener el primer día del mes y el último día del mes
$primerDia = new DateTime("$year-$month-01");
$ultimoDia = new DateTime($primerDia->format('Y-m-t'));

// Ajustar el primer día para que comience en lunes
while ($primerDia->format('N') != 1) {
	$primerDia->modify('-1 day');
}

// Ajustar el último día para que termine en domingo
while ($ultimoDia->format('N') != 7) {
	$ultimoDia->modify('+1 day');
}
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>Calendario</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<style>
			.cuerpo, table {
				border-collapse: collapse;
				margin-left: auto;
				margin-right: auto;
				width: 95%;
			}

			table {
				border-collapse: collapse;
				margin-left: auto;
				margin-right: auto;
				width: 100%;
				table-layout: fixed;
				margin-top: 1.5%;
			}
			th, td {
				border: 1px solid black;
				padding: 8px;
				text-align: center;
				vertical-align: top;
				width: 14.28%;
			}
			th {
				background-color: #f2f2f2;
			}
			.other-month {
				background-color: #e0e0e0;
			}
			.asignatura {
				font-weight: bold;
			}
			.evento {
				margin-top: 4px;
			}
			#tituloPagina {
				background-color: #333 !important;
				color: #f2f2f2 !important;
			}
			.tituloDerecha {
				float: right !important;
			}
			#nombreMes {
				float: left;
			}
			.nav-button {
				background-color: #333;
				color: #f2f2f2;
				padding: 8px 16px;
				text-decoration: none;
				border: none;
				border-radius: 5px;
				margin: 5px;
				float: right;
				margin-top: 1.2%;
			}

			.nav-button:hover {
				background-color: #ddd;
				color: black;
			}
		</style>
		<link rel="stylesheet" href="https://davidmartos.dev/base/styles.css" type="text/css">
	</head>
	<body>
		<div class="cabecera">
			<a id="tituloPagina">Calendario</a>
			<a class="tituloDerecha" href="login.php">Iniciar sesión</a>
			<a class="tituloDerecha" href="exportarCalendario.php">Exportar calendario</a>
			<a class="tituloDerecha" href="/vistaMensual.php">Vista mensual</a>
			<a class="tituloDerecha" href="/">Vista listado</a>
		</div>
		<div class="cuerpo">
			<h2 id="nombreMes"><?php echo nombreMes($calendar->modify('-1 month')->format('F')) . ' ' . $calendar->format('Y'); ?></h2>

			<a href="?fecha=<?php echo $nextMonth; ?>" class="nav-button">Mes siguiente &gt;</a>
			<a href="/vistaMensual.php" class="nav-button">Mes actual</a>
			<a href="?fecha=<?php echo $prevMonth; ?>" class="nav-button">&lt; Mes anterior</a>


			<table>
				<tr>
					<th>Lunes</th>
					<th>Martes</th>
					<th>Miércoles</th>
					<th>Jueves</th>
					<th>Viernes</th>
					<th>Sábado</th>
					<th>Domingo</th>
				</tr>

				<?php
				for ($day = $primerDia; $day <= $ultimoDia; $day->modify('+1 day')) {
					$fechaActual = $day->format('Y-m-d');
					$esDiaActual = ($fechaActual == date('Y-m-d')); // Verifica si es el día actual

					if ($day->format('N') == 1) {
						echo "<tr>";
					}

					echo "<td";
					if ($day->format('m') != $month) {
						echo " class=\"other-month\"";
					}
					if ($esDiaActual) {
						echo " class=\"current-day\""; // Agrega la clase current-day para el día actual
					}
					echo ">" . ($esDiaActual ? '<strong>' : '') . $day->format('j') . ($esDiaActual ? '</strong>' : '');

					if (isset($eventos[$fechaActual])) {
						echo "<div>";
						foreach ($eventos[$fechaActual] as $evento) {
							echo "<div class=\"asignatura\">" . $evento['asignatura'] . "</div>";
							echo "<div class=\"evento\">" . $evento['nombre_evento'] . "</div>";

							// Verificar si hay anotaciones y mostrarlas si existen
							if (!empty($evento['anotaciones'])) {
								echo "<div class=\"anotaciones\"><i>" . $evento['anotaciones'] . "</i></div>";
							}
						}
						echo "</div>";
					}

					echo "</td>";

					if ($day->format('N') == 7) {
						echo "</tr>";
					}
				}
				?>

			</table>
			<br>
		</div>
		<?php
		$conn->close();
		?>
		<script>
			// Verificar el tamaño de la pantalla y redirigir si es necesario
			if (window.innerWidth <= 1000) { // Puedes ajustar el valor de 768 según tus necesidades
				window.location.href = "https://calendario.davidmartos.dev/vistaMensualMovil.php"; // Reemplaza con la URL a la que deseas redirigir
			}
		</script>
	</body>
</html>