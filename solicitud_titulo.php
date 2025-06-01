<?php
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
$certificaciones = [];

// Paso 3: El sistema busca los datos por DNI
if (!empty($dni)) {
    // Buscar datos del egresado
    $stmt = $conn->prepare("SELECT e.*, ex.* FROM estudiantes e 
                           LEFT JOIN expedientes ex ON e.id = ex.estudiante_id 
                           WHERE e.numero_id = ?");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        // Paso 4: El sistema presenta en pantalla un registro del egresado
        $datos_egresado = $resultado->fetch_assoc();
        
        // Obtener certificaciones disponibles para solicitar
        $stmt_cert = $conn->prepare("SELECT id, nombre, descripcion FROM requisitos");
        $stmt_cert->execute();
        $result_cert = $stmt_cert->get_result();
        
        while ($cert = $result_cert->fetch_assoc()) {
            $certificaciones[] = $cert;
        }
        $stmt_cert->close();
    } else {
        // Alternativa 1.B: El sistema no encuentra el DNI
        $mensaje = "DNI no encontrado en el sistema.";
    }
    $stmt->close();
}

// Paso 5-6: El egresado selecciona aceptar y se genera la solicitud
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generar_solicitud'])) {
    // Verificar que el egresado esté habilitado para solicitar
    $habilitado = true;
    $razon_no_habilitado = "";
    
    // Verificar requisitos básicos (implementar lógica según tus reglas)
    if ($datos_egresado) {
        if (!isset($datos_egresado['creditos_totales']) || $datos_egresado['creditos_totales'] < 400) {
            $habilitado = false;
            $razon_no_habilitado .= "- No cumple con los créditos mínimos requeridos.<br>";
        }
        
        if (!isset($datos_egresado['promedio']) || $datos_egresado['promedio'] < 7.0) {
            $habilitado = false;
            $razon_no_habilitado .= "- No cumple con el promedio mínimo requerido.<br>";
        }
        
        if (!isset($datos_egresado['servicio_social']) || $datos_egresado['servicio_social'] != 1) {
            $habilitado = false;
            $razon_no_habilitado .= "- No ha completado el servicio social.<br>";
        }
        
        // Verificar adeudo bibliográfico (paso 13)
        if (!isset($datos_egresado['adeudos']) || $datos_egresado['adeudos'] == 1) {
            $habilitado = false;
            $razon_no_habilitado .= "- Tiene adeudos de material bibliográfico.<br>";
        }
    } else {
        $habilitado = false;
        $razon_no_habilitado = "No se encontraron datos del egresado.";
    }
    
    if ($habilitado) {
        // Generar identificador único para la solicitud
        $identificador = "SOL-".time()."-".rand(1000, 9999);
        $fecha_actual = date('Y-m-d H:i:s');
        $estado = "iniciada";
        
        // Insertar la solicitud en la base de datos
        $stmt = $conn->prepare("INSERT INTO solicitudes (estudiante_id, fecha_solicitud, estado, comentarios) 
                               VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $usuario_id, $fecha_actual, $estado, $identificador);
        
        if ($stmt->execute()) {
            $solicitud_id = $conn->insert_id;
            $mensaje = "Solicitud generada exitosamente. Identificador: $identificador";
            
            // Redireccionar a la siguiente pantalla
            $_SESSION['solicitud_id'] = $solicitud_id;
            $_SESSION['solicitud_identificador'] = $identificador;
            header("Location: expedicion_titulo.php");
            exit();
        } else {
            $mensaje = "Error al generar la solicitud: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $mensaje = "No es posible generar la solicitud:<br>" . $razon_no_habilitado;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Solicitud de Título</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Solicitud de Título</h1>
        
        <?php if (!empty($mensaje)): ?>
            <div class="mensaje <?php echo strpos($mensaje, 'Error') !== false || strpos($mensaje, 'No es posible') !== false ? 'error' : 'success'; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$datos_egresado): ?>
            <!-- Paso 2: Sistema presenta pantalla para ingresar DNI -->
            <form method="get" action="">
                <div class="form-group">
                    <label for="dni">Ingrese su DNI/Número de ID:</label>
                    <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($dni); ?>" required>
                </div>
                
                <button type="submit" class="btn">Buscar</button>
            </form>
        <?php else: ?>
            <!-- Paso 4: Mostrar datos del egresado -->
            <div class="datos-egresado">
                <h2>Datos del Egresado</h2>
                <table class="datos-tabla">
                    <tr>
                        <th>Nombre:</th>
                        <td><?php echo htmlspecialchars($datos_egresado['nombre'] . ' ' . $datos_egresado['apellidos']); ?></td>
                    </tr>
                    <tr>
                        <th>DNI/ID:</th>
                        <td><?php echo htmlspecialchars($datos_egresado['numero_id']); ?></td>
                    </tr>
                    <tr>
                        <th>Carrera:</th>
                        <td><?php echo htmlspecialchars($datos_egresado['carrera']); ?></td>
                    </tr>
                    <tr>
                        <th>Créditos:</th>
                        <td><?php echo isset($datos_egresado['creditos_totales']) ? $datos_egresado['creditos_totales'] : 'No registrado'; ?></td>
                    </tr>
                    <tr>
                        <th>Promedio:</th>
                        <td><?php echo isset($datos_egresado['promedio']) ? $datos_egresado['promedio'] : 'No registrado'; ?></td>
                    </tr>
                    <tr>
                        <th>Servicio Social:</th>
                        <td><?php echo isset($datos_egresado['servicio_social']) && $datos_egresado['servicio_social'] == 1 ? 'Completado' : 'Pendiente'; ?></td>
                    </tr>
                    <tr>
                        <th>Prácticas Profesionales:</th>
                        <td><?php echo isset($datos_egresado['practicas_profesionales']) && $datos_egresado['practicas_profesionales'] == 1 ? 'Completado' : 'Pendiente'; ?></td>
                    </tr>
                    <tr>
                        <th>Adeudos:</th>
                        <td><?php echo isset($datos_egresado['adeudos']) && $datos_egresado['adeudos'] == 0 ? 'Sin adeudos' : 'Con adeudos'; ?></td>
                    </tr>
                </table>
                
                <!-- Paso 5: El egresado selecciona aceptar -->
                <form method="post" action="">
                    <div class="form-group">
                        <p>Por favor, verifique que toda la información sea correcta antes de continuar.</p>
                    </div>
                    
                    <input type="hidden" name="dni" value="<?php echo htmlspecialchars($dni); ?>">
                    <button type="submit" name="generar_solicitud" class="btn">Generar Solicitud de Título</button>
                </form>
            </div>
        <?php endif; ?>
        
        <div class="back-link">
            <a href="dashboard.php">Volver al panel principal</a>
        </div>
    </div>
</body>
</html>