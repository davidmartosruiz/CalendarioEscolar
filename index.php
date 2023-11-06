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
$sql = "SELECT eventos.id, eventos.nombre as nombre_evento, eventos.fecha, eventos.anotaciones, asignaturas.nombre as nombre_asignatura FROM eventos INNER JOIN asignaturas ON eventos.asignatura_id = asignaturas.id ORDER BY eventos.fecha ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Calendario</title>
		<style>
			h2 {
				margin: 2%;
			}
			table {
				border-collapse: collapse;
				width: 96%;
				margin: 2%;
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
			<a class="tituloDerecha" href="login.php">Iniciar sesión</a>
			<a class="tituloDerecha" href="exportarCalendario.php">Exportar calendario</a>
			<a class="tituloDerecha" href="/vistaMensual.php">Vista mensual</a>
			<a class="tituloDerecha" href="/">Vista listado</a>
		</div>
		<br>
		<div class="cuerpo">
			<h2>Eventos futuros</h2>
			<table>
				<tr>
					<th>Fecha</th>
					<th>Módulo profesional</th>
					<th>Evento</th>
					<th>Anotaciones</th>
				</tr>
				<?php
				$hoy = date("Y-m-d");
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						if ($row["fecha"] >= $hoy) {
							echo "<tr>";
							echo "<td>" . date("d/m/Y", strtotime($row["fecha"])) . "</td>";
							echo "<td>" . $row["nombre_asignatura"] . "</td>";
							echo "<td>" . $row["nombre_evento"] . "</td>";
							echo "<td>" . $row["anotaciones"] . "</td>";
							echo "</tr>";
						}
					}
				} else {
					echo "<tr><td colspan='4'>No hay eventos programados.</td></tr>";
				}
				?>
			</table>

			<h2>Eventos pasados</h2>
			<table>
				<tr>
					<th>Fecha</th>
					<th>Módulo profesional</th>
					<th>Evento</th>
					<th>Anotaciones</th>
				</tr>
				<?php
				$result->data_seek(0);
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						if ($row["fecha"] < $hoy) {
							echo "<tr>";
							echo "<td>" . date("d/m/Y", strtotime($row["fecha"])) . "</td>";
							echo "<td>" . $row["nombre_asignatura"] . "</td>";
							echo "<td>" . $row["nombre_evento"] . "</td>";
							echo "<td>" . $row["anotaciones"] . "</td>";
							echo "</tr>";
						}
					}
				} else {
					echo "<tr><td colspan='4'>No hay eventos pasados.</td></tr>";
				}
				?>
			</table>   
			<?php
			$conn->close();
			?>
		</div>
		<script>
			// Verificar el tamaño de la pantalla y redirigir si es necesario
			if (window.innerWidth <= 1000) { // Puedes ajustar el valor de 768 según tus necesidades
				window.location.href = "https://calendario.davidmartos.dev/vistaMovil.php"; // Reemplaza con la URL a la que deseas redirigir
			}
		</script>
	</body>
</html>
