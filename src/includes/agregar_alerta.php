<?php
function alerta(string $type, string $message):void{
    // type que se pueden usar en las alertas, cada una diferente: success, error, warning, info, question
    if (!isset($_SESSION['alerts']) || !is_array($_SESSION['alerts'])) {
        $_SESSION['alerts'] = [];
    }

     $_SESSION['alerts'][] = [
        'type'     => $type,
        'message'  => $message,
    ];

}

?>