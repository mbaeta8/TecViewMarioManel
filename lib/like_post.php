<?php
session_start();
require '../lib/controlDB.php';

if (!isset($_SESSION['user']) || !isset($_POST['postID'])) {
    echo json_encode(["status" => "error"]);
    exit();
}

$conn = getDBConnection();
$postID = intval($_POST['postID']);

// Actualizar likes
$query = "UPDATE posts SET likes = likes + 1 WHERE idpost = :postID";
$stmt = $conn->prepare($query);
$stmt->bindParam(':postID', $postID);
$stmt->execute();

// Obtener likes actualizados
$queryLikes = "SELECT likes FROM posts WHERE idpost = :postID";
$stmtLikes = $conn->prepare($queryLikes);
$stmtLikes->bindParam(':postID', $postID);
$stmtLikes->execute();
$row = $stmtLikes->fetch(PDO::FETCH_ASSOC);

echo json_encode(["status" => "success", "likes" => $row['likes']]);
?>
