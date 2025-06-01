<?php
require_once('config/db.php');

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Procesar el formulario cuando se envía
    $numero_id = $_POST['numero_id'];
    $password = $_POST['password'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $carrera = $_POST['carrera'];
    $email = $_POST['email'];
    
    // Validación simple
    if (empty($numero_id) || empty($password) || empty($nombre) || empty($apellidos)) {
        $mensaje = "Por favor, completa todos los campos obligatorios.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO estudiantes (numero_id, password, nombre, apellidos, carrera, email) 
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $numero_id, $password_hash, $nombre, $apellidos, $carrera, $email);
        
        if ($stmt->execute()) {
            $mensaje = "Estudiante registrado correctamente.";
        } else {
            $mensaje = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro de Estudiante</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Registro de Estudiante</h1>
        
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="numero_id">Número de ID *</label>
                <input type="text" id="numero_id" name="numero_id" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña *</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="nombre">Nombre *</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="apellidos">Apellidos *</label>
                <input type="text" id="apellidos" name="apellidos" required>
            </div>
            
            <div class="form-group">
                <label for="carrera">Carrera</label>
                <input type="text" id="carrera" name="carrera">
            </div>
            
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email">
            </div>
            
            <button type="submit" class="btn">Registrar</button>
        </form>
        
        <div class="links">
            <a href="index.php">Volver a la página principal</a>
        </div>
    </div>
</body>
</html>