<?php
function getConnection() {
    static $pdo = null;

    if ($pdo === null) {
        $host = '127.0.0.1';
        $db   = 'chats';
        $user = 'root';
        $pass = 'root';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
            //echo "Conexión exitosa a la base de datos.";
        } catch (PDOException $e) {
            echo $e;
            die('Error de conexión a la base de datos');
        }
    }

    return $pdo;

}

?>
