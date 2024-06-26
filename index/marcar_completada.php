<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_tarea'])) {
    $id_tarea = $_POST['id_tarea'];

    // Consulta para marcar la tarea como completada (invierte el estado actual)
    $query_marcar = "UPDATE tarea SET completada = NOT completada WHERE id_tarea = ?";
    $stmt_marcar = mysqli_prepare($conn, $query_marcar);
    mysqli_stmt_bind_param($stmt_marcar, "i", $id_tarea);

    if (mysqli_stmt_execute($stmt_marcar)) {
        echo 'success';
    } else {
        echo 'error';
    }

    mysqli_stmt_close($stmt_marcar);
    mysqli_close($conn);
}
?>