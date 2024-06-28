
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de tus tareas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>

<body>
    <header class="bg-primary text-white text-center py-3 mb-4">
        <div class="container">
            <h1>Gestión de tus tareas</h1>
        </div>
    </header>
    <style>
    .tarea-completada {
        background-color: green; /* Color oscuro de fondo */
        color: #fff; /* Texto claro */
    }
</style>
    <div class="container">
        <h2 class="text-center">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8'); ?>!</h2>
        <div class="text-end">
            <a href="/Proyecto/logout.php" class="btn btn-danger">Cerrar sesión</a>
            <a href="/Proyecto/index/History/historial.php" class="btn btn-primary">Historial</a>

        </div>

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

        <section class="mt-4">
            <h3>¿Cuál es tu próxima tarea?</h3>
            <form method="post" action="agregar_tarea.php">
                <div class="mb-3">
                    <textarea class="form-control" name="nueva_tarea" placeholder="Escribe aquí tu nueva tarea" required></textarea>
                </div>
                <div class="mb-3">
                    <input type="date" class="form-control" name="fecha_tarea" required>
                </div>
                <div class="mb-3">
                    <select class="form-control" name="categoria" required>
                        <option value="">Selecciona una categoría</option>
                        <option value="Trabajo">Trabajo</option>
                        <option value="Personal">Personal</option>
                        <!-- Añade más opciones de categorías aquí -->
                    </select>
                </div>
                <div class="mb-3">
                    <select class="form-control" name="importancia" required>
                        <option value="">Selecciona una importancia</option>
                        <option value="1">Baja</option>
                        <option value="2">Media</option>
                        <option value="3">Alta</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Agregar tarea</button>
            </form>
        </section>

        <section class="task-list mt-4">
            <h3>Tus tareas</h3>
            <div id="tabs">
                <ul>
                    <?php
                    // Obtener las tareas del usuario actual y agruparlas por mes
                    if (isset($id_usuario)) {
                        $query_tareas = "SELECT id_tarea, tarea, DATE_FORMAT(fecha, '%Y-%m') as mes, fecha, categoria, importancia, completada FROM tarea WHERE id_usuario = ? AND completada = 0 ORDER BY fecha DESC";
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
                                echo '<div class="card mb-3';
                                if (isset($tarea['completada']) && $tarea['completada']) {
                                    echo ' tarea-completada";'; // Agrega una clase si la tarea está completada
                                } else {
                                    echo '";';
                                }
                                echo '>';
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">' . htmlspecialchars($tarea['tarea'], ENT_QUOTES, 'UTF-8') . '</h5>';
                                echo '<p class="card-text">Fecha: ' . htmlspecialchars($tarea['fecha'], ENT_QUOTES, 'UTF-8') . '</p>';
                            

                                // Mostrar la categoría
                                if (isset($tarea['categoria']) && !empty($tarea['categoria'])) {
                                    echo '<p class="card-text">Categoría: ' . htmlspecialchars($tarea['categoria'], ENT_QUOTES, 'UTF-8') . '</p>';
                                } else {
                                    echo '<p class="card-text">Categoría: Sin categoría</p>';
                                }

                                // Mostrar la importancia
                                if (isset($tarea['importancia']) && !empty($tarea['importancia'])) {
                                    $importancia_texto = '';
                                    switch ($tarea['importancia']) {
                                        case '1':
                                            $importancia_texto = 'Baja';
                                            break;
                                        case '2':
                                            $importancia_texto = 'Media';
                                            break;
                                        case '3':
                                            $importancia_texto = 'Alta';
                                            break;
                                        default:
                                            $importancia_texto = 'No especificada';
                                            break;
                                    }
                                    echo '<p class="card-text">Importancia: ' . htmlspecialchars($importancia_texto, ENT_QUOTES, 'UTF-8') . '</p>';
                                } else {
                                    echo '<p class="card-text">Importancia: No especificada</p>';
                                }

   // Checkbox de completada
echo '<div class="form-check">';
echo '<input class="form-check-input" type="checkbox" id="completada-' . htmlspecialchars($tarea['id_tarea'], ENT_QUOTES, 'UTF-8') . '" ' . (isset($tarea['completada']) && $tarea['completada'] ? 'checked' : '') . ' onclick="marcarCompletada(' . htmlspecialchars($tarea['id_tarea'], ENT_QUOTES, 'UTF-8') . ')">';
echo '<label class="form-check-label" for="completada-' . htmlspecialchars($tarea['id_tarea'], ENT_QUOTES, 'UTF-8') . '">Completada</label>';
echo '</div>';
    // Botones de editar y eliminar
    echo '<div class="btn-group" role="group">';
    echo '<button class="btn btn-secondary" onclick="editarTarea(' . htmlspecialchars($tarea['id_tarea'], ENT_QUOTES, 'UTF-8') . ')">Editar</button>';
    echo '<button class="btn btn-danger" onclick="eliminarTarea(' . htmlspecialchars($tarea['id_tarea'], ENT_QUOTES, 'UTF-8') . ')">Eliminar</button>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
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

    <footer class="bg-primary text-white text-center py-3 mt-4">
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
                            location.reload();
                        } else {
                            alert("Error al editar la tarea.");
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
                            location.reload();
                        } else {
                            alert("Error al eliminar la tarea.");
                        }
                    }
                });
            }
        }

        function marcarCompletada(id) {
    $.ajax({
        url: 'marcar_completada.php',
        type: 'POST',
        data: {id_tarea: id},
        success: function(response) {
            if (response === 'success') {
                // Actualizar visualmente el estado de completada
                var checkbox = $('#completada-' + id);
                checkbox.prop('checked', !checkbox.prop('checked')); // Invertir estado actual del checkbox

                // Opcional: Actualizar estilo u otros elementos de la tarea completada
                checkbox.closest('.card').toggleClass('tarea-completada');
            } else {
                alert("Error al marcar la tarea como completada.");
            }
        }
    });
}


    </script>
</body>
</html>
