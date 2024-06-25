<?php
require_once 'conexion.php'; // Incluir el archivo de conexión

// Verificar si se enviaron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Iniciar la sesión
    session_start();

    // Obtener la conexión desde el archivo de conexión
    global $conn; // Asegurarse de que $conn está disponible globalmente

    // Sanitizar y escapar la nueva tarea (no es necesario con consultas preparadas)
    $nueva_tarea = isset($_POST['nueva_tarea']) ? $_POST['nueva_tarea'] : '';

    // Verificar que la tarea no esté vacía
    if (!empty($nueva_tarea)) {
        // Insertar la nueva tarea en la base de datos
        $query = "INSERT INTO tarea (id_usuario, tarea, fecha) VALUES (?, ?, NOW())";

        // Preparar la consulta
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            // Obtener el ID de usuario desde la sesión
            $id_usuario = isset($_SESSION['id']) ? $_SESSION['id'] : null;

            if ($id_usuario !== null) { // Verificar que el id_usuario no sea null
                // Vincular los parámetros y ejecutar la consulta
                mysqli_stmt_bind_param($stmt, "is", $id_usuario, $nueva_tarea);
                mysqli_stmt_execute($stmt);

                // Verificar si se insertó correctamente
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    echo "Tarea agregada correctamente.";
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
        echo "Por favor, completa el campo de nueva tarea.";
    }

    // Cerrar la conexión (opcional, dependiendo de la lógica de tu aplicación)
    mysqli_close($conn);
} else {
    echo "Acceso no válido.";
}
?>