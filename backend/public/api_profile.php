<?php
// backend/public/api_profile.php
session_start();
header('Content-Type: application/json');
require 'db.php';

// 1. Verificación de sesión
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No has iniciado sesión']);
    exit;
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

// 2. OBTENER DATOS (GET)
if ($method === 'GET') {
    // TRUCO: Pedimos 'name' pero le decimos que nos lo devuelva con la etiqueta 'nombre'
    // para que coincida con lo que espera tu JavaScript.
    $sql = "SELECT name as nombre, apellido, email, telefono FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($usuario);
    exit;
}

// 3. GUARDAR DATOS (POST/PUT)
if ($method === 'PUT' || $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $nombre = $input['nombre'];
    $apellido = $input['apellido'];
    $telefono = $input['telefono'];

    // Actualizamos la tabla 'users' y la columna 'name'
    $sql = "UPDATE users SET name = ?, apellido = ?, telefono = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$nombre, $apellido, $telefono, $user_id])) {
        $_SESSION['user_nombre'] = $nombre; // Actualizamos la sesión
        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al guardar']);
    }
    exit;
}
?>