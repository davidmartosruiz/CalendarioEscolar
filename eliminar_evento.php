<?php
session_start();

// Comprueba si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}


// Verificar si se proporciona un ID de evento válido
if (isset($_GET['id'])) {
    // Obtener el ID del evento a eliminar desde la URL
    $eventoID = $_GET['id'];

    // Realizar la lógica para eliminar el evento con el ID proporcionado
    $servername = "db";
    $username = "root";
    $password = "";
    $dbname = "calendario";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $query = "DELETE FROM eventos WHERE id = '$eventoID'";
    $result = $conn->query($query);

    // Cerrar la conexión a la base de datos
    $conn->close();

    // Redirigir a la página de administración después de eliminar el evento
    header('Location: /admin.php');
    exit();
} else {
    // Si no se proporcionó un ID válido, redirigir a la página de administración
    header('Location: /admin.php');
    exit();
}
?>
