<!DOCTYPE html>
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

// Consulta para obtener eventos y asignaturas
$sql = "SELECT eventos.id, eventos.nombre as nombre_evento, eventos.fecha, eventos.anotaciones, asignaturas.nombre as nombre_asignatura, asignaturas.abreviatura as abreviatura_asignatura FROM eventos INNER JOIN asignaturas ON eventos.asignatura_id = asignaturas.id ORDER BY eventos.fecha ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Calendario</title>
		<style>
			* {
				box-sizing: border-box;
				margin: 0;
				padding: 0;
			}
			body {
				font-family: sans-serif;
				background-color: #f2f2f2;
			}
			.cabecera {
				background-color: #333;
				color: #f2f2f2;
				text-align: center;
			}
			.tituloDerecha {
				float: right;
				margin-left: 10px;
			}
			.cuerpo {
				padding: 10px;
			}
			.card {
				background-color: #fff;
				border-radius: 10px;
				margin: 10px 0;
				padding: 10px;
				box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
			}
			.card h2 {
				margin-bottom: 10px;
				font-size: 1.35em;
			}
			#tituloPagina {
				background-color: #333 !important;
				color: #f2f2f2 !important;
			}
			.tituloDerecha {
				float: right !important;
			}
			.izquierda {
				float: left !important;
			}
			.derecha {
				float: right !important;
			}
			.evento {
				margin-top: 5%;
				text-align: center;
				font-size: 1.15em;
			}
			.anotacion {
				text-align: center;
			}
		</style>
		<link rel="stylesheet" href="https://davidmartos.dev/base/styles.css" type="text/css">
	</head>
	<body>
		<div class="cabecera">
			<a id="tituloPagina">Calendario</a>
			<a class="tituloDerecha" href="exportarCalendario.php">Exportar calendario</a>
			<a class="tituloDerecha" href="/vistaMensualMovil.php">Vista mensual</a>
		</div>
		<div class="cuerpo">
			<h2>Eventos futuros</h2>
			<?php
			$hoy = date("Y-m-d");
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					if ($row["fecha"] >= $hoy) {
						echo "<div class='card'>";
						echo "<h2 class='izquierda'>" . date("d/m/Y", strtotime($row["fecha"])) . "</h2>";
						echo "<h2 class='derecha'>" . $row["abreviatura_asignatura"] . "</h2><br>";
						echo "<p class='evento'><strong>". $row["nombre_evento"] . "</strong></p>";
						echo "<p class='anotacion'>" . $row["anotaciones"] . "</p>";
						echo "</div>";
					}
				}
			} else {
				echo "<p>No hay eventos programados.</p>";
			}
			?>
			<br>
			<h2>Eventos pasados</h2>
			<?php
			$result->data_seek(0);
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					if ($row["fecha"] < $hoy) {
						echo "<div class='card'>";
						echo "<h2 class='izquierda'>" . date("d/m/Y", strtotime($row["fecha"])) . "</h2>";
						echo "<h2 class='derecha'>" . $row["abreviatura_asignatura"] . "</h2><br>";
						echo "<p class='evento'><strong>". $row["nombre_evento"] . "</strong></p>";
						echo "<p class='anotacion'>" . $row["anotaciones"] . "</p>";
						echo "</div>";
					}
				}
			} else {
				echo "<p>No hay eventos pasados.</p>";
			}

			$conn->close();
			?>
		</div>
		<script>
			// Verificar el tamaño de la pantalla y redirigir si es necesario
			if (window.innerWidth > 1000) { // Puedes ajustar el valor de 768 según tus necesidades
				window.location.href = "https://calendario.davidmartos.dev/index.php"; // Reemplaza con la URL a la que deseas redirigir
			}
		</script>
	</body>
</html>
