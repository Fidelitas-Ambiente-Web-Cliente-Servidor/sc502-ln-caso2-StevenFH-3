<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Solicitudes pendientes</title>
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/jquery-4.0.0.min.js"></script>
    <script src="public/js/solicitud.js"></script>
</head>
<body class="container mt-5">
    <nav>
        <div>
            <a href="index.php?page=talleres">Talleres</a>
            <a href="index.php?page=admin">Gestionar Solicitudes</a>
        </div>
        <div>
            <span>Admin: <?= htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['user'] ?? 'Administrador') ?></span>
            <!--Se agregó el onclick para que funcione el logout-->
            <button id="btnLogout" class="btn-logout" onclick="window.location.href='app/views/logout.php'">Cerrar sesión</button>
        </div>
    </nav>
    
    <main>
        <h2>Solicitudes pendientes de aprobación</h2>
        
        <div class="table-container">
            <table id="tabla-solicitudes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Taller</th>
                        <th>Solicitante</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="solicitudes-body">
                    <tr>
                        <td colspan="5" class="loader text-center">Cargando solicitudes...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="mensaje"></div>


    </main>

    

</body>
</html>