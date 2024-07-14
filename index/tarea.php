<?php
require_once 'conexion.php'; 
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];
$query = "SELECT tipo, id_usuario FROM logins WHERE usuario = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $tipo, $id_usuario);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$_SESSION['tipo'] = $tipo;

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
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
        .task-list .card {
            transition: transform 0.2s;
        }
        .task-list .card:hover {
            transform: scale(1.05);
        }
        .task-list .card .card-title {
            font-weight: bold;
        }
        .form-control {
            position: relative;
            padding-left: 2.5rem;
        }
        .form-control-icon {
            position: absolute;
            left: 0;
            height: 100%;
            display: flex;
            align-items: center;
            padding-left: 0.75rem;
            color: #6c757d;
        }
        .btn {
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn:hover {
            transform: scale(1.05);
        }
        .tooltip-inner {
            max-width: none;
            text-align: left;
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
    </style>
</head>
<body>
    <header class="text-center py-3 mb-4">
        <div class="container">
            <h1>Gestión de tus tareas</h1>
        </div>
    </header>
    <div class="container">
        <div class="welcome-card text-center">
            <div class="icon">
                <i class="fas fa-smile"></i>
            </div>
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8'); ?>!</h2>
            <p>ID de usuario: <?php echo htmlspecialchars($id_usuario, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        
        <div class="text-end mb-3">
            <a href="/Proyecto/logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            <a href="./History/historial.php" class="btn btn-primary"><i class="fas fa-history"></i> Historial</a>
            <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 1): ?>
                <a href="./usuarios.php" class="btn btn-primary"><i class="fas fa-users"></i> Usuarios</a>
            <?php endif; ?>
        </div>

        <section class="mt-4">
            <h3>¿Cuál es tu próxima tarea?</h3>
            <form method="post" action="agregar_tarea.php">
                <div class="mb-3 position-relative">
                    <i class="fas fa-tasks form-control-icon"></i>
                    <textarea class="form-control" name="nueva_tarea" placeholder="Escribe aquí tu nueva tarea" required></textarea>
                </div>
                <div class="mb-3 position-relative">
                    <i class="fas fa-calendar-alt form-control-icon"></i>
                    <input type="date" class="form-control" name="fecha_tarea" required>
                </div>
                <div class="mb-3 position-relative">
                    <i class="fas fa-tags form-control-icon"></i>
                    <select class="form-control" name="categoria" required>
                        <option value="">Selecciona una categoría</option>
                        <option value="Trabajo">Trabajo</option>
                        <option value="Personal">Personal</option>
                    </select>
                </div>
                <div class="mb-3 position-relative">
                    <i class="fas fa-exclamation-circle form-control-icon"></i>
                    <select class="form-control" name="importancia" required>
                        <option value="">Selecciona una importancia</option>
                        <option value="1">Baja</option>
                        <option value="2">Media</option>
                        <option value="3">Alta</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar tarea</button>
            </form>
        </section>
        <section class="task-list mt-4">
            <h3>Tus tareas</h3>
            <div id="tabs">
                <ul>
                    <?php
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

                        foreach ($tareas_por_mes as $mes => $tareas) {
                            echo '<li><a href="#tabs-' . htmlspecialchars($mes, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($mes, ENT_QUOTES, 'UTF-8') . '</a></li>';
                        }
                        echo '</ul>';

                        foreach ($tareas_por_mes as $mes => $tareas) {
                            echo '<div id="tabs-' . htmlspecialchars($mes, ENT_QUOTES, 'UTF-8') . '" class="row">';
                            $count = 0;

                            foreach ($tareas as $tarea) {
                                if ($count % 2 == 0) {
                                    echo '<div class="row">';
                                }

                                echo '<div class="col-md-6">';
                                echo '<div class="card mb-3';
                                if (isset($tarea['completada']) && $tarea['completada']) {
                                    echo ' tarea-completada';
                                }
                                echo '">';
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">' . htmlspecialchars($tarea['tarea'], ENT_QUOTES, 'UTF-8') . '</h5>';
                                echo '<p class="card-text">Fecha: ' . htmlspecialchars($tarea['fecha'], ENT_QUOTES, 'UTF-8') . '</p>';

                                if (isset($tarea['categoria']) && !empty($tarea['categoria'])) {
                                    echo '<p class="card-text">Categoría: ' . htmlspecialchars($tarea['categoria'], ENT_QUOTES, 'UTF-8') . '</p>';
                                } else {
                                    echo '<p class="card-text">Categoría: Sin categoría</p>';
                                }

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

                                echo '<div class="form-check">';
                                echo '<input class="form-check-input" type="checkbox" id="completada-' . htmlspecialchars($tarea['id_tarea'], ENT_QUOTES, 'UTF-8') . '" ' . (isset($tarea['completada']) && $tarea['completada'] ? 'checked' : '') . ' onclick="marcarCompletada(' . htmlspecialchars($tarea['id_tarea'], ENT_QUOTES, 'UTF-8') . ')">';
                                echo '<label class="form-check-label" for="completada-' . htmlspecialchars($tarea['id_tarea'], ENT_QUOTES, 'UTF-8') . '">Completada</label>';
                                echo '</div>';

                                echo '<div class="btn-group" role="group">';
                                echo '<button class="btn btn-secondary" onclick="editarTarea(' . htmlspecialchars($tarea['id_tarea'], ENT_QUOTES, 'UTF-8') . ')"><i class="fas fa-edit"></i> Editar</button>';
                                echo '<button class="btn btn-danger" onclick="eliminarTarea(' . htmlspecialchars($tarea['id_tarea'], ENT_QUOTES, 'UTF-8') . ')"><i class="fas fa-trash"></i> Eliminar</button>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';

                                if ($count % 2 != 0 || $count == count($tareas) - 1) {
                                    echo '</div>';
                                }

                                $count++;
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

    <footer class="text-center py-3 mt-4">
        <div class="container">
            &copy; <?php echo date('Y'); ?> | EXPLOSIVO
        </div>
    </footer>

    <script>
        $(function() {
            $("#tabs").tabs();
            $('[data-toggle="tooltip"]').tooltip();
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
                        var checkbox = $('#completada-' + id);
                        checkbox.prop('checked', !checkbox.prop('checked'));
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
