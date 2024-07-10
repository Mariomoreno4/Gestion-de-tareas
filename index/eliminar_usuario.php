<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_usuario'])) {
        $id_usuario = $_POST['id_usuario'];

        // Iniciar una transacción
        mysqli_begin_transaction($conn);

        try {
            // Eliminar las filas dependientes de la tabla 'tarea'
            $query_tarea = "DELETE FROM tarea WHERE id_usuario = ?";
            $stmt_tarea = mysqli_prepare($conn, $query_tarea);
            mysqli_stmt_bind_param($stmt_tarea, 'i', $id_usuario);
            mysqli_stmt_execute($stmt_tarea);
            mysqli_stmt_close($stmt_tarea);

            // Eliminar el usuario de la tabla 'logins'
            $query_usuario = "DELETE FROM logins WHERE id_usuario = ?";
            $stmt_usuario = mysqli_prepare($conn, $query_usuario);
            mysqli_stmt_bind_param($stmt_usuario, 'i', $id_usuario);
            mysqli_stmt_execute($stmt_usuario);
            mysqli_stmt_close($stmt_usuario);

            // Confirmar la transacción
            mysqli_commit($conn);

            echo 'success';
        } catch (mysqli_sql_exception $exception) {
            mysqli_rollback($conn);
            echo 'Error al eliminar el usuario: ' . $exception->getMessage();
        }

        mysqli_close($conn);
    } else {
        echo 'Error: ID de usuario no recibido';
    }
} else {
    echo 'Método de solicitud no válido';
}
?>
