<?php
// filepath: c:\www\Apache24\htdocs\proyecto_titulos\perfil.php
session_start();
require_once('config/db.php');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$dni = isset($_SESSION['numero_id']) ? $_SESSION['numero_id'] : '';
$mensaje = '';
$datos_egresado = null;

// Buscar todos los datos del egresado
if (!empty($dni)) {
    $stmt = $conn->prepare("SELECT e.*, ex.* FROM estudiantes e 
                            LEFT JOIN expedientes ex ON e.id = ex.estudiante_id 
                            WHERE e.numero_id = ?");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $datos_egresado = $resultado->fetch_assoc();
    } else {
        $mensaje = "No se encontraron datos del alumno.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Perfil del Alumno</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Perfil del Alumno</h1>
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje error"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <?php if ($datos_egresado): ?>
            <table class="datos-tabla">
                <?php foreach ($datos_egresado as $campo => $valor): ?>
                    <?php if (strtolower($campo) === 'id') continue; // Oculta el campo ID ?>
                    <?php if (strtolower($campo) === 'password') continue; // Oculta el campo password ?>
                    <?php if (strtolower($campo) === 'estudiante_id') continue; // Oculta el campo estudiante_id ?>
                    <tr>
                        <th><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $campo))); ?>:</th>
                        <td><?php echo htmlspecialchars($valor); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <div class="back-link">
            <a href="dashboard.php">Volver al panel principal</a>
        </div>
    </div>
</body>
</html>