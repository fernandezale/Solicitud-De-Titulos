<?php
// Puedes agregar lógica PHP aquí si es necesario
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
        <p>Esta es la página de prueba para ver las funciones de este sistema.</p>
        <a href="registro_alumno.php">Registro de alumno</a>
        <a href="solicitud_titulo.php">Solicitud de título</a>
        <a href="index.php">Volver a la página principal</a>
        <b href="dashboard.php">DASHBOARD</b>
        
    </div>
</body>
</html>