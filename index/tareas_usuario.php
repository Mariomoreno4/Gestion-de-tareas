<?php
require_once 'conexion.php'; 

session_start(); // Iniciar la sesión

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

$id_usuario = $_GET['id_usuario']; // Obtener el id_usuario desde el enlace

// Obtener las tareas del usuario específico
$query_tareas = "SELECT * FROM tarea WHERE id_usuario = $id_usuario";
$resultado_tareas = mysqli_query($conn, $query_tareas);

if (!$resultado_tareas) {
    die("Error en la consulta de tareas: " . mysqli_error($conn));
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tareas de Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #42C7E8;
            font-family: Arial, sans-serif;
        }
        header, footer {
            background-color: #007bff;
            color: white;
        }
        .welcome-card {
            background-color: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .welcome-card .icon {
            font-size: 3rem;
            color: #007bff;
        }
        .welcome-card h2 {
            margin-top: 10px;
        }
        .table {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .table thead th {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .table tbody tr {
            transition: background-color 0.3s;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn-group .btn {
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-group .btn:hover {
            transform: scale(1.05);
        }
        .modal-header {
            background-color: #007bff;
            color: white;
        }
        .modal-header .btn-close {
            background-color: white;
        }
    </style>
</head>
<body>
    <header class="text-center py-3 mb-4">
        <div class="container">
            <h1>Tareas del Usuario</h1>
        </div>
    </header>
    <div class="container">
        <div class="welcome-card text-center">
            <div class="icon">
                <i class="fas fa-tasks"></i>
            </div>
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8'); ?>!</h2>
        </div>
        
        <div class="text-end mb-3">
            <a href="/Proyecto/logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            <a href="./usuarios.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>

        <section class="mt-4">
            <h3>Tareas</h3>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Tarea</th>
                            <th>Tarea</th>
                            <th>Fecha</th>
                            <th>Categoría</th>
                            <th>Importancia</th>
                            <th>Completada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($tarea = mysqli_fetch_assoc($resultado_tareas)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($tarea['id_tarea'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($tarea['tarea'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($tarea['fecha'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($tarea['categoria'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($tarea['importancia'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($tarea['completada'] ? 'Sí' : 'No', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-danger" onclick="eliminarTarea('<?php echo $tarea['id_tarea']; ?>')"><i class="fas fa-trash"></i> Eliminar</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    <footer class="text-center py-3 mt-4">
        <div class="container">
            &copy; <?php echo date('Y'); ?> | EXPLOSIVO
        </div>
    </footer>

    <script>
        function eliminarTarea(id) {
            if (confirm('¿Estás seguro de que deseas eliminar esta tarea?')) {
                $.post('eliminar_tarea.php', { id_tarea: id }, function(response) {
                    if (response === 'success') {
                        location.reload(); // Recargar la página para ver los cambios
                    } else {
                        console.log(response); // Mostrar el error en la consola
                        alert('Error al eliminar la tarea: ' + response);
                    }
                });
            }
        }
    </script>
</body>
</html>
