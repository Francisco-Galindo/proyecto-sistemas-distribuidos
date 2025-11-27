<?php
session_start();
require_once __DIR__ . '/../db/usuariobd.php';
require_once __DIR__ . '/../includes/agregar_alerta.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['id_usuario'];
    $nuevo_usuario = $_POST['usuario'];
    $nueva_contrasena = !empty($_POST['contraseña']) ? $_POST['contraseña'] : null;
    if (empty($nuevo_usuario) && empty($nueva_contrasena)) {
        alerta('error', 'Los campos no pueden estar vacíos.');
        header('Location: ../perfil.php');
        exit;
    } else {
        $usuario_existente = obtenerUsuarioPorNombre($nuevo_usuario);
        if ($usuario_existente && $usuario_existente['id_usuario'] != $id_usuario) {
            alerta('error', 'El nombre de usuario ya está en uso.');
            header('Location: ../perfil.php');
            exit;
        } else {
            $actualizacion_exitosa = actualizarUsuario($id_usuario, $nuevo_usuario, $nueva_contrasena);

            if ($actualizacion_exitosa) {
                $_SESSION['usuario'] = $nuevo_usuario;
                alerta('success', 'Perfil actualizado correctamente.');
            } else {
                alerta('error', 'Error al actualizar el perfil.');
            }

            header('Location: ../perfil.php');
            exit;
        }
    }
} else {
    header('Location: ../perfil.php');
    exit;
}
