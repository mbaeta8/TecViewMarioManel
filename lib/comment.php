<?php
session_start();
require './controlDB.php';

$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Guardar comentario
    $postID = $_POST['postID'];
    $comment = trim($_POST['comment']);
    $user = $_SESSION['user'];

    // Obtener ID del usuario
    $stmtUser = $conn->prepare("SELECT iduser FROM users WHERE username = :username");
    $stmtUser->bindParam(':username', $user);
    $stmtUser->execute();
    $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);
    $userID = $userRow['iduser'];

    $stmt = $conn->prepare("INSERT INTO comments (userID, postID, comment) VALUES (:userID, :postID, :comment)");
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':postID', $postID);
    $stmt->bindParam(':comment', $comment);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'username' => $user,
            'comment' => htmlspecialchars($comment)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar el comentario']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $postID = $_GET['postID'];

    $stmt = $conn->prepare("SELECT comments.comment, users.username 
                            FROM comments 
                            JOIN users ON comments.userID = users.iduser 
                            WHERE comments.postID = :postID 
                            ORDER BY comments.createdAT ASC");
    $stmt->bindParam(':postID', $postID);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($comments as $comment) {
        echo "<div class='comment-item'><strong>" . htmlspecialchars($comment['username']) . ":</strong> " . htmlspecialchars($comment['comment']) . "</div>";
    }
    exit();
}
?>
