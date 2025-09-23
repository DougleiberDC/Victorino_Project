<?php
function limpiar($dato) {
    global $mysqli;
    return $mysqli->real_escape_string(htmlspecialchars($dato));
}

function formatearFecha($fecha) {
    return date("d/m/Y", strtotime($fecha));
}
?>