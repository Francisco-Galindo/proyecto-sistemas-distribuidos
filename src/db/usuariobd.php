<?php
require_once __DIR__ . '/conexion.php';

function crearUsuario($usuario, $contrasenaPlano)
{
    $pdo = getConnection();

    // Hash de contraseña
    $contrasenaHash = password_hash($contrasenaPlano, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO usuario (usuario,contrasena) VALUES (:usuario, :contrasena)');
    return $stmt->execute([
        'usuario'  => $usuario,
        'contrasena' => $contrasenaHash
    ]);
}
//funcion para verificar si el usuario existe
function obtenerUsuarioPorNombre($usuario)
{
    $pdo = getConnection();

    $stmt = $pdo->prepare('SELECT * FROM usuario WHERE usuario = :usuario');
    $stmt->execute(['usuario' => $usuario]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

//funcion para obtener usuario por id
function obtenerUsuarioPorId($id)
{
    $pdo = getConnection();

    $stmt = $pdo->prepare('SELECT * FROM usuario WHERE id = :id');
    $stmt->execute(['id_usuario' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

//funcion para actualizar los datos del usuario
function actualizarUsuario($id_usuario, $nuevo_usuario, $nueva_contrasena = null)
{
    $pdo = getConnection();

    if ($nueva_contrasena) {
        $contrasenaHash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE usuario SET usuario = :usuario, contrasena = :contrasena WHERE id_usuario = :id_usuario');
        return $stmt->execute([
            'usuario' => $nuevo_usuario,
            'contrasena' => $contrasenaHash,
            'id_usuario' => $id_usuario
        ]);
    } else {
        $stmt = $pdo->prepare('UPDATE usuario SET usuario = :usuario WHERE id_usuario = :id_usuario');
        return $stmt->execute([
            'usuario' => $nuevo_usuario,
            'id_usuario' => $id_usuario
        ]);
    }
}
