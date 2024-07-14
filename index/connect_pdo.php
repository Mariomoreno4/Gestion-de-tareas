<?php
$servername = "db"; // Usar el nombre del servicio de Docker
$username = "root"; // Nombre de usuario configurado en el docker-compose
$password = "password"; // Contraseña configurada en el docker-compose
$dbname = "mysql"; // Nombre de la base de datos

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Establecer el modo de error de PDO a excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa";
} catch(PDOException $e) {
    echo "Conexión fallida: " . $e->getMessage();
}
?>