<?php
require_once __DIR__. '/../db/contactobd.php';
session_start();

header('Content-Type: application/json');

try {
    if(!isset($_SESSION['id_usuario'])){
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }
    
    $usuario_id = $_SESSION['id_usuario'];
    
    $pdo = getConnection();
    
    // Obtener todas las conversaciones del usuario
    $sql = '
        SELECT 
            u.id_usuario,
            u.usuario,
            (SELECT m.mensaje 
             FROM mensaje m 
             WHERE (m.de_usuario_id = u.id_usuario AND m.para_usuario_id = :usuario_id)
                OR (m.de_usuario_id = :usuario_id2 AND m.para_usuario_id = u.id_usuario)
             ORDER BY m.fecha_creacion DESC 
             LIMIT 1) as ultimo_mensaje,
            (SELECT m.fecha_creacion 
             FROM mensaje m 
             WHERE (m.de_usuario_id = u.id_usuario AND m.para_usuario_id = :usuario_id3)
                OR (m.de_usuario_id = :usuario_id4 AND m.para_usuario_id = u.id_usuario)
             ORDER BY m.fecha_creacion DESC 
             LIMIT 1) as fecha_ultimo_mensaje,
            (SELECT COUNT(*) 
             FROM mensaje m 
             WHERE m.de_usuario_id = u.id_usuario 
                AND m.para_usuario_id = :usuario_id5
                AND m.leido = 0) as mensajes_sin_leer
        FROM usuario u
        WHERE u.id_usuario != :usuario_id6
            AND EXISTS (
                SELECT 1 FROM mensaje m 
                WHERE (m.de_usuario_id = u.id_usuario AND m.para_usuario_id = :usuario_id7)
                   OR (m.de_usuario_id = :usuario_id8 AND m.para_usuario_id = u.id_usuario)
            )
        ORDER BY fecha_ultimo_mensaje DESC
    ';
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'usuario_id' => $usuario_id,
        'usuario_id2' => $usuario_id,
        'usuario_id3' => $usuario_id,
        'usuario_id4' => $usuario_id,
        'usuario_id5' => $usuario_id,
        'usuario_id6' => $usuario_id,
        'usuario_id7' => $usuario_id,
        'usuario_id8' => $usuario_id
    ]);
    
    $conversaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'conversaciones' => $conversaciones
    ]);
    
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>