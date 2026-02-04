<?php
    header('Content-Type: application/json');
    session_start();

    $usuario = $_POST['usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    $usuario_valido = 'admin';
    $contrasena_valida = 'admin'
    
    if($usuario === $usuario_valido && $contrasena === $contrasena_valida){
        $_SESSION['loggedin'] = true;
        echo json_encode(['success'=> true, 'message' => 'Login exitoso']);
    } else {
        http_response_code(401);
        echo json_encode(['success'=> false, 'message' => 'Usuario o contraseña incorrectos']);
    }
?>