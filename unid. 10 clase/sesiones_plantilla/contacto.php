<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <header>
    <?php
        require_once "header.php";
    ?>
    </header>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="nosotros.php">Nosotros</a></li>
            <li><a href="servicios.php">Servicios</a></li>
            <li><a href="contacto.php">Contacto</a></li>
        </ul>
    </nav>
    <main>
        <h2>Contacto</h2>
        <p>¿Tienes preguntas? Rellena el formulario y nos pondremos en contacto contigo:</p>
        <form>
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required><br><br>

            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="mensaje">Mensaje:</label><br>
            <textarea id="mensaje" name="mensaje" rows="4" required></textarea><br><br>

            <button type="submit">Enviar</button>
        </form>
    </main>
    <footer>
    <?php
        require_once "footer.php";
    ?>
    </footer>
</body>
</html>
