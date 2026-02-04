<?php
    header('Content-Type: application/json');
    session_start();

    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    $usuario_valido = 'admin';
    $contrasena_valida = 'admin';
    
    if($usuario === $usuario_valido && $contrasena === $contrasena_valida){
        $_SESSION['loggedin'] = true;
        echo json_encode(['success'=> $_SESSION['loggedin']]);
    } else {
        http_response_code(401);
    }
?>