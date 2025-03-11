<?php
session_start();
require '../lib/controlDB.php';

if (!isset($_SESSION['user'])) {
    echo "No autorizado";
    exit();
}

$conn = getDBConnection();
$user = $_SESSION['user'];

// Obtener el ID del usuario
$queryUser = "SELECT iduser FROM users WHERE username = :user";
$stmtUser = $conn->prepare($queryUser);
$stmtUser->bindParam(':user', $user);
$stmtUser->execute();
$rowUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$rowUser) {
    echo "Usuario no encontrado";
    exit();
}

$userID = $rowUser['iduser'];

// Verificar si hay contenido
$content = isset($_POST['content']) ? trim($_POST['content']) : "";

// Procesar imagen o video
$imageData = null;
$mediaURL = null;

if (!empty($_FILES['image']['tmp_name'])) {
    $fileType = mime_content_type($_FILES['image']['tmp_name']);
    
    if (in_array($fileType, ["image/jpeg", "image/png", "image/gif"])) {
        // Convertir imagen a base64
        $imageData = base64_encode(file_get_contents($_FILES['image']['tmp_name']));
    } elseif ($fileType === "video/mp4") {
        // Guardar video en el servidor
        $uploadDir = "uploads/videos/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $videoName = uniqid("vid_") . ".mp4";
        $videoPath = $uploadDir . $videoName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $videoPath)) {
            $mediaURL = $videoPath; // Guardamos la URL en la BD
        }
    }
}

// Insertar en la base de datos
$queryInsert = "INSERT INTO posts (userID, content, image, video) VALUES (:userID, :content, :image, :video)";
$stmtInsert = $conn->prepare($queryInsert);
$stmtInsert->bindParam(':userID', $userID);
$stmtInsert->bindParam(':content', $content);
$stmtInsert->bindParam(':image', $imageData);
$stmtInsert->bindParam(':video', $mediaURL);
$stmtInsert->execute();

echo "Publicación creada con éxito";
?>
