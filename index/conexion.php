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



?>