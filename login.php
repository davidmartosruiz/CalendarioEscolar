<?php
session_start();

// Verifica si el usuario ya ha iniciado sesión
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: admin.php");
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

$username = $password = "";
$username_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Por favor, ingrese su nombre de usuario.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor, ingrese su contraseña.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, username, password FROM usuarios WHERE username = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $username, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            session_start();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            
                            header("location: admin.php");
                        } else {
                            $password_err = "Credenciales inválidas, inténtalo de nuevo.";
                        }
                    }
                } else {
                    $password_err = "Credenciales inválidas, inténtalo de nuevo.";
                }
            } else {
                $password_err = "Algo salió mal, por favor inténtalo de nuevo.";
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
    <title>Iniciar sesión</title>
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
        <h2>Iniciar sesión</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <label style="width: 120px;display: inline-block;">Usuario</label>
                <input type="text" name="username" value="<?php echo $username; ?>"><br>
                <span class="error"><?php echo $username_err; ?></span>
            </div>
            <div>
                <label style="width: 120px;display: inline-block;">Contraseña</label>
                <input type="password" name="password"><br>
				<span class="error"><?php echo $password_err; ?></span>
			</div>
			<div>
				<input type="submit" value="Iniciar sesión">
			</div>
			<p>¿No tienes una cuenta? <a href="registro.php">Regístrate ahora</a>.</p>
		</form>
	</div>
	</body>
</html>