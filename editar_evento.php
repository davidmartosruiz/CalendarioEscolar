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

$event_id = $_GET["id"];
$nombre_evento = $fecha = $asignatura_id = "";
$nombre_evento_err = $fecha_err = $asignatura_id_err = "";
$anotaciones_err = "";

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$event_id = $_POST["id"];

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

	// Obtener el valor de las anotaciones del formulario
	if (empty(trim($_POST["anotaciones"]))) {
		$anotaciones = "";
	} else {
		$anotaciones = trim($_POST["anotaciones"]);
	}

	// Validar asignatura
	if (empty(trim($_POST["asignatura_id"]))) {
		$asignatura_id_err = "Por favor, seleccione una asignatura.";
	} else {
		$asignatura_id = trim($_POST["asignatura_id"]);
	}

	// Verificar errores de entrada antes de actualizar en la base de datos
	if (empty($nombre_evento_err) && empty($fecha_err) && empty($asignatura_id_err) && empty($anotaciones_err)) {
		$sql = "UPDATE eventos SET nombre = ?, fecha = ?, asignatura_id = ?, anotaciones = ? WHERE id = ?";
		if ($stmt = $conn->prepare($sql)) {
			$stmt->bind_param("ssssi", $param_nombre_evento, $param_fecha, $param_asignatura_id, $param_anotaciones, $param_event_id);

			$param_nombre_evento = $nombre_evento;
			$param_fecha = $fecha;
			$param_asignatura_id = $asignatura_id;
			$param_anotaciones = $anotaciones;
			$param_event_id = $event_id;

			if ($stmt->execute()) {
				header("location: admin.php");
			} else {
				echo "Algo salió mal, por favor inténtalo de nuevo.";
			}
			$stmt->close();
		}
	}
}

// Obtener datos del evento
$sql = "SELECT * FROM eventos WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
	$stmt->bind_param("i", $param_event_id);
	$param_event_id = $event_id;

	if ($stmt->execute()) {
		$result = $stmt->get_result();
		if ($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			$nombre_evento = $row["nombre"];
			$fecha = $row["fecha"];
			$asignatura_id = $row["asignatura_id"];
			$anotaciones = $row["anotaciones"];
		} else {
			header("location: admin.php");
			exit();
		}
	} else {
		echo "Algo salió mal, por favor inténtalo de nuevo.";
	}

	// Obtener datos de las asignaturas
	$sql_asignaturas = "SELECT id, nombre FROM asignaturas";
	$stmt_asignaturas = $conn->prepare($sql_asignaturas);
	$stmt_asignaturas->execute();
	$result_asignaturas = $stmt_asignaturas->get_result();
	$stmt_asignaturas->close();

	$stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Editar Evento</title>
		<style>
			body {
				font-family: sans-serif;
			}
			.wrapper {
				width: 350px;
				padding: 20px;
				margin: auto;
				margin-top: 50px;
				border: 1px solid #ccc;
				border-radius: 4px;
			}
			.error {
				color: red;
			}
		</style>
	</head>
	<body>
		<div class="wrapper">
			<h2>Editar Evento</h2>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<input type="hidden" name="id" value="<?php echo $event_id; ?>">
				<div>
					<label>Nombre del evento</label>
					<input type="text" name="nombre_evento" value="<?php echo $nombre_evento; ?>">
					<span class="error"><?php echo $nombre_evento_err; ?></span>
				</div>
				<div>
					<label>Fecha</label>
					<input type="date" name="fecha" value="<?php echo $fecha; ?>">
					<span class="error"><?php echo $fecha_err; ?></span>
				</div>
				<div>
					<label>Módulo</label>
					<select name="asignatura_id">
						<?php
						if ($result_asignaturas->num_rows > 0) {
							while ($row_asignatura = $result_asignaturas->fetch_assoc()) {
								$selected = ($asignatura_id == $row_asignatura["id"]) ? "selected" : "";
								echo "<option value='" . $row_asignatura["id"] . "' " . $selected . ">" . $row_asignatura["nombre"] . "</option>";
							}
						}
						?>
					</select>
					<span class="error"><?php echo $asignatura_id_err; ?></span>
				</div>
				<div>
					<label>Anotaciones</label>
					<textarea name="anotaciones"><?php echo $anotaciones; ?></textarea>
					<span class="error"><?php echo $anotaciones_err; ?></span>
				</div>

				<div>
					<input type="submit" value="Actualizar Evento">
				</div>
			</form>
		</div>
	</body>
</html>

