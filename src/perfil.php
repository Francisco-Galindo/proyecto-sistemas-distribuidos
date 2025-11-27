<?php
session_start()
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
</head>

<body>
    <h1>Perfil de Usuario</h1>
    <p>Aquí puedes ver y editar tu perfil.</p>
    <p>Usuario: <?php echo htmlspecialchars($_SESSION['usuario']); ?></p>
    <h2>Actualiza tu información</h2>
    <form action="procesos/actualizar_usuario.php" method="POST">
        <label for="usuario">Nuevo nombre de usuario:</label>
        <input type="text" id="usuario" name="usuario"><br><br>

        <label for="contraseña">Nueva contraseña:</label>
        <input type="password" id="contraseña" name="contraseña"><br><br>

        <input type="submit" value="Editar_Perfil">
    </form>
    <a href="chat.php">Volver al Chat</a>
    <?php
    include 'includes/alertas.php'; ?>
</body>

</html>
