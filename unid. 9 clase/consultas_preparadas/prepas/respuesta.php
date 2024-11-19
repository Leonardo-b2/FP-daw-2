<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        require_once "config.php";
        require_once "funciones.php";

        $conexion = conectar($nombre_host, $nombre_usuario, $password_db, $nombre_db);
    
        $nombre=$_POST["nombre"];
        $edad=$_POST['edad'];

        $sentencia = "SELECT * 
                        FROM alumnos 
                        WHERE nombre_completo LIKE ?
                            AND edad>?";
        $consulta=$conexion->prepare($sentencia);

        $nombre_like = "%$nombre%";
        $consulta->bind_param('si', $nombre_like, $edad);
        $consulta->bind_result($dni_alumno, $nombre_alumno, $edad_alumno);

        $consulta->execute();

        while($consulta->fetch()){
            echo "<p>$dni_alumno - $nombre_alumno - $edad_alumno años</p>";
        }

        $consulta->close();
        $conexion->close();
    ?>
</body>
</html>