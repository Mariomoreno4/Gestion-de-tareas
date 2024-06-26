<?php
require_once 'conexion.php'; // Incluye el archivo de conexión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_tarea = $_POST['id_tarea'];
    $nueva_tarea = $_POST['nueva_tarea'];

    // Consulta SQL para actualizar la tarea
    $query_editar = "UPDATE tarea SET tarea = ? WHERE id_tarea = ?";
    $stmt_editar = mysqli_prepare($conn, $query_editar);
    mysqli_stmt_bind_param($stmt_editar, 'si', $nueva_tarea, $id_tarea);

    if (mysqli_stmt_execute($stmt_editar)) {
        echo 'success';
    } else {
        echo 'error';
    }

    mysqli_stmt_close($stmt_editar);
    mysqli_close($conn);
}
?>
