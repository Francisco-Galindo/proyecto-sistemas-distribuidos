<?php
require_once __DIR__. '/../db/contactobd.php';
session_start();

header('Content-Type: application/json');

try {
    if(!isset($_SESSION['id_usuario'])){
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }
    
    $id_contacto = intval($_POST['id_contacto'] ?? 0);
    $usuario_id = $_SESSION['id_usuario'];

    if($id_contacto <= 0) {
        echo json_encode(['error' => 'ID de contacto inválido']);
        exit;
    }

    $mensajes = obtenerMensajes($usuario_id, $id_contacto);
    
    // Marcar mensajes como leídos
    marcarMensajesLeidos($usuario_id, $id_contacto);
    
    echo json_encode([
        'success' => true,
        'mensajes' => $mensajes
    ]);
    
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>