<?php
echo("entra update_profile");
session_start();
require 'controlDB.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "No hay sesión activa"]);
    exit();
}

$conn = getDBConnection();
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Datos no válidos"]);
    exit();
}

$username = $_SESSION['user'];
$nombre = $data['nombre'] ?? '';
$apellido = $data['apellido'] ?? '';
$edad = $data['edad'] ?? '';
$descripcion = $data['descripcion'] ?? '';
$ubicacion = $data['ubicacion'] ?? '';

$query = "UPDATE users SET userFirstName = :nombre, userLastName = :apellido, edad = :edad, descripcion = :descripcion, ubicacion = :ubicacion WHERE username = :username";
$stmt = $conn->prepare($query);
$stmt->bindParam(":nombre", $nombre);
$stmt->bindParam(":apellido", $apellido);
$stmt->bindParam(":edad", $edad);
$stmt->bindParam(":descripcion", $descripcion);
$stmt->bindParam(":ubicacion", $ubicacion);
$stmt->bindParam(":username", $username);

if ($stmt->execute()) {
    echo("pone los datos");
    $_SESSION['userFirstName'] = $nombre;
    $_SESSION['userLastName'] = $apellido;
    $_SESSION['edad'] = $edad;
    $_SESSION['descripcion'] = $descripcion;
    $_SESSION['ubicacion'] = $ubicacion;
    
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar"]);
}
?>
