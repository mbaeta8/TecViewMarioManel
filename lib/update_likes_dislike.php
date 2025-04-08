<?php
include_once './controlDB.php';
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

// Recibir datos JSON desde el frontend (request POST)
$data = json_decode(file_get_contents("php://input"), true);

// Verificar que postID y isLike se están recibiendo correctamente
if (!isset($data['postID']) || !isset($data['isLike'])) {
    echo json_encode(['success' => false, 'message' => 'postID o isLike no recibido']);
    exit;
}

$postID = $data['postID'];
$isLike = $data['isLike'];

$userID = $_SESSION['userID'];
$conn = getDBConnection();

// Función para manejar el Like
function handleLike($conn, $postID, $userID) {
    // Verificar si ya existe el like
    $stmt = $conn->prepare("SELECT * FROM likes WHERE postID = :postID AND userID = :userID");
    $stmt->execute(['postID' => $postID, 'userID' => $userID]);
    $likeExists = $stmt->fetch();

    // Si ya existe el like, lo eliminamos (toggle like)
    if ($likeExists) {
        $stmt = $conn->prepare("DELETE FROM likes WHERE postID = :postID AND userID = :userID");
        $stmt->execute(['postID' => $postID, 'userID' => $userID]);
    } else {
        // Si existe un dislike, lo eliminamos antes de dar like
        $stmt = $conn->prepare("DELETE FROM dislikes WHERE postID = :postID AND userID = :userID");
        $stmt->execute(['postID' => $postID, 'userID' => $userID]);

        // Insertamos el nuevo like
        $stmt = $conn->prepare("INSERT INTO likes (postID, userID) VALUES (:postID, :userID)");
        $stmt->execute(['postID' => $postID, 'userID' => $userID]);
    }
}

// Función para manejar el Dislike
function handleDislike($conn, $postID, $userID) {
    // Verificar si ya existe el dislike
    $stmt = $conn->prepare("SELECT * FROM dislikes WHERE postID = :postID AND userID = :userID");
    $stmt->execute(['postID' => $postID, 'userID' => $userID]);
    $dislikeExists = $stmt->fetch();

    // Si ya existe el dislike, lo eliminamos (toggle dislike)
    if ($dislikeExists) {
        $stmt = $conn->prepare("DELETE FROM dislikes WHERE postID = :postID AND userID = :userID");
        $stmt->execute(['postID' => $postID, 'userID' => $userID]);
    } else {
        // Si existe un like, lo eliminamos antes de dar dislike
        $stmt = $conn->prepare("DELETE FROM likes WHERE postID = :postID AND userID = :userID");
        $stmt->execute(['postID' => $postID, 'userID' => $userID]);

        // Insertamos el nuevo dislike
        $stmt = $conn->prepare("INSERT INTO dislikes (postID, userID) VALUES (:postID, :userID)");
        $stmt->execute(['postID' => $postID, 'userID' => $userID]);
    }
}

// Lógica principal
try {
    $conn->beginTransaction(); // Comienza la transacción

    if ($isLike) {
        handleLike($conn, $postID, $userID);  // Llamamos a la función para manejar el Like
    } else {
        handleDislike($conn, $postID, $userID);  // Llamamos a la función para manejar el Dislike
    }

    $conn->commit();  // Confirmamos la transacción

    // Obtener los nuevos conteos de likes y dislikes
    $stmt = $conn->prepare("SELECT 
        (SELECT COUNT(*) FROM likes WHERE postID = :postID) AS likes,
        (SELECT COUNT(*) FROM dislikes WHERE postID = :postID) AS dislikes
    ");
    $stmt->execute(['postID' => $postID]);
    $counts = $stmt->fetch();

    // Enviar los resultados como respuesta JSON
    echo json_encode(['success' => true, 'likes' => $counts['likes'], 'dislikes' => $counts['dislikes']]);

} catch (Exception $e) {
    $conn->rollBack();  // Si ocurre un error, hacemos rollback
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
