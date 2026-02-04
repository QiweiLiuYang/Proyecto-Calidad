<?php
    header('Content-Type: application/json');
    session_start();

    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
?>