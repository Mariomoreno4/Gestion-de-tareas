<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_usuario']) && isset($_POST['usuario']) && isset($_POST['tipo'])) {
        $id_usuario = $_POST['id_usuario'];
        $usuario = $_POST['usuario'];
        $tipo = $_POST['tipo'];

        $query = "UPDATE logins SET usuario = ?, tipo = ? WHERE id_usuario = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sii', $usuario, $tipo, $id_usuario);

            if (mysqli_stmt_execute($stmt)) {
                echo 'success';
            } else {
                echo 'Error al ejecutar la consulta: ' . mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        } else {
            echo 'Error al preparar la consulta: ' . mysqli_error($conn);
        }

        mysqli_close($conn);
    } else {
        echo 'Error: Datos incompletos';
    }
} else {
    echo 'Método de solicitud no válido';
}
?>
