
<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Chat</title>
    <style>
        .conversaciones-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .conversacion-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.2s;
        }
        .conversacion-item:hover {
            background: #f5f5f5;
        }
        .conversacion-info {
            flex: 1;
        }
        .conversacion-nombre {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .conversacion-ultimo {
            color: #666;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 400px;
        }
        .badge-no-leidos {
            background: #25d366;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        .sin-conversaciones {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div style="text-align: center; padding: 20px;">
        <h1>👋 Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
        <h2>Bienvenido al sistema de chats</h2>
    </div>

    <!-- Formulario de búsqueda -->
    <form id="formBusqueda" onsubmit="event.preventDefault(); buscarUsuarios();">
        <input type="text" id="terminoBusqueda" name="termino" placeholder="Buscar usuarios..." required>
        <button type="submit">🔍 Buscar</button>
    </form>

    <!-- Contenedor para resultados de búsqueda -->
    <div id="resultadosBusqueda" style="display:none;">
        <h2>Resultados de la búsqueda:</h2>
        <div id="listaResultados"></div>
    </div>
    <a href="perfil.php">Perfil</a>
    <!-- Lista de conversaciones activas -->
    <div class="conversaciones-container">
        <h3 style="padding: 15px; margin: 0; border-bottom: 2px solid #f0f0f0;">
            💬 Conversaciones Activas
        </h3>
        <div id="listaConversaciones"></div>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="logout.php" style="color: #25d366; text-decoration: none; font-weight: 500;">
            🚪 Cerrar sesión
        </a>
    </div>

    <?php include 'includes/alertas.php'; ?>
    
    <script src="js/app.js"></script>
</body>
</html>
