<?php
require_once './controlDB.php';

header('Content-Type: application/json');

$response = ['success' => false, 'comments' => []];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['postID'])) {
    $postID = $_GET['postID'];

    try {
        $conn = getDBConnection();

        // Consulta para obtener comentarios con formato requerido
        $stmt = $conn->prepare("
            SELECT 
                DATE_FORMAT(c.createdAt, '%d/%m/%y') AS fecha,
                u.username AS usuario,
                c.commentario 
            FROM comments c
            JOIN users u ON c.userID = u.iduser
            WHERE c.postID = :postID
            ORDER BY c.createdAt ASC
        ");
        $stmt->execute([':postID' => $postID]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($comments) {
            $response['success'] = true;
            $response['comments'] = $comments;
        } else {
            $response['message'] = 'No hay comentarios';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Error en la base de datos: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
