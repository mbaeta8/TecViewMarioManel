<?php
include_once './controlDB.php';
session_start();

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$postID = $data['postID'];
$isLike = $data['isLike']; // true = like, false = dislike
$userID = $_SESSION['userID'];

$conn = getDBConnection();

try {
    $conn->beginTransaction();

    if ($isLike) {
        // Verificar si ya hay un like
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
    } else {
        // Verificar si ya hay un dislike
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

    $conn->commit();

    // Obtener el nuevo conteo de likes y dislikes
    $stmt = $conn->prepare("SELECT 
        (SELECT COUNT(*) FROM likes WHERE postID = :postID) AS likes,
        (SELECT COUNT(*) FROM dislikes WHERE postID = :postID) AS dislikes
    ");
    $stmt->execute(['postID' => $postID]);
    $counts = $stmt->fetch();

    echo json_encode(['success' => true, 'likes' => $counts['likes'], 'dislikes' => $counts['dislikes']]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
