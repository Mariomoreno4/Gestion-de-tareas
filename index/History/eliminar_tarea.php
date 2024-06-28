<?php
require_once 'conexion.php'; // Incluye el archivo de conexión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_tarea = $_POST['id_tarea'];

    // Consulta SQL para eliminar la tarea
    $query_eliminar = "DELETE FROM tarea WHERE id_tarea = ?";
    $stmt_eliminar = mysqli_prepare($conn, $query_eliminar);
    mysqli_stmt_bind_param($stmt_eliminar, 'i', $id_tarea);

    if (mysqli_stmt_execute($stmt_eliminar)) {
        echo 'success';
    } else {
        echo 'error';
    }

    mysqli_stmt_close($stmt_eliminar);
    mysqli_close($conn);
}
?>