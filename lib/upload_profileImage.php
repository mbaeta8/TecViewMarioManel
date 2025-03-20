<?php
session_start();
require 'controlDB.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "error" => "Unauthorized"]);
    exit();
}

$conn = getDBConnection();
$user = $_SESSION['user'];

$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['image'])) {
    $imageData = $data['image'];
    
    // Remove metadata (e.g., "data:image/png;base64,")
    list(, $imageData) = explode(',', $imageData);
    $imageData = base64_decode($imageData);

    // Store image in database
    $query = "UPDATE users SET foto_perfil = :foto WHERE username = :user";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':foto', $data['image']);
    $stmt->bindParam(':user', $user);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Database error"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "No image received"]);
}
?>