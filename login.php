<?php
session_start(); // Iniciar la sesión

$servername = "localhost";
$username = "root";
$password = "";
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
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

    // Consulta preparada para verificar las credenciales
    $sql = "SELECT id_usuario FROM logins WHERE Usuario = ? AND Contasena = ?";
    
    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario, $contrasena);

    // Ejecutar la consulta
    $stmt->execute();
    $stmt->store_result();

    // Verificar si se encontró el usuario
    if ($stmt->num_rows > 0) {
        // Obtener el ID de usuario
        $stmt->bind_result($id_usuario);
        $stmt->fetch();

        // Guardar el ID de usuario en la sesión
        $_SESSION['id'] = $id_usuario;
        $_SESSION['usuario'] = $usuario; // Opcional, guardar también el nombre de usuario

        // Redireccionar a la página de tareas
        header("Location: tarea.php");
        exit();
    } else {
        // Usuario o contraseña inválidos
        echo "Usuario o contraseña incorrectos.";
    }

    // Cerrar la consulta preparada
    $stmt->close();
}

// Cerrar conexión
$conn->close();
?>