<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Eventos</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../favicon.ico" />
    <link href="../../public/dist/output.css" rel="stylesheet">
</head>
<body>
    <h1 class="text-3xl font-bold">Eventos</h1>
    <table>
        <thead>
            <tr>
                <th>Asignatura</th>
                <th>Nombre</th>
                <th>Fecha</th>
                <th>Anotaciones</th>
                <!-- Otros campos si son necesarios -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($eventos as $evento) { ?>
                <tr>
                    <td><?php echo $evento->nombre_asignatura; ?></td>
                    <td><?php echo $evento->nombre; ?></td>
                    <td><?php echo $evento->fecha; ?></td>
                    <td><?php echo $evento->anotaciones; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
