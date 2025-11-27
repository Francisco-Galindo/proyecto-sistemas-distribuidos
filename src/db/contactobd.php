<?php
require_once __DIR__ . '/conexion.php';

// Función para buscar usuarios que NO sean contacto ni el usuario actual 
function buscarUsuariosNoContactos(string $busqueda, int $usuario_id): array {
    $pdo = getConnection();
    
    $sql = '
        SELECT id_usuario, usuario, fecha_creacion
        FROM usuario
        WHERE usuario LIKE :busqueda
          AND id_usuario != :usuario_id
          AND id_usuario NOT IN (
              SELECT contacto_id 
              FROM contacto 
              WHERE usuario_id = :usuario_id2
          )
        LIMIT 10
    ';
    
    $stmt = $pdo->prepare($sql);

    $busqueda_param = "%$busqueda%";
    
    $stmt->execute([
        'busqueda'    => $busqueda_param,
        'usuario_id'  => $usuario_id,
        'usuario_id2' => $usuario_id
    ]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para guardar un mensaje
function enviarMensaje(int $de_usuario_id, int $para_usuario_id, string $mensaje_texto): bool {
    $pdo = getConnection();
    
    $sql = 'INSERT INTO mensaje (de_usuario_id, para_usuario_id, mensaje, fecha_creacion) 
            VALUES (:de_usuario_id, :para_usuario_id, :mensaje, NOW())';
    
    $stmt = $pdo->prepare($sql);
    
    return $stmt->execute([
        'de_usuario_id' => $de_usuario_id,
        'para_usuario_id' => $para_usuario_id,
        'mensaje' => $mensaje_texto
    ]);
}

// Función para obtener mensajes entre dos usuarios
function obtenerMensajes(int $usuario_id, int $contacto_id): array {
    $pdo = getConnection();
    
    $sql = '
        SELECT 
            m.id_mensaje,
            m.de_usuario_id,
            m.para_usuario_id,
            m.mensaje,
            m.fecha_creacion,
            m.leido,
            u.usuario as nombre_remitente
        FROM mensaje m
        JOIN usuario u ON m.de_usuario_id = u.id_usuario
        WHERE 
            (m.de_usuario_id = :usuario_id AND m.para_usuario_id = :contacto_id) 
            OR 
            (m.de_usuario_id = :contacto_id2 AND m.para_usuario_id = :usuario_id2)
        ORDER BY m.fecha_creacion ASC
    ';
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        'usuario_id' => $usuario_id,
        'contacto_id' => $contacto_id,
        'contacto_id2' => $contacto_id,
        'usuario_id2' => $usuario_id
    ]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para marcar mensajes como leídos
function marcarMensajesLeidos(int $usuario_id, int $remitente_id): bool {
    $pdo = getConnection();
    
    $sql = 'UPDATE mensaje 
            SET leido = 1 
            WHERE para_usuario_id = :usuario_id 
            AND de_usuario_id = :remitente_id 
            AND leido = 0';
    
    $stmt = $pdo->prepare($sql);
    
    return $stmt->execute([
        'usuario_id' => $usuario_id,
        'remitente_id' => $remitente_id
    ]);
}