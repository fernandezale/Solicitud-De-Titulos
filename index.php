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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            text-align: center;
            padding: 50px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #0078D7;
        }
        p {
            font-size: 1.2em;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #0078D7;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background-color: #005bb5;
        }
        b {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color:#00bed7;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        b:hover {
            background-color:rgb(0, 166, 188);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Solicitud de Titulos en Apache</h1>
        <p>Esta es la página de inicio de sesion.</p>
        <h1>Iniciar Sesión</h1>
        
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