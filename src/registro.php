<?php 
session_start();
include 'includes/alertas.php'; ?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
    <h2>Registro</h2>
    <form action="procesos/registrar.php" method="POST">
        <label for="usuario">Usuario:</label>
        <input type="text" id="usuario" name="usuario" required><br><br>
        
        <label for="contraseña">Contraseña:</label>
        <div class="input-group">
        <input type="password" id="contraseña" name="contraseña" required>
        <button type="button" class="toggle-pw" onclick="togglePassword('contraseña', this)">Mostrar</button>
        </div>
        
        <label for="contraseña_confirmar">Confirmar Contraseña:</label>
        <div class="input-group">
        <input type="password" id="contraseña_confirmar" name="contraseña_confirmar" required>
        <button type="button" class="toggle-pw" onclick="togglePassword('contraseña_confirmar', this)">Mostrar</button>
        </div>
        
        <input type="submit" value="Registrarse">
    </form>
    <a href="index.php">Volver al Login</a>
  </div>
  <script src="js/app.js"></script>
</body>
</html>
