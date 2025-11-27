<?php
require_once __DIR__. '/../db/contactobd.php';
session_start();

// Para ver errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    if(!isset($_SESSION['id_usuario'])){
        echo json_encode(['error' => 'No autenticado', 'session' => $_SESSION]);
        exit;
    }
    
    $termino = trim($_POST['termino'] ?? '');
    $usuario_id = $_SESSION['id_usuario'];

    $resultados = buscarUsuariosNoContactos($termino, $usuario_id);

    echo json_encode([
        'success' => true,
        'resultados' => $resultados,
        'termino' => $termino
    ]);
} catch(Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>