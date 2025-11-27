<?php
// logout.php
session_start();
// Borrar todas las variables de sesión
$_SESSION = [];
//Destruir la sesión
session_destroy();
//Redirigir al login con un indicador de logout
header('Location: index.php?logout=1');
exit;
