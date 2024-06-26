<?php
require_once 'conexion.php'; // Incluir el archivo de conexión

// Verificar si se enviaron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Iniciar la sesión
    session_start();

    // Obtener la conexión desde el archivo de conexión
    global $conn; // Asegurarse de que $conn está disponible globalmente

    // Obtener y sanitizar los datos del formulario
    $nueva_tarea = isset($_POST['nueva_tarea']) ? $_POST['nueva_tarea'] : '';
    $fecha_tarea = isset($_POST['fecha_tarea']) ? $_POST['fecha_tarea'] : '';
    $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
    $importancia = isset($_POST['importancia']) ? $_POST['importancia'] : '';

    // Verificar que los campos no estén vacíos
    if (!empty($nueva_tarea) && !empty($fecha_tarea) && !empty($categoria) && !empty($importancia)) {
        // Insertar la nueva tarea en la base de datos
        $query = "INSERT INTO tarea (id_usuario, tarea, fecha, categoria, importancia) VALUES (?, ?, ?, ?, ?)";

        // Preparar la consulta
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            // Obtener el ID de usuario desde la sesión
            $id_usuario = isset($_SESSION['id']) ? $_SESSION['id'] : null;

            if ($id_usuario !== null) { // Verificar que el id_usuario no sea null
                // Vincular los parámetros y ejecutar la consulta
                mysqli_stmt_bind_param($stmt, "issss", $id_usuario, $nueva_tarea, $fecha_tarea, $categoria, $importancia);
                mysqli_stmt_execute($stmt);

                // Verificar si se insertó correctamente
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    // Redirigir a tarea.php
                    header("Location: tarea.php");
                    exit();
                } else {
                    echo "Error al agregar la tarea: " . mysqli_stmt_error($stmt);
                }
            } else {
                echo "No se pudo obtener el ID de usuario desde la sesión.";
            }

            // Cerrar la consulta preparada
            mysqli_stmt_close($stmt);
        } else {
            echo "Error en la preparación de la consulta: " . mysqli_error($conn);
        }
    } else {
        echo "Por favor, completa todos los campos del formulario.";
    }

    // Cerrar la conexión (opcional, dependiendo de la lógica de tu aplicación)
    mysqli_close($conn);
} else {
    echo "Acceso no válido.";
}
?>
