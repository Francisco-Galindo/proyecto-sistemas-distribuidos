<?php
// Inicializamos variables para cada tipo de alerta
$mensaje_exito     = '';
$mensaje_error     = '';
$mensaje_info      = '';
$mensaje_warning   = '';
$mensaje_question  = '';

// Leemos las alertas que se hayan guardado con la función alerta()
if (isset($_SESSION['alerts']) && is_array($_SESSION['alerts'])) {
    foreach ($_SESSION['alerts'] as $alert) {
        if (!isset($alert['type'], $alert['message'])) {
            continue;
        }

        // Primer success que encontremos
        if ($alert['type'] === 'success' && $mensaje_exito === '') {
            $mensaje_exito = $alert['message'];
        }

        // Primer error que encontremos
        if ($alert['type'] === 'error' && $mensaje_error === '') {
            $mensaje_error = $alert['message'];
        }

        // Primer info que encontremos
        if ($alert['type'] === 'info' && $mensaje_info === '') {
            $mensaje_info = $alert['message'];
        }

        // Primer warning que encontremos
        if ($alert['type'] === 'warning' && $mensaje_warning === '') {
            $mensaje_warning = $alert['message'];
        }

        // Primer question que encontremos
        if ($alert['type'] === 'question' && $mensaje_question === '') {
            $mensaje_question = $alert['message'];
        }
    }
}

// Limpiamos para que solo se muestren una vez
unset($_SESSION['alerts']);
?>

<!-- para las alertas utilizamos SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const mensajeExito    = <?= json_encode($mensaje_exito) ?>;
    const mensajeError    = <?= json_encode($mensaje_error) ?>;
    const mensajeInfo     = <?= json_encode($mensaje_info) ?>;
    const mensajeWarning  = <?= json_encode($mensaje_warning) ?>;
    const mensajeQuestion = <?= json_encode($mensaje_question) ?>;

    if (mensajeExito) {
        Swal.fire({
            icon: 'success',
            title: '¡Listo!',
            text: mensajeExito,
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#3085d6'
        });
    }

    if (mensajeError) {
        Swal.fire({
            icon: 'error',
            title: 'Ups...',
            text: mensajeError,
            confirmButtonText: 'Intentar de nuevo',
            confirmButtonColor: '#d33'
        });
    }

    if (mensajeInfo) {
        Swal.fire({
            icon: 'info',
            title: 'Información',
            text: mensajeInfo,
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#17a2b8'
        });
    }

    if (mensajeWarning) {
        Swal.fire({
            icon: 'warning',
            title: 'Cuidado',
            text: mensajeWarning,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#f0ad4e'
        });
    }

    if (mensajeQuestion) {
        Swal.fire({
            icon: 'question',
            title: 'Pregunta',
            text: mensajeQuestion,
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#6c757d'
        });
    }
</script>
