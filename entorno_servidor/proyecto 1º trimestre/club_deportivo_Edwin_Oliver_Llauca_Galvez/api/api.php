<?php
//PRUEBA
// Encabezados para permitir CORS
header("Access-Control-Allow-Origin: *"); // Permite todas las solicitudes de origen
header("Content-Type: application/json");

//configuracion y funciones
require "config.php";
require_once "funciones.php";
require_once "../config/funciones.php"; // aqui traigo la funcion conectar q ya tengo

try {
    $conn = conectar($nombre_host, $nombre_usuario, $password_db, $nombre_db);
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al conectar con la base de datos"]);
    die();
}

$API_KEYS = [
    "gR0WVlcNgkLLcveUs45txlxxbfiPKSwun2VIGmE9gXNiEaqAtgAhdOuo2ehy1xyR",
    "Q7Oti0xIa6XV3dzkAyNr7VLnjK2KbuiDQurbmH4b0PSvprXfeMLLVXDcTiVPWzbN",
];

// Determinar el método HTTP
$metodo = $_SERVER['REQUEST_METHOD'];

//uso esto solo para el put ya que esto no admite el envio de archivos y necesito poder pasar
//datos de tipo archivo para que el post pueda admitir la subida de imagenes
if($metodo=="PUT" || $metodo=="DELETE"){
    $entrada=json_decode(file_get_contents("php://input"),true);
}

if (in_array($metodo, ['POST', 'PUT', 'DELETE'])) {
    validarApiKey($API_KEYS);
}

switch ($metodo) {
    case 'GET':
        if(isset($_GET['id'])){
            $resultado = [];
            $resultado = obtenerProducto($conn, $_GET['id']);
            http_response_code($resultado["http"]);
            echo json_encode($resultado["respuesta"]);
        }else{
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 10;

            //parametros de busqueda
            $nombre = isset($_GET['nombre']) ? $_GET['nombre'] : null;
            $precioMin = isset($_GET['precio_min']) ? (float)$_GET['precio_min'] : null;
            $precioMax = isset($_GET['precio_max']) ? (float)$_GET['precio_max'] : null;
            $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null; 

            //Validaciones de parametros
            if ($page < 1 || $limit < 1) {
                http_response_code(400); // Bad Request
                echo json_encode([
                    "error" => "Parámetros de paginación inválidos, el numero de pagina y el limite no pueden ser menor que 1"
                ]);
                die();
            }
            if($precioMin !== null && $precioMax !== null && $precioMin > $precioMax){
                http_response_code(400);
                echo json_encode(["error" => "precio_min no puede ser mayor que precio_max"]);
                die();
            }

            $resultado = obtenerProductosPag(
                $conn, 
                $page, 
                $limit, 
                $nombre, 
                $precioMin, 
                $precioMax, 
                $categoria
            );
            http_response_code($resultado["http"]);
            echo json_encode($resultado["respuesta"]);

        }
        

        break;
    
    case 'POST':  
        // Cambiamos a multipart/form-data para manejar archivos
        $datosProducto = $_POST;
        $imagen = $_FILES['imagen'] ?? null;

        // Validar campos obligatorios
        if (empty($datosProducto['nombre']) || !isset($datosProducto['precio']) || !isset($datosProducto['stock'])) {
            http_response_code(400);
            echo json_encode(["error" => "Campos obligatorios faltantes: nombre, precio, stock"]);
            die();
        }

        // Validar tipos de datos
        if (!is_numeric($datosProducto['precio']) || !is_numeric($datosProducto['stock'])) {
            http_response_code(400);
            echo json_encode(["error" => "Precio y stock deben ser valores numéricos"]);
            die();
        }

        // Procesar la imagen si existe
        $rutaImagen = null;
        if ($imagen && $imagen['error'] === UPLOAD_ERR_OK) {
            // Validar tipo de imagen
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
            $tipoMime = mime_content_type($imagen['tmp_name']);
            
            if (!in_array($tipoMime, $tiposPermitidos)) {
                http_response_code(415);
                echo json_encode(["error" => "Formato de imagen no permitido. Use JPEG, PNG o WEBP"]);
                die();
            }

            // Validar tamaño (ej: máximo 5MB)
            if ($imagen['size'] > 5 * 1024 * 1024) {
                http_response_code(413);
                echo json_encode(["error" => "La imagen supera el tamaño máximo de 5MB"]);
                die();
            }
        }

        // Crear el producto (manejamos la imagen en la función)
        $resultado = crearProducto(
            $conn,
            $datosProducto,
            $imagen
        );

        http_response_code($resultado["http"]);
        echo json_encode($resultado["respuesta"]);
        
        break;

    case 'PUT':
        if (!isset($entrada['id']) || !is_numeric($entrada['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Se requiere un ID de producto válido"]);
            die();
        }

        // Definir los campos permitidos para actualizar
        $camposPermitidos = ['nombre', 'descripcion', 'precio', 'stock', 'categoria'];
        
        $camposActualizar = obtenerCamposActualizar($entrada, $camposPermitidos);
        
        if (empty($camposActualizar)) {
            http_response_code(400);
            echo json_encode(["error" => "Se requiere al menos un campo para actualizar"]);
            die();
        }
        
        // Llamar a la función de actualización del producto
        $resultado = actualizarProducto(
            $conn,
            $entrada['id'],
            $camposActualizar
        );
        
        http_response_code($resultado["http"]);
        echo json_encode($resultado["respuesta"]);
        break;

    case 'DELETE':
        // Validar que se envíe el campo id y que sea numérico
        if (!isset($entrada['id']) || !is_numeric($entrada['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Se requiere un ID de producto válido"]);
            die();
        }

        // Llamar a la función para eliminar el producto
        $resultado = eliminarProducto($conn, $entrada['id']);

        http_response_code($resultado["http"]);
        echo json_encode($resultado["respuesta"]);
        break;


    default:
        http_response_code(405);
        echo json_encode(["error"=>"Método no soportado :'D"]);
        
}/*

            
            */


// Cerrar la conexión
$conn->close();


?>