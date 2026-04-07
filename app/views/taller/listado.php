<!DOCTYPE html>
<html>

<head>

    <title>Listado Talleres</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
    
    <script src="public/js/jquery-4.0.0.min.js"></script>
    <script src="public/js/taller.js"></script>
</head>

<body class="container mt-5">

    <nav>
        <div>
            <a href="index.php?page=talleres">Talleres</a>
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <a href="index.php?page=admin">Gestionar Solicitudes</a>
            <?php endif; ?>
        </div>
        <div>
            <span> <?= htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['user'] ?? 'Usuario') ?></span>
            <!--Se agregó el onclick para que funcione el logout-->
            <button id="btnLogout" class="btn-logout" onclick="window.location.href='app/views/logout.php'">Cerrar sesión</button>
        </div>
    </nav>
    
    <main>
        <h3>Talleres Disponibles</h3>

        <table class="table table-borderless">

        </table>
    </main>



</body>

</html>