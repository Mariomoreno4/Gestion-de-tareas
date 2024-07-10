<?php
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

// Función para validar la contraseña
function validarContrasena($contrasena) {
    $longitud = strlen($contrasena) >= 8;
    $mayuscula = preg_match('@[A-Z]@', $contrasena);
    $minuscula = preg_match('@[a-z]@', $contrasena);
    $numero = preg_match('@[0-9]@', $contrasena);
    $caracter_especial = preg_match('@[^\w]@', $contrasena);

    return $longitud && $mayuscula && $minuscula && $numero && $caracter_especial;
}

// Verificar si se enviaron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario y evitar inyección SQL
    $usuario = isset($_POST['usuario']) ? $conn->real_escape_string($_POST['usuario']) : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
    $confirmar_contrasena = isset($_POST['confirmar_contrasena']) ? $_POST['confirmar_contrasena'] : '';
    $tipo = isset($_POST['tipouser']) ? $_POST['tipouser'] : '';

    // Verificar que los campos no estén vacíos y que las contraseñas coincidan
    if (!empty($usuario) && !empty($contrasena) && !empty($confirmar_contrasena)&& !empty($tipo)) {
        if ($contrasena === $confirmar_contrasena) {
            if (validarContrasena($contrasena)) {
                // Verificar si el usuario ya existe usando prepared statements
                $stmt = $conn->prepare("SELECT * FROM logins WHERE Usuario = ?");
                $stmt->bind_param("s", $usuario);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // El usuario ya existe
                    echo "El nombre de usuario ya está en uso. Por favor, elige otro.";
                } else {
                    // Encriptar la contraseña
                    $hashed_password = password_hash($contrasena, PASSWORD_BCRYPT);

                    // Insertar el nuevo usuario en la base de datos usando prepared statements
                    $stmt = $conn->prepare("INSERT INTO logins (Usuario, Contasena,tipo) VALUES (?, ?,?)");
                    $stmt->bind_param("sss", $usuario, $hashed_password,$tipo);

                    if ($stmt->execute()) {
                        header("Location: index.php");
                        exit();
                    } else {
                        echo "Error: " . $stmt->error;
                    }
                }
            } else {
                echo "La contraseña no cumple con los requisitos de seguridad.";
            }
        } else {
            echo "Las contraseñas no coinciden.";
        }
    } else {
        echo "Por favor, completa todos los campos.";
    }
}

// Cerrar conexión
$conn->close();
?>
