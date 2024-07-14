<?php
session_start(); // Iniciar la sesión

$servername = "localhost"; // Usar el nombre del servicio de Docker
$username = "root"; // Nombre de usuario configurado en el docker-compose
$password = ""; // Contraseña configurada en el docker-compose
$dbname = "mysql"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se enviaron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario y evitar inyección SQL
    $usuario = isset($_POST['usuario']) ? $conn->real_escape_string($_POST['usuario']) : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

    // Consulta preparada para verificar el usuario
    $sql = "SELECT id_usuario, Contasena FROM logins WHERE Usuario = ?";
    
    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);

        // Ejecutar la consulta
        $stmt->execute();
        $stmt->store_result();

    // Verificar si se encontró el usuario
    if ($stmt->num_rows > 0) {
        // Obtener el ID de usuario y la contraseña encriptada
        $stmt->bind_result($id_usuario, $hashed_password);
        $stmt->fetch();

        // Verificar la contraseña
        if (password_verify($contrasena, $hashed_password)) {
            // Guardar el ID de usuario en la sesión
            $_SESSION['id'] = $id_usuario;
            $_SESSION['usuario'] = $usuario; // Opcional, guardar también el nombre de usuario

            // Redireccionar a la página de tareas
            header("Location: index/tarea.php");
            exit();
        } else {
            // Contraseña incorrecta
            echo "Usuario o contraseña incorrectos.";
        }
    } else {
        // Usuario no encontrado
        echo "Usuario o contraseña incorrectos.";
    }
}

// Cerrar conexión
$conn->close();
?>
