<?php
require_once 'conexion.php'; // Incluye el archivo de conexión

session_start(); // Iniciar la sesión

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestión de tus tareas</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <header id="head">
        <div class="container">
            Gestión de tus tareas
        </div>
    </header>

    <div class="container main-content">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
        <a href="logout.php">Cerrar sesión</a>

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

            echo "<p>ID de usuario: " . htmlspecialchars($id_usuario) . "</p>";
        }
        ?>

        <section>
            <h2>¿Cuál es tu próxima tarea?</h2>
            <form method="post" action="agregar_tarea.php"> <!-- Ajusta según tu archivo de manejo de agregar tarea -->
                <textarea name="nueva_tarea" placeholder="Escribe aquí tu nueva tarea"></textarea>
                <button type="submit" class="btn-edit">Agregar tarea</button>
            </form>
        </section>

        <section class="task-list">
            <h2>Tus tareas</h2>

            <?php
            // Obtener las tareas del usuario actual
            if (isset($id_usuario)) {
                // Consulta SQL para obtener las tareas del usuario actual
                $query_tareas = "SELECT id_tarea, tarea, fecha FROM tarea WHERE id_usuario = ? ORDER BY fecha DESC";
                $stmt_tareas = mysqli_prepare($conn, $query_tareas);
                mysqli_stmt_bind_param($stmt_tareas, "i", $id_usuario);
                mysqli_stmt_execute($stmt_tareas);
                $resultado_tareas = mysqli_stmt_get_result($stmt_tareas);

                if ($resultado_tareas) {
                    while ($fila = mysqli_fetch_assoc($resultado_tareas)) {
                        echo '<article class="task-item">';
                        echo '<h3>' . htmlspecialchars($fila['tarea']) . '</h3>';
                        echo '<p>Descripción de la tarea...</p>'; // Puedes agregar una descripción si la tienes en tu tabla
                        echo '<div class="btn-group">';
                        echo '<button class="btn-edit">Editar</button>';
                        echo '<button class="btn-delete">Eliminar</button>';
                        echo '</div>';
                        echo '</article>';
                    }
                    mysqli_free_result($resultado_tareas);
                } else {
                    echo "Error en la consulta de tareas: " . mysqli_error($conn);
                }

                mysqli_stmt_close($stmt_tareas);
            } else {
                echo "Error: No se pudo obtener el ID de usuario.";
            }

            mysqli_close($conn);
            ?>
        </section>
    </div>

    <footer>
        <div class="container">
            &copy; <?php echo date('Y'); ?> | EXPLOSIVO
        </div>
    </footer>
</body>
</html>