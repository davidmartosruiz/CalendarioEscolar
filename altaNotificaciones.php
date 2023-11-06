<!DOCTYPE html>
<?php
$block_new_registrations = true;

// Conexión a la base de datos
$servername = "db";
$username = "root";
$password = "";
$dbname = "calendario";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die("Error de conexión: " . $conn->connect_error);
}

$name = $email = "";
$name_err = $email_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Validar nombre
	if (empty(trim($_POST["name"]))) {
		$name_err = "Por favor, ingrese un nombre.";
	} else {
		$name = trim($_POST["name"]);
	}


	// Validar correo electrónico
	if (empty(trim($_POST["email"]))) {
		$email_err = "Por favor, ingrese un correo electrónico.";
	} else {
		$email = trim($_POST["email"]);
		if (substr($email, -14) !== "@g.educaand.es") {
			$email_err = "El correo electrónico ser @g.educaand.es.";
		} else {
			$sql = "SELECT idUsuarioNotificaciones FROM usuariosNotificaciones WHERE correoUsuarioNotificaciones = ?";
			if ($stmt = $conn->prepare($sql)) {
				$stmt->bind_param("s", $param_email);
				$param_email = $email;
				if ($stmt->execute()) {
					$stmt->store_result();
					if ($stmt->num_rows == 1) {
						$email_err = "Ese correo ya está registrado.";
					}
				} else {
					echo "Algo salió mal, por favor inténtalo de nuevo.";
				}
				$stmt->close();
			}
		}
	}



	// Verificar errores de entrada antes de insertar en la base de datos
	if (empty($name_err) && empty($email_err)) {
		$sql = "INSERT INTO usuariosNotificaciones (nombreUsuarioNotificaciones, correoUsuarioNotificaciones) VALUES (?, ?)";
		if ($stmt = $conn->prepare($sql)) {
			$stmt->bind_param("ss", $param_name, $param_email);
			$param_name = $name;
			$param_email = $email;

			if ($stmt->execute()) {
				header("location: index.php");
			} else {
				echo "Algo salió mal, por favor inténtalo de nuevo.";
			}
			$stmt->close();
		}
	}
	$conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Alta en notificaciones</title>
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
			<h2>Alta en notificaciones</h2>
			<?php if ($block_new_registrations): ?>
			<p>Lo sentimos, los registros están temporalmente bloqueados. Por favor, contacta con tu delegado.</p>
			<?php else: ?>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div>
					<label style="width: 140px;display: inline-block;">Nombre</label>
					<input type="text" name="name" value="<?php echo $name; ?>"><br>
					<span class="error"><?php echo $name_err; ?></span>
				</div>
				<div>
					<label style="width: 140px;display: inline-block;">Correo electrónico</label>
					<input type="email" name="email" value="<?php echo $email; ?>"><br>
					<span class="error"><?php echo $email_err; ?></span>
				</div>
				<div>
					<input type="submit" value="Registrarse">
				</div>
				<?php endif; ?>
			</form>
		</div>
	</body>
</html>