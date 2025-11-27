<?php
session_start();
require_once __DIR__ . '/../db/usuariobd.php';
require_once __DIR__ . '/../includes/agregar_alerta.php';
//verificar si se ha enviado el formulario
// Evita errores si se entra por GET o sin datos
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
$usuario=$_POST['usuario'];
$contrasena=$_POST['contraseña'];
$contrasena_confirmar=$_POST['contraseña_confirmar'];
//validar que las contraseñas coincidan 
    if($contrasena !== $contrasena_confirmar){
        header('Location: ../registro.php');
        alerta('error', "Las contraseñas no coinciden.");
        exit;
    }else{
        if(obtenerUsuarioPorNombre($usuario)){
            header('Location: ../registro.php');
             alerta('error',"El nombre de usuario ya existe. Por favor elige otro.");
            exit;
        }else{
             //crear el usuario
        $resultado=crearUsuario($usuario,$contrasena);
        if($resultado){
             header('Location: ../index.php');
             alerta('success',"Usuario creado exitosamente. Ahora puedes iniciar sesión.");
            
        }else{
             header('Location: ../registro.php');
             alerta('error',"Error al crear el usuario. Inténtalo de nuevo.");
        }

        }
       
    }

} else {
    // Si alguien entra directo a registrar.php,regresa al formulario
    header('Location: ../registro.php'); 
    exit;
}
?>
