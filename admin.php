<?php
session_start();

// Comprueba si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
	header('Location: login.php');
	exit;
}

// Conexión a la base de datos
$servername = "db";
$username = "root";
$password = "";
$dbname = "calendario";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die("Error de conexión: " . $conn->connect_error);
}

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	// Validar nombre del evento
	if (empty(trim($_POST["nombre_evento"]))) {
		$nombre_evento_err = "Por favor, ingrese un nombre para el evento.";
	} else {
		$nombre_evento = trim($_POST["nombre_evento"]);
	}

	// Validar fecha
	if (empty(trim($_POST["fecha"]))) {
		$fecha_err = "Por favor, ingrese una fecha para el evento.";
	} else {
		$fecha = trim($_POST["fecha"]);
	}

	// Validar asignatura
	if (empty(trim($_POST["asignatura_id"]))) {
		$asignatura_id_err = "Por favor, seleccione una asignatura.";
	} else {
		$asignatura_id = trim($_POST["asignatura_id"]);
	}

	// Obtener anotaciones
	$anotaciones = trim($_POST["anotaciones"]);

	// Verificar errores de entrada antes de insertar en la base de datos
	if (empty($nombre_evento_err) && empty($fecha_err) && empty($asignatura_id_err)) {
		$sql = "INSERT INTO eventos (nombre, fecha, asignatura_id, anotaciones) VALUES (?, ?, ?, ?)";
		if ($stmt = $conn->prepare($sql)) {
			$stmt->bind_param("ssis", $param_nombre_evento, $param_fecha, $param_asignatura_id, $param_anotaciones);

			$param_nombre_evento = $nombre_evento;
			$param_fecha = $fecha;
			$param_asignatura_id = $asignatura_id;
			$param_anotaciones = $anotaciones;

			if ($stmt->execute()) {
				header("location: admin.php");
			} else {
				echo "Algo salió mal, por favor inténtalo de nuevo.";
			}
			$stmt->close();
		}
	}
}

// Consulta para obtener eventos y asignaturas
$sql = "SELECT eventos.id, eventos.nombre as nombre_evento, eventos.fecha, eventos.anotaciones, asignaturas.nombre as nombre_asignatura FROM eventos INNER JOIN asignaturas ON eventos.asignatura_id = asignaturas.id ORDER BY eventos.fecha ASC";
$result = $conn->query($sql);

// Consulta para obtener asignaturas
$sql_asignaturas = "SELECT * FROM asignaturas";
$result_asignaturas = $conn->query($sql_asignaturas);
?>

<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Calendario</title>
		<style>
			.cuerpo, table {
				border-collapse: collapse;
				margin-left: auto;
				margin-right: auto;
				width: 95%;
				margin-bottom: 1%;
			}
			table {
				border-collapse: collapse;
				margin-left: auto;
				margin-right: auto;
				width: 100%;
				table-layout: fixed;
			}
			th, td {
				border: 1px solid black;
				padding: 8px;
				text-align: left;
			}
			th {
				background-color: #f2f2f2;
			}
			* {
				font-family: sans-serif;
			}
			#tituloPagina {
				background-color: #333 !important;
				color: #f2f2f2 !important;
			}
			.tituloDerecha {
				float: right !important;
			}
		</style>
		<link rel="stylesheet" href="https://davidmartos.dev/base/styles.css" type="text/css">
	</head>
	<body>
		<div class="cabecera">
			<a id="tituloPagina">Calendario</a>
			<a class="tituloDerecha" href="logout.php">Cerrar sesión</a>
			<a class="tituloDerecha" href="crear_evento.php">Crear evento</a>
			<a class="tituloDerecha" href="/vistaMensual.php">Vista mensual</a>
		</div>
		<br>
		<div class="cuerpo">
			<h2>Eventos</h2>
			<table>
				<tr>
					<th>Fecha</th>
					<th>Módulo profesional</th>
					<th>Evento</th>
					<th>Anotaciones</th>
					<th>Acciones</th>
				</tr>
				<?php
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						echo "<tr>";
						echo "<td>" . date("d/m/Y", strtotime($row["fecha"])) . "</td>";
						echo "<td>" . $row["nombre_asignatura"] . "</td>";
						echo "<td>" . $row["nombre_evento"] . "</td>";
						echo "<td>" . $row["anotaciones"] . "</td>";
						echo "<td>";
						echo "<a href='editar_evento.php?id=" . $row["id"] . "'>Editar</a> | ";
						echo "<a href='eliminar_evento.php?id=" . $row["id"] . "'>Eliminar</a>";
						echo "</td>";
						echo "</tr>";
					}
				} else {
					echo "<tr><td colspan='5'>No hay eventos programados.</td></tr>";
				}
				?>
			</table>
			<?php
			$conn->close();
			?>
		</div>
		<div class="pie">
			<p>Sistema de Gestión de Calendario de Exámenes - David Martos Ruiz</p>
		</div>
	</body>
</html>

