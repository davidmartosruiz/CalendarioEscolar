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

$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar nombre de usuario
    if (empty(trim($_POST["username"]))) {
        $username_err = "Por favor, ingrese un nombre de usuario.";
    } else {
        $sql = "SELECT id FROM usuarios WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = trim($_POST["username"]);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $username_err = "Este nombre de usuario ya está en uso.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Algo salió mal, por favor inténtalo de nuevo.";
            }
            $stmt->close();
        }
    }

    // Validar correo electrónico
    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor, ingrese un correo electrónico.";
    } else {
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = trim($_POST["email"]);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $email_err = "Este correo electrónico ya está en uso.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Algo salió mal, por favor inténtalo de nuevo.";
            }
            $stmt->close();
        }
    }

    // Validar contraseña
    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor, ingrese una contraseña.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validar confirmación de contraseña
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Por favor, confirme la contraseña.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Las contraseñas no coinciden.";
        }
    }

    // Verificar errores de entrada antes de insertar en la base de datos
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)) {
        $sql = "INSERT INTO usuarios (username, password, email) VALUES (?, ?, ?)";
		if ($stmt = $conn->prepare($sql)) {
			$stmt->bind_param("sss", $param_username, $param_password, $param_email);
        $param_username = $username;
        $param_password = password_hash($password, PASSWORD_DEFAULT); // Crea un hash de contraseña
        $param_email = $email;

        if ($stmt->execute()) {
            header("location: login.php");
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
    <title>Registrarse</title>
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
        <h2>Registrarse</h2>
		<?php if ($block_new_registrations): ?>
            <p>Lo sentimos, los registros están temporalmente bloqueados. Por favor, inténtelo más tarde.</p>
        <?php else: ?>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div>
					<label>Nombre de usuario</label>
					<input type="text" name="username" value="<?php echo $username; ?>">
					<span class="error"><?php echo $username_err; ?></span>
				</div>
				<div>
					<label>Correo electrónico</label>
					<input type="email" name="email" value="<?php echo $email; ?>">
					<span class="error"><?php echo $email_err; ?></span>
				</div>
				<div>
					<label>Contraseña</label>
					<input type="password" name="password" value="<?php echo $password; ?>">
					<span class="error"><?php echo $password_err; ?></span>
				</div>
				<div>
					<label>Confirmar contraseña</label>
					<input type="password" name="confirm_password" value="<?php echo $confirm_password; ?>">
					<span class="error"><?php echo $confirm_password_err; ?></span>
				</div>
				<div>
					<input type="submit" value="Registrarse">
				</div>
			<?php endif; ?>
            <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>.</p>
        </form>
    </div>
</body>
</html>