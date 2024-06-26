<?php
require_once 'conexion.php'; // Incluye el archivo de conexión

session_start(); // Iniciar la sesión

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Asegurarse de que la conexión a la base de datos sea exitosa
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestión de tus tareas</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Añadir jQuery para AJAX -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> <!-- jQuery UI para pestañas -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<header id="head">
    <div class="container">
        Gestión de tus tareas
    </div>
</header>

<body>
    <div class="container main-content">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
        <a href="/Proyecto/logout.php">Cerrar sesión</a>

        <?php
        // Mostrar el id_usuario del usuario actual
        if (isset($_SESSION['usuario'])) {
            $usuario = $_SESSION['usuario'];

            // Consulta SQL para obtener el id_usuario del usuario actual
            $query_id_usuario = "SELECT id_usuario FROM logins WHERE usuario = ?";
            $stmt_id_usuario = mysqli_prepare($conn, $query_id_usuario);
            mysqli_stmt_bind_param($stmt_id_usuario, "s", $usuario);
            mysqli_stmt_execute($stmt_id_usuario);
            mysqli_stmt_bind_result($stmt_id_usuario, $id_usuario);
            mysqli_stmt_fetch($stmt_id_usuario);
            mysqli_stmt_close($stmt_id_usuario);

            echo "<p>ID de usuario: " . htmlspecialchars($id_usuario, ENT_QUOTES, 'UTF-8') . "</p>";
        }
        ?>

        <section>
            <h2>¿Cuál es tu próxima tarea?</h2>
            <form method="post" action="agregar_tarea.php"> <!-- Ajusta según tu archivo de manejo de agregar tarea -->
                <textarea name="nueva_tarea" placeholder="Escribe aquí tu nueva tarea"></textarea>
                <input type="date" name="fecha_tarea" id="start" value="2023-01-01" min="2023-01-01" max="2023-12-31" />
                <button type="submit" class="btn-edit">Agregar tarea</button>
            </form>
        </section>

        <section class="task-list">
            <h2>Tus tareas</h2>
            <div id="tabs">
                <ul>
                    <?php
                    // Obtener las tareas del usuario actual y agruparlas por mes
                    if (isset($id_usuario)) {
                        $query_tareas = "SELECT id_tarea, tarea, DATE_FORMAT(fecha, '%Y-%m') as mes, fecha FROM tarea WHERE id_usuario = ? ORDER BY fecha DESC";
                        $stmt_tareas = mysqli_prepare($conn, $query_tareas);
                        mysqli_stmt_bind_param($stmt_tareas, "i", $id_usuario);
                        mysqli_stmt_execute($stmt_tareas);
                        $resultado_tareas = mysqli_stmt_get_result($stmt_tareas);

                        $tareas_por_mes = [];

                        if ($resultado_tareas) {
                            while ($fila = mysqli_fetch_assoc($resultado_tareas)) {
                                $mes = $fila['mes'];
                                if (!isset($tareas_por_mes[$mes])) {
                                    $tareas_por_mes[$mes] = [];
                                }
                                $tareas_por_mes[$mes][] = $fila;
                            }
                            mysqli_free_result($resultado_tareas);
                        } else {
                            echo "Error en la consulta de tareas: " . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8');
                        }

                        mysqli_stmt_close($stmt_tareas);

                        // Generar las pestañas y el contenido
                        foreach ($tareas_por_mes as $mes => $tareas) {
                            echo '<li><a href="#tabs-' . htmlspecialchars($mes, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($mes, ENT_QUOTES, 'UTF-8') . '</a></li>';
                        }
                        echo '</ul>';

                        foreach ($tareas_por_mes as $mes => $tareas) {
                            echo '<div id="tabs-' . htmlspecialchars($mes, ENT_QUOTES, 'UTF-8') . '">';
                            foreach ($tareas as $tarea) {
                                echo '<article class="task-item" data-id="' . htmlspecialchars($tarea['id_tarea'], ENT_QUOTES, 'UTF-8') . '">';
                                echo '<h3>' . htmlspecialchars($tarea['tarea'], ENT_QUOTES, 'UTF-8') . '</h3>';
                                echo '<p>Fecha: ' . htmlspecialchars($tarea['fecha'], ENT_QUOTES, 'UTF-8') . '</p>';
                                echo '<div class="btn-group">';
                                echo '<button class="btn-edit" onclick="editarTarea(' . htmlspecialchars($tarea['id_tarea'], ENT_QUOTES, 'UTF-8') . ')">Editar</button>';
                                echo '<button class="btn-delete" onclick="eliminarTarea(' . htmlspecialchars($tarea['id_tarea'], ENT_QUOTES, 'UTF-8') . ')">Eliminar</button>';
                                echo '</div>';
                                echo '</article>';
                            }
                            echo '</div>';
                        }
                    } else {
                        echo "Error: No se pudo obtener el ID de usuario.";
                    }

                    mysqli_close($conn);
                    ?>
                </ul>
            </div>
        </section>
    </div>

    <footer>
        <div class="container">
            &copy; <?php echo date('Y'); ?> | EXPLOSIVO
        </div>
    </footer>

    <script>
        $(function() {
            $("#tabs").tabs();
        });

        function editarTarea(id) {
            var nuevaTarea = prompt("Edita tu tarea:");
            if (nuevaTarea != null && nuevaTarea.trim() != "") {
                $.ajax({
                    url: 'editar_tarea.php',
                    type: 'POST',
                    data: {id_tarea: id, nueva_tarea: nuevaTarea},
                    success: function(response) {
                        if (response === 'success') {
                            location.reload(); // Recargar la página para ver los cambios
                        } else {
                            location.reload(); // Recargar la página para ver los cambios
                        }
                    }
                });
            }
        }

        function eliminarTarea(id) {
            if (confirm("¿Estás seguro de que quieres eliminar esta tarea?")) {
                $.ajax({
                    url: 'eliminar_tarea.php',
                    type: 'POST',
                    data: {id_tarea: id},
                    success: function(response) {
                        if (response === 'success') {
                            $('article[data-id="' + id + '"]').remove(); // Eliminar el elemento de la página sin recargar
                        } else {
                            $('article[data-id="' + id + '"]').remove(); // Eliminar el elemento de la página sin recargar
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>
