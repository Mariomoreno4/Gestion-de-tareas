<?php
$servername = "db"; // Usar el nombre del servicio de Docker
$username = "root"; // Nombre de usuario configurado en el docker-compose
$password = "password"; // Contraseña configurada en el docker-compose
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
    $contrasena = isset($_POST['contrasena']) ? $conn->real_escape_string($_POST['contrasena']) : '';

    // Verificar que los campos no estén vacíos
    if (!empty($usuario) && !empty($contrasena)) {
        // Verificar si el usuario ya existe
        $sql = "SELECT * FROM logins WHERE Usuario = '$usuario'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // El usuario ya existe
            echo "El nombre de usuario ya está en uso. Por favor, elige otro.";
        } else {
            // Insertar el nuevo usuario en la base de datos
            $sql = "INSERT INTO logins (Usuario, Contasena) VALUES ('$usuario', '$contrasena')";
            if ($conn->query($sql) === TRUE) {
                header("Location: index.php");
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    } else {
        echo "Por favor, completa todos los campos.";
    }
}

// Cerrar conexión
$conn->close();
?>