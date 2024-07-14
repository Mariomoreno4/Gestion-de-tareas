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

// Obtener el id_usuario del usuario actual
if (isset($_SESSION['usuario'])) {
    $usuario = $_SESSION['usuario'];
    $query_id_usuario = "SELECT id_usuario FROM logins WHERE usuario = ?";
    $stmt_id_usuario = mysqli_prepare($conn, $query_id_usuario);
    mysqli_stmt_bind_param($stmt_id_usuario, "s", $usuario);
    mysqli_stmt_execute($stmt_id_usuario);
    mysqli_stmt_bind_result($stmt_id_usuario, $id_usuario);
    mysqli_stmt_fetch($stmt_id_usuario);
    mysqli_stmt_close($stmt_id_usuario);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de tus tareas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <style>
        body {
            background-color: #42C7E8;
            font-family: Arial, sans-serif;
        }
        .tarea-completada {
            background-color: green; 
            color: #fff; 
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
        .btn-group .btn {
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-group .btn:hover {
            transform: scale(1.05);
        }
        .search-bar {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <header class="text-center py-3 mb-4">
        <div class="container">
            <h1>Historial</h1>
        </div>
    </header>
    <div class="container">
        <div class="welcome-card text-center">
            <div class="icon">
                <i class="fas fa-history"></i>
            </div>
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8'); ?>!</h2>
            <p>ID de usuario: <?php echo htmlspecialchars($id_usuario, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="text-end mb-3">
            <a href="../../logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            <a href="../tarea.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>

        <section class="mt-4">
            <h3>Buscar Tarea Completada</h3>
            <div class="input-group search-bar">
                <input type="text" id="buscar" class="form-control" placeholder="Buscar tarea completada" onkeyup="buscarTareas()">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
        </section>

        <section class="task-list mt-4">
            <h3>Tus tareas completadas</h3>
            <div id="tareas-completadas">
                <?php
                // Obtener las tareas completadas del usuario actual
                if (isset($id_usuario)) {
                    $query_tareas_completadas = "SELECT id_tarea, tarea, fecha, categoria, importancia FROM tarea WHERE id_usuario = ? AND completada = 1 ORDER BY fecha DESC";
                    $stmt_tareas_completadas = mysqli_prepare($conn, $query_tareas_completadas);
                    mysqli_stmt_bind_param($stmt_tareas_completadas, "i", $id_usuario);
                    mysqli_stmt_execute($stmt_tareas_completadas);
                    $resultado_tareas_completadas = mysqli_stmt_get_result($stmt_tareas_completadas);

                    if ($resultado_tareas_completadas) {
                        $count = 0; // Inicializamos el contador para controlar las columnas

                        while ($tarea = mysqli_fetch_assoc($resultado_tareas_completadas)) {
                            if ($count % 2 == 0) { // Abre una nueva fila de Bootstrap cada dos tarjetas
                                echo '<div class="row">';
                            }

                            echo '<div class="col-md-6">';
                            echo '<div class="card mb-3 tarea-completada">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">' . htmlspecialchars($tarea['tarea'], ENT_QUOTES, 'UTF-8') . '</h5>';
                            echo '<p class="card-text"><i class="fas fa-calendar-alt"></i> ' . htmlspecialchars($tarea['fecha'], ENT_QUOTES, 'UTF-8') . '</p>';
                            echo '<p class="card-text"><i class="fas fa-tag"></i> ' . htmlspecialchars($tarea['categoria'], ENT_QUOTES, 'UTF-8') . '</p>';
                            echo '<p class="card-text"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($tarea['importancia'], ENT_QUOTES, 'UTF-8') . '</p>';
                            echo '<button class="btn btn-warning" onclick="desmarcarTarea(' . $tarea['id_tarea'] . ')"><i class="fas fa-undo"></i> Desmarcar</button> ';
                            echo '<button class="btn btn-danger" onclick="eliminarTarea(' . $tarea['id_tarea'] . ')"><i class="fas fa-trash"></i> Eliminar</button>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';

                            if ($count % 2 != 0 || $count == mysqli_num_rows($resultado_tareas_completadas) - 1) { // Cierra la fila si es el último elemento o se han mostrado dos tarjetas
                                echo '</div>'; // Cierra la fila de Bootstrap
                            }

                            $count++;
                        }

                        mysqli_free_result($resultado_tareas_completadas);
                    } else {
                        echo "Error en la consulta de tareas completadas: " . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8');
                    }

                    mysqli_stmt_close($stmt_tareas_completadas);
                } else {
                    echo "Error: No se pudo obtener el ID de usuario.";
                }

                mysqli_close($conn);
                ?>
            </div>
        </section>
    </div>

    <footer class="text-center py-3 mt-4">
        <div class="container">
            &copy; <?php echo date('Y'); ?> | EXPLOSIVO
        </div>
    </footer>

    <script>
        function buscarTareas() {
            var input = document.getElementById("buscar");
            var filter = input.value.toLowerCase();
            var tareas = document.getElementById("tareas-completadas").getElementsByClassName("card");

            for (var i = 0; i < tareas.length; i++) {
                var tarea = tareas[i].getElementsByClassName("card-title")[0];
                if (tarea) {
                    var txtValue = tarea.textContent || tarea.innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        tareas[i].style.display = "";
                    } else {
                        tareas[i].style.display = "none";
                    }
                }       
            }
        }

        function desmarcarTarea(id_tarea) {
            $.post('marcar_completada.php', { id_tarea: id_tarea }, function(response) {
                if (response === 'success') {
                    location.reload(); // Recargar la página para ver los cambios
                } else {
                    alert('Error al desmarcar la tarea');
                }
            });
        }

        function eliminarTarea(id_tarea) {
            if (confirm('¿Estás seguro de que deseas eliminar esta tarea?')) {
                $.post('eliminar_tarea.php', { id_tarea: id_tarea }, function(response) {
                    if (response === 'success') {
                        location.reload(); // Recargar la página para ver los cambios
                    } else {
                        alert('Error al eliminar la tarea');
                    }
                });
            }
        }
    </script>
</body>
</html>
