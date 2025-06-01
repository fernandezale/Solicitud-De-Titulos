<?php
// Configuración de conexión a la base de datos
$servername = "localhost";
$username = "root";  // Cambia esto por tu usuario de MySQL si es diferente
$password = "admin";      // Cambia esto por tu contraseña de MySQL
$dbname = "sistema_titulos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Establecer caracteres UTF-8
$conn->set_charset("utf8");
?>