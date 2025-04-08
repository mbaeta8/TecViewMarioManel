<?php
session_start(); // Iniciar sesión

header('Content-Type: application/json'); // Asegura que la respuesta será en formato JSON

// Incluir la conexión a la base de datos
require_once  './controlDB.php';

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$userID = $_SESSION['userID']; // Obtener ID de usuario de la sesión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents("debug.log", print_r($_FILES, true), FILE_APPEND);
    file_put_contents("debug.log", print_r($_POST, true), FILE_APPEND);

    // Capturar los datos enviados
    $description = $_POST['description'] ?? '';
    $mediaType = $_POST['mediaType'] ?? '';
    $image = $_FILES['image'] ?? null;
    $video = $_FILES['video'] ?? null;
    $gifUrl = $_POST['gifUrl'] ?? null;

    // Validaciones
    if (empty($description)) {
        echo json_encode(['success' => false, 'message' => 'La descripción no puede estar vacía']);
        exit;
    }

    if (empty($mediaType)) {
        echo json_encode(['success' => false, 'message' => 'Debe seleccionar un tipo de medio']);
        exit;
    }

    // Solo permite cargar un tipo de medio (imagen, video o gif)
    if ($mediaType == 'image' && !$image) {
        echo json_encode(['success' => false, 'message' => 'Debe cargar una imagen']);
        exit;
    }

    if ($mediaType == 'video' && !$video) {
        echo json_encode(['success' => false, 'message' => 'Debe cargar un video']);
        exit;
    }

    if ($mediaType == 'gif' && empty($gifUrl)) {
        echo json_encode(['success' => false, 'message' => 'Debe ingresar una URL de GIF']);
        exit;
    }

    // Crear la publicación en la base de datos
    $success = crearPublicacion($userID, $description, $mediaType, $image, $video, $gifUrl);

    // Responder al cliente
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Publicación creada exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al crear la publicación']);
    }

    exit;
}

/* Función para crear una publicación en la base de datos */
function crearPublicacion($userID, $description, $mediaType, $image = null, $video = null, $gifUrl = null) {
    $conn = getDBConnection();

    $sql = "INSERT INTO posts (userID, content, image, gif_url, video, media_type) 
            VALUES (:userID, :description, :image, :gifUrl, :video, :mediaType)";

    try {
        $stmt = $conn->prepare($sql);

        // Manejo de imagen
        $imageData = null;
        if ($mediaType == 'image' && $image && file_exists($image['tmp_name'])) {
            $imageData = base64_encode(file_get_contents($image['tmp_name']));
        }

        // Manejo de video
        $videoData = null;

        if ($mediaType == 'video') {
            if ($video && is_uploaded_file($video['tmp_name'])) {
                $videoData = base64_encode(file_get_contents($video['tmp_name']));
                error_log("Video procesado correctamente");
            } else {
                error_log("No se pudo procesar el video");
                echo json_encode(['success' => false, 'message' => 'No se pudo procesar el archivo de video']);
                return false;
            }
        }


        // Ejecución de la consulta de inserción
        $stmt->execute([
            ':userID' => $userID,
            ':description' => $description,
            ':image' => $imageData,
            ':gifUrl' => $gifUrl ?: null, // Guardar null si no hay URL
            ':video' => $videoData,
            ':mediaType' => $mediaType
        ]);

        return true;
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
        return false;
    }
}