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
			<a class="tituloDerecha" href="/admin.php">Volver</a>
		</div>
		<br>
		<div class="cuerpo">
			<h2>Agregar nuevo evento</h2>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<table>
					<tr>
						<td>Fecha:</td>
						<td><input type="date" name="fecha" required></td>
					</tr>
					<tr>
						<td>Módulo profesional:</td>
						<td>
							<select name="asignatura_id" required>
								<option value="" disabled selected>Selecciona una opción</option>
								<?php
								if ($result_asignaturas->num_rows > 0) {
									while ($row = $result_asignaturas->fetch_assoc()) {
										echo "<option value='" . $row["id"] . "'>" . $row["nombre"] . "</option>";
									}
								} else {
									echo "<option disabled>No hay asignaturas disponibles.</option>";
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Evento:</td>
						<td>
						<select name="nombre_evento" required>
							<option value="" disabled selected>Selecciona una opción</option>
							<option value="Examen">Examen</option>
							<option value="Entrega actividades">Entrega actividades</option>
							<option value="Entrega trabajo">Entrega trabajo</option>
							<option value="Exposición">Exposición</option>
							
							<option value="" disabled></option>
							
							<option value="Apertura plazo">Apertura plazo</option>
							<option value="Finalización plazo">Finalización plazo</option>
							
							<option value="" disabled></option>
							
							<option value="Festivo">Festivo</option>
							<option value="Vacaciones">Vacaciones</option>
							<option value="Huelga convocada">Huelga convocada</option>
							
							<option value="" disabled></option>
							
							<option value="Sin especificar">Sin especificar</option>
							<option value="Evento de prueba">Evento de prueba</option>
						</select>
						</td>
					</tr>
					<tr>
						<td>Anotaciones:</td>
						<td><textarea name="anotaciones" rows="4" cols="50"></textarea></td>
					</tr>
					<tr>
						<td colspan="2"><input type="submit" value="Agregar evento"></td>
					</tr>
				</table>
			</form>

			<?php
			$conn->close();
			?>
		</div>
		<div class="pie">
			<p>Sistema de Gestión de Calendario de Exámenes - David Martos Ruiz</p>
		</div>
	</body>
</html>

