// Insertar un nuevo estudiante
$numero_id = "20230001";
$password_hash = password_hash("contraseña123", PASSWORD_DEFAULT); // Siempre encripta las contraseñas
$nombre = "Juan";
$apellidos = "Pérez López";
$carrera = "Ingeniería en Sistemas";
$email = "juan.perez@example.com";

$sql = "INSERT INTO estudiantes (numero_id, password, nombre, apellidos, carrera, email) 
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $numero_id, $password_hash, $nombre, $apellidos, $carrera, $email);

if ($stmt->execute()) {
    echo "Estudiante registrado correctamente";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();