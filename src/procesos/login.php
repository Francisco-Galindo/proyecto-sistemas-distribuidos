<?php
session_start();
require_once __DIR__ . '/../db/usuariobd.php';
require_once __DIR__ . '/../includes/agregar_alerta.php';
// Evita errores si se entra por GET o sin datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario    = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    
    $usuario_correcto=obtenerUsuarioPorNombre($usuario);

    if (!$usuario_correcto || !password_verify($contrasena, $usuario_correcto['contrasena'])) {
            header('Location: ../index.php');
            alerta('error',"Usuario o contraseña incorrectos.");
            exit;
    }
     if ($usuario_correcto && password_verify($contrasena, $usuario_correcto['contrasena'])) {
        // Login correcto
        $_SESSION['id_usuario'] = $usuario_correcto['id_usuario'];
        $_SESSION['usuario']    = $usuario_correcto['usuario'];
        header('Location: ../chat.php');
        alerta('success',"Inicio de sesion correcto.");
        exit;

    }
    
} else {
    // Si alguien entra directo a login.php,regresa al formulario
    header('Location: ../index.php'); 
    exit;
}
?>
