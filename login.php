<?php
session_start();
require_once('config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $numero_id = $_POST['numero_id'];
    $password = $_POST['password'];
    
    // Consulta preparada para evitar inyecciones SQL
    $stmt = $conn->prepare("SELECT id, numero_id, password, nombre FROM estudiantes WHERE numero_id = ?");
    $stmt->bind_param("s", $numero_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $usuario = $result->fetch_assoc();
        
        // Verificar contraseña (asumiendo que está hasheada en la BD)
        if (password_verify($password, $usuario['password']) || $password === $usuario['password']) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['numero_id'] = $usuario['numero_id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        $error = "Usuario no encontrado";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Iniciar Sesión - Sistema de Títulos</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="numero_id">Número de Identificación:</label>
                <input type="text" id="numero_id" name="numero_id" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>