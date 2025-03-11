<?php
session_start();
require '../lib/controlDB.php';

if (!isset($_SESSION['user']) || !isset($_POST['postID']) || !isset($_POST['comment'])) {
    echo json_encode(["status" => "error"]);
    exit();
}

$conn = getDBConnection();
$user = $_SESSION['user'];
$postID = intval($_POST['postID']);
$comment = trim($_POST['comment']);

// Obtener ID del usuario
$queryUser = "SELECT iduser FROM users WHERE username = :user";
$stmtUser = $conn->prepare($queryUser);
$stmtUser->bindParam(':user', $user);
$stmtUser->execute();
$rowUser = $stmtUser->fetch(PDO::FETCH_ASSOC);
$userID = $rowUser['iduser'];

// Insertar comentario
$query = "INSERT INTO comments (postID, userID, comment) VALUES (:postID, :userID, :comment)";
$stmt = $conn->prepare($query);
$stmt->bindParam(':postID', $postID);
$stmt->bindParam(':userID', $userID);
$stmt->bindParam(':comment', $comment);
$stmt->execute();

echo json_encode(["status" => "success", "comment" => htmlspecialchars($comment)]);
?>
