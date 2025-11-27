<?php
require_once __DIR__. '/../db/contactobd.php';
session_start();

header('Content-Type: application/json');

try {
    if(!isset($_SESSION['id_usuario'])){
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }
    
    $para_usuario_id = intval($_POST['id_destinatario'] ?? 0);
    $mensaje = trim($_POST['mensaje'] ?? '');
    $de_usuario_id = $_SESSION['id_usuario'];

    if($para_usuario_id <= 0 || empty($mensaje)) {
        echo json_encode(['error' => 'Datos inválidos']);
        exit;
    }

    $resultado = enviarMensaje($de_usuario_id, $para_usuario_id, $mensaje);
    
    if($resultado) {
        echo json_encode([
            'success' => true,
            'mensaje' => 'Mensaje enviado',
            'fecha' => date('Y-m-d H:i:s')
        ]);
    } else {
        echo json_encode(['error' => 'Error al enviar mensaje']);
    }
    
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>